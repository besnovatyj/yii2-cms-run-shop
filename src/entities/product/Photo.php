<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\product;

use Besnovatyj\Upload\heap\UploadBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string $file
 * @property int $sort
 *
 * @mixin UploadBehavior
 */
class Photo extends ActiveRecord
{
    public static function create(UploadedFile $file): self
    {
        $photo = new static();
        $photo->file = $file;
        return $photo;
    }

    public function setSort($sort): void
    {
        $this->sort = $sort;
    }

    public function isIdEqualTo($id): bool
    {
        return $this->id == $id;
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => UploadBehavior::class,
                'attribute' => 'file',
                'filePath' => '@static/origin/RunShop/products/[[attribute_product_id]]/[[id]].[[extension]]',
                'fileUrl' => '@staticHostInfo/origin/RunShop/products/[[attribute_product_id]]/[[id]].[[extension]]',
                'thumbPath' => '@static/cache/RunShop/products/[[attribute_product_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbUrl' => '@staticHostInfo/cache/RunShop/products/[[attribute_product_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbs' => [
                    // backend
                    'admin' => ['width' => 100, 'height' => 70],
                    'thumb' => ['width' => 640, 'height' => 480],
                    // frontend
                    'cart_list' => ['width' => 150, 'height' => 150],
                    'cart_widget_list' => ['width' => 150, 'height' => 150],
                    'product_page' => ['width' => 470, 'height' => 670],
                    '343x301' => ['width' => 343, 'height' => 301],
                    'front_widget' => ['width' => 800, 'height' => 800],
                ],
            ],
        ];
    }

    public static function tableName(): string
    {
        return '{{%run_shop_photos}}';
    }

}
