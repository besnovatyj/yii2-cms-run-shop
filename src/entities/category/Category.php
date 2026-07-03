<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\entities\category;

use Besnovatyj\Meta\Meta;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\TreeManager\Manager\entities\Node;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use Besnovatyj\Upload\heap\UploadBehavior;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

/**
 * Категория товаров (иерархическая, NestedSets через TreeManager).
 *
 * @property int         $id
 * @property int         $tree
 * @property int         $lft
 * @property int         $rgt
 * @property int         $depth
 * @property string      $name
 * @property string      $slug
 * @property string|null $description
 * @property string      $photo
 * @property int         $status
 * @property int         $sort_order  Сортировка корневых узлов
 *
 * @property Meta        $meta
 *
 * @mixin MetaBehavior
 */
class Category extends Node
{
    public Meta $meta;

    public static function create(string $name, string $slug, ?string $description, Meta $meta): self
    {
        $category              = new static();
        $category->name        = $name;
        $category->slug        = $slug;
        $category->description = $description;
        $category->meta        = $meta;
        return $category;
    }

    public function edit(string $name, string $slug, ?string $description, Meta $meta): void
    {
        $this->name        = $name;
        $this->slug        = $slug;
        $this->description = $description;
        $this->meta        = $meta;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    /**
     * Переключает статус отображения категории.
     */
    public function changeStatus(): void
    {
        $this->status = ((int) $this->status) === 1 ? 0 : 1;
    }

    public function countProductsByMainCategory(): bool|int|string|null
    {
        return $this->getProducts()->count();
    }

    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['category_id' => 'id']);
    }

    public function getActiveProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['category_id' => 'id'])
            ->onCondition(['status' => Product::STATUS_ACTIVE]);
    }

    /**
     * Предки категории (путь от корня, без самой категории) — навигация дерева через TreeManager.
     *
     * @return Category[]
     */
    public function getParentsList(): array
    {
        return (new TreeQueryScope(self::class))->parentsQuery($this)->all();
    }

    /**
     * Прямые потомки категории — навигация дерева через TreeManager.
     *
     * @return Category[]
     */
    public function getChildrenList(): array
    {
        return (new TreeQueryScope(self::class))->childrenQuery($this)->all();
    }

    public function setPhoto(UploadedFile $photo): void
    {
        $this->photo = $photo;
    }

    public function removePhoto(): void
    {
        $this->photo = '';
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            [
                'class'     => UploadBehavior::class,
                'attribute' => 'photo',
                'filePath'  => '@static/origin/RunShop/categories/[[id]].[[extension]]',
                'fileUrl'   => '@staticHostInfo/origin/RunShop/categories/[[id]].[[extension]]',
                'thumbPath' => '@static/cache/RunShop/categories/[[profile]]_[[id]].[[extension]]',
                'thumbUrl'  => '@staticHostInfo/cache/RunShop/categories/[[profile]]_[[id]].[[extension]]',
                'thumbs'    => [
                    'admin'        => ['width' => 100, 'height' => 57],
                    'front_widget' => ['width' => 1200, 'height' => 1200],
                ],
            ],
            ...parent::behaviors(),
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'          => 'Идентификатор',
            'name'        => 'Название',
            'slug'        => 'Уникальное название',
            'description' => 'Описание',
        ];
    }

    public static function tableName(): string
    {
        return '{{%run_shop_categories}}';
    }
}
