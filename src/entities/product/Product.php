<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\product;

use DomainException;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\DomainEvents\AggregateRoot;
use Besnovatyj\Meta\Meta;
use Besnovatyj\DomainEvents\EventTrait;
use Besnovatyj\PessimisticLock\PessimisticLockBehavior;
use Besnovatyj\RunShop\entities\Brand;
use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\entities\product\events\ProductAppearedInStock;
use Besnovatyj\RunShop\entities\product\queries\ProductQuery;
use Besnovatyj\RunShop\entities\Tag;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $created_at
 * @property string $code
 * @property string $name
 * @property string $description
 * @property int $category_id
 * @property int $brand_id
 * @property int $price_old
 * @property int $price_new
 * @property int $rating
 * @property int $main_photo_id
 * @property int $status
 * @property string $email_additional_text_html
 * @property string $email_additional_text_plain_text
 * @property string $mail_attach
 * @property int $weight
 * @property int $quantity
 *
 * @property Meta $meta
 * @property Brand $brand
 * @property Category $category
 * @property CategoryAssignment[] $categoryAssignments
 * @property Category[] $categories
 * @property TagAssignment[] $tagAssignments
 * @property Tag[] $tags
 * @property RelatedAssignment[] $relatedAssignments
 * @property Modification[] $modifications
 * @property Value[] $values
 * @property Photo[] $photos
 * @property Photo $mainPhoto
 * @property Review[] $reviews
 */
class Product extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;

    public $meta;

    public static function create(int $brandId, int $categoryId, string $code, string $name, string $description, $weight, string $email_additional_text_html, string $email_additional_text_plain_text, string $mail_attach, int $quantity, Meta $meta): self
    {
        $product = new static();
        $product->brand_id = $brandId;
        $product->category_id = $categoryId;
        $product->code = $code;
        $product->name = $name;
        $product->description = $description;
        $product->weight = $weight;
        $product->email_additional_text_html = $email_additional_text_html;
        $product->email_additional_text_plain_text = $email_additional_text_plain_text;
        $product->mail_attach = $mail_attach;
        $product->quantity = $quantity;
        $product->meta = $meta;
        $product->status = self::STATUS_DRAFT;
        $product->created_at = time();
        return $product;
    }

    public function edit(int $brandId, string $code, string $name, string $description, $weight, string $email_additional_text_html, string $email_additional_text_plain_text, string $mail_attach, Meta $meta): void
    {
        $this->brand_id = $brandId;
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->weight = $weight;
        $this->email_additional_text_html = $email_additional_text_html;
        $this->email_additional_text_plain_text = $email_additional_text_plain_text;
        $this->mail_attach = $mail_attach;
        $this->meta = $meta;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    public function setPrice(int $new, int $old): void
    {
        $this->price_new = $new;
        $this->price_old = $old;
    }

    public function changeQuantity($quantity): void
    {
        if ($this->modifications) {
            throw new DomainException('Change modifications quantity.');
        }
        $this->setQuantity($quantity);
    }

    private function setQuantity(int $quantity): void
    {
        if ($this->quantity == 0 && $quantity > 0) {
            $this->recordEvent(new ProductAppearedInStock($this)); // TODO: Вынос в сервис `$entity->recordEvent()`. Там после этого `$repo->save();`
        }
        $this->quantity = $quantity;
    }

    public function changeMainCategory(int $categoryId): void
    {
        $this->category_id = $categoryId;
    }

    /**
     * Устанавливает главную фотографию (управляется модулем изображений).
     */
    public function setMainPhoto(?int $photoId): void
    {
        $this->main_photo_id = $photoId;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Product is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    public function draft(): void
    {
        if ($this->isDraft()) {
            throw new DomainException('Product is already draft.');
        }
        $this->status = self::STATUS_DRAFT;
    }

    public function isActive(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function isAvailable(): bool
    {
        return $this->quantity > 0;
    }

    public function canChangeQuantity(): bool
    {
        return !$this->modifications;
    }

    public function canBeCheckout($modificationId, $quantity): bool
    {
        if ($modificationId) {
            return $quantity <= $this->getModification($modificationId)->quantity;
        }
        return $quantity <= $this->quantity;
    }

    /**
     * Списывает количество при оформлении заказа.
     *
     * Мутирует модификацию/товар в памяти и пересчитывает остаток. Персистентность дирти-модификации
     * и товара — на вызывающем сервисе (OrderService) в транзакции.
     */
    public function checkout($modificationId, $quantity): void
    {
        if ($modificationId) {
            foreach ($this->modifications as $modification) {
                if ($modification->isIdEqualTo($modificationId)) {
                    $modification->checkout($quantity);
                    $this->recalcQuantityFromModifications();
                    return;
                }
            }
        }
        if ($quantity > $this->quantity) {
            throw new DomainException('Only ' . $this->quantity . ' items are available.');
        }
        $this->setQuantity($this->quantity - $quantity);
    }

    /**
     * Пересчитывает остаток товара как сумму остатков модификаций.
     */
    public function recalcQuantityFromModifications(): void
    {
        $this->setQuantity(array_sum(array_map(
            static fn(Modification $modification) => $modification->quantity,
            $this->modifications,
        )));
    }

    /**
     * @param int $id - Id Характеристики
     * @return Value
     */
    public function getValue(int $id): Value
    {
        $values = $this->values;
        foreach ($values as $val) {
            if ($val->isForCharacteristic($id)) {
                return $val;
            }
        }
        return Value::blank($id);
    }

    // Modification

    public function getModification($id): Modification
    {
        foreach ($this->modifications as $modification) {
            if ($modification->isIdEqualTo($id)) {
                return $modification;
            }
        }
        throw new DomainException('Modification is not found.');
    }

    public function getModificationPrice($id): int
    {
        foreach ($this->modifications as $modification) {
            if ($modification->isIdEqualTo($id)) {
                return $modification->price ?: $this->price_new;
            }
        }
        throw new DomainException('Modification is not found.');
    }

    // Queries

    public function getBrand(): ActiveQuery
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getCategoryAssignments(): ActiveQuery
    {
        return $this->hasMany(CategoryAssignment::class, ['product_id' => 'id']);
    }

    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->via('categoryAssignments');
    }

    public function getTagAssignments(): ActiveQuery
    {
        return $this->hasMany(TagAssignment::class, ['product_id' => 'id']);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagAssignments');
    }

    public function getModifications(): ActiveQuery
    {
        return $this->hasMany(Modification::class, ['product_id' => 'id']);
    }

    public function getValues(): ActiveQuery
    {
        return $this->hasMany(Value::class, ['product_id' => 'id']);
    }

    public function getPhotos(): ActiveQuery
    {
        return $this->hasMany(Photo::class, ['product_id' => 'id'])->orderBy('sort');
    }

    public function getMainPhoto(): ActiveQuery
    {
        return $this->hasOne(Photo::class, ['id' => 'main_photo_id']);
    }

    public function getRelatedAssignments(): ActiveQuery
    {
        return $this->hasMany(RelatedAssignment::class, ['product_id' => 'id']);
    }

    public function getRelateds(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['id' => 'related_id'])->via('relatedAssignments');
    }

    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['product_id' => 'id']);
    }

    // Other

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            PessimisticLockBehavior::class,
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            foreach ($this->photos as $photo) {
                $photo->delete();
            }
            return true;
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Identifier',
            'name' => 'Name',
            'code' => 'Code',
            'description' => 'Description',
            'category.name' => 'Category',
            'price_old' => 'Old price',
            'price_new' => 'New price',
            'rating' => 'Rating',
            'status' => 'Status',
            'quantity' => 'Quantity',
        ];
    }

    public static function tableName(): string
    {
        return '{{%run_shop_products}}';
    }

    public static function find(): ProductQuery
    {
        return new ProductQuery(static::class);
    }
}
