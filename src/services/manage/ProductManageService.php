<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\services\manage;

use Besnovatyj\DomainEvents\TransactionManager;
use Besnovatyj\Meta\Meta;
use Besnovatyj\RunShop\entities\product\CategoryAssignment;
use Besnovatyj\RunShop\entities\product\Modification;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\entities\product\RelatedAssignment;
use Besnovatyj\RunShop\entities\product\TagAssignment;
use Besnovatyj\RunShop\entities\product\Value;
use Besnovatyj\RunShop\entities\Tag;
use Besnovatyj\RunShop\forms\backend\product\ModificationForm;
use Besnovatyj\RunShop\forms\backend\product\PriceForm;
use Besnovatyj\RunShop\forms\backend\product\ProductCreateForm;
use Besnovatyj\RunShop\forms\backend\product\ProductEditForm;
use Besnovatyj\RunShop\forms\backend\product\QuantityForm;
use Besnovatyj\RunShop\forms\backend\product\TagsForm;
use Besnovatyj\RunShop\repositories\BrandRepository;
use Besnovatyj\RunShop\repositories\CategoryRepository;
use Besnovatyj\RunShop\repositories\ProductRepository;
use Besnovatyj\RunShop\repositories\TagRepository;
use DomainException;

/**
 * Сервис управления товарами (CRUD + связи).
 *
 * Связи (доп. категории, значения характеристик, теги, модификации, сопутствующие товары)
 * сохраняются ЯВНО через AR внутри транзакции — без SaveRelationsBehavior.
 * Фотографии товара управляются модулем изображений (yii2-cms-images) через
 * {@see \Besnovatyj\RunShop\image\ProductImageOwner} и добавляются после создания товара.
 */
class ProductManageService
{
    public function __construct(
        private readonly ProductRepository  $products,
        private readonly BrandRepository    $brands,
        private readonly CategoryRepository $categories,
        private readonly TagRepository      $tags,
        private readonly TransactionManager $transaction,
    ) {}

    public function create(ProductCreateForm $form): Product
    {
        $brand    = $form->brandId ? $this->brands->get($form->brandId) : null;
        $category = $this->categories->get($form->categories->main);

        $product = Product::create(
            $brand?->id,
            $category->id,
            $form->code,
            $form->name,
            $form->description,
            $form->weight,
            $form->email_additional_text_html,
            $form->email_additional_text_plain_text,
            $form->mail_attach,
            $form->quantity->quantity,
            new Meta($form->meta->title, $form->meta->description, $form->meta->keywords),
        );
        $product->setPrice($form->price->new, $form->price->old);

        $this->transaction->wrap(function () use ($product, $form) {
            $this->products->save($product);

            foreach ($form->categories->others as $otherId) {
                $this->assignCategory($product, (int) $otherId);
            }
            foreach ($form->values as $value) {
                $this->saveValue($product->id, (int) $value->getId(), (string) $value->value);
            }
            $this->syncTags($product, $form->tags);
        });

        return $product;
    }

    public function edit(int $id, ProductEditForm $form): void
    {
        $product  = $this->products->get($id);
        $brand    = $form->brandId ? $this->brands->get($form->brandId) : null;
        $category = $this->categories->get($form->categories->main);

        $product->edit(
            $brand?->id,
            $form->code,
            $form->name,
            $form->description,
            $form->weight,
            $form->email_additional_text_html,
            $form->email_additional_text_plain_text,
            $form->mail_attach,
            new Meta($form->meta->title, $form->meta->description, $form->meta->keywords),
        );
        $product->changeMainCategory($category->id);

        $this->transaction->wrap(function () use ($product, $form) {
            $this->products->save($product);

            CategoryAssignment::deleteAll(['product_id' => $product->id]);
            foreach ($form->categories->others as $otherId) {
                $this->assignCategory($product, (int) $otherId);
            }
            foreach ($form->values as $value) {
                $this->saveValue($product->id, (int) $value->getId(), (string) $value->value);
            }
            $this->syncTags($product, $form->tags);
        });
    }

    public function changePrice(int $id, PriceForm $form): void
    {
        $product = $this->products->get($id);
        $product->setPrice($form->new, $form->old);
        $this->products->save($product);
    }

    public function changeQuantity(int $id, QuantityForm $form): void
    {
        $product = $this->products->get($id);
        $product->changeQuantity($form->quantity);
        $this->products->save($product);
    }

    public function activate(int $id): void
    {
        $product = $this->products->get($id);
        $product->activate();
        $this->products->save($product);
    }

    public function draft(int $id): void
    {
        $product = $this->products->get($id);
        $product->draft();
        $this->products->save($product);
    }

    public function addRelatedProduct(int $id, int $otherId): void
    {
        $product = $this->products->get($id);
        $this->products->get($otherId); // проверка существования

        $exists = RelatedAssignment::find()
            ->andWhere(['product_id' => $product->id, 'related_id' => $otherId])
            ->exists();

        if (!$exists) {
            $assignment = RelatedAssignment::create($otherId);
            $assignment->product_id = $product->id;
            $assignment->save();
        }
    }

    public function removeRelatedProduct(int $id, int $otherId): void
    {
        RelatedAssignment::deleteAll(['product_id' => $id, 'related_id' => $otherId]);
    }

    // ── Модификации ──────────────────────────────────────────────────────────

    public function addModification(int $id, ModificationForm $form): void
    {
        $product = $this->products->get($id);

        if (Modification::find()->andWhere(['product_id' => $product->id, 'code' => $form->code])->exists()) {
            throw new DomainException('Modification already exists.');
        }

        $this->transaction->wrap(function () use ($product, $form) {
            $modification = Modification::create($form->code, $form->name, $form->price, $form->quantity);
            $modification->product_id = $product->id;
            $modification->save();
            $this->syncProductQuantity($product);
        });
    }

    public function editModification(int $id, int $modificationId, ModificationForm $form): void
    {
        $product      = $this->products->get($id);
        $modification = $this->getModification($product->id, $modificationId);

        $this->transaction->wrap(function () use ($product, $modification, $form) {
            $modification->edit($form->code, $form->name, $form->price, $form->quantity);
            $modification->save();
            $this->syncProductQuantity($product);
        });
    }

    public function removeModification(int $id, int $modificationId): void
    {
        $product      = $this->products->get($id);
        $modification = $this->getModification($product->id, $modificationId);

        $this->transaction->wrap(function () use ($product, $modification) {
            $modification->delete();
            $this->syncProductQuantity($product);
        });
    }

    public function remove(int $id): void
    {
        $product = $this->products->get($id);

        $this->transaction->wrap(function () use ($product) {
            TagAssignment::deleteAll(['product_id' => $product->id]);
            CategoryAssignment::deleteAll(['product_id' => $product->id]);
            RelatedAssignment::deleteAll(['product_id' => $product->id]);
            RelatedAssignment::deleteAll(['related_id' => $product->id]);
            Value::deleteAll(['product_id' => $product->id]);
            Modification::deleteAll(['product_id' => $product->id]);

            $this->products->remove($product); // beforeDelete удалит фото
        });
    }

    // ── Приватные помощники ──────────────────────────────────────────────────

    private function getModification(int $productId, int $modificationId): Modification
    {
        $modification = Modification::findOne(['id' => $modificationId, 'product_id' => $productId]);
        if (!$modification) {
            throw new DomainException('Modification is not found.');
        }
        return $modification;
    }

    /**
     * Пересчитывает остаток товара как сумму остатков модификаций (после их изменения в БД).
     */
    private function syncProductQuantity(Product $product): void
    {
        $product->populateRelation('modifications', $product->getModifications()->all());
        $product->recalcQuantityFromModifications();
        $this->products->save($product);
    }

    private function assignCategory(Product $product, int $categoryId): void
    {
        $exists = CategoryAssignment::find()
            ->andWhere(['product_id' => $product->id, 'category_id' => $categoryId])
            ->exists();

        if (!$exists) {
            $assignment = CategoryAssignment::create($categoryId);
            $assignment->product_id = $product->id;
            $assignment->save();
        }
    }

    /**
     * Upsert значения характеристики. Пустая строка удаляет запись.
     */
    private function saveValue(int $productId, int $characteristicId, string $value): void
    {
        $existing = Value::findOne(['product_id' => $productId, 'characteristic_id' => $characteristicId]);
        if ($value === '') {
            $existing?->delete();
            return;
        }
        if ($existing) {
            $existing->change($value);
            $existing->save();
        } else {
            $new = Value::create($characteristicId, $value);
            $new->product_id = $productId;
            $new->save();
        }
    }

    /**
     * Синхронизирует теги товара (существующие по id + новые по именам).
     */
    private function syncTags(Product $product, TagsForm $tagsForm): void
    {
        TagAssignment::deleteAll(['product_id' => $product->id]);

        foreach ($tagsForm->existing as $tagId) {
            $this->tags->get((int) $tagId); // проверка существования
            $this->attachTag($product->id, (int) $tagId);
        }
        foreach ($tagsForm->getNewNames() as $tagName) {
            $tag = $this->tags->findByName($tagName);
            if (!$tag) {
                $tag = Tag::create($tagName, $tagName);
                $this->tags->save($tag);
            }
            $this->attachTag($product->id, $tag->id);
        }
    }

    private function attachTag(int $productId, int $tagId): void
    {
        $exists = TagAssignment::find()
            ->andWhere(['product_id' => $productId, 'tag_id' => $tagId])
            ->exists();
        if (!$exists) {
            $assignment = TagAssignment::create($tagId);
            $assignment->product_id = $productId;
            $assignment->save();
        }
    }
}
