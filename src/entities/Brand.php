<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities;

use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\Meta\Meta;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name - Название бренда
 * @property string $slug
 * @property Meta $meta
 */
class Brand extends ActiveRecord
{
    public $meta;

    public static function create(string $name, string $slug, Meta $meta): self
    {
        $brand = new static();
        $brand->name = $name;
        $brand->slug = $slug;
        $brand->meta = $meta;
        return $brand;
    }

    public function edit(string $name, string $slug, Meta $meta): void
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->meta = $meta;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'Id',
            'name' => 'Brand',
//            'slug'=>'',
//            'meta'=>'',
        ];
    }

    public static function tableName(): string
    {
        return '{{%run_shop_brands}}';
    }
}
