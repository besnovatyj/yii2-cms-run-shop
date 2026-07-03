<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\entities\product;

use Besnovatyj\Images\base\BaseImage;

/**
 * Фотография товара (управляется модулем yii2-cms-images).
 *
 * @property int    $id
 * @property int    $product_id
 * @property string $file
 * @property int    $sort
 */
class Photo extends BaseImage
{
    /**
     * {@inheritdoc}
     */
    protected static function getParentAttribute(): string
    {
        return 'product_id';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getStorageName(): string
    {
        return 'RunShop';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getThumbProfiles(): array
    {
        return [
            // backend
            'admin'                      => ['width' => 100, 'height' => 70],
            'thumb'                      => ['width' => 640, 'height' => 480],
            // frontend
            'cart_list'                  => ['width' => 150, 'height' => 150],
            'cart_widget_list'           => ['width' => 57,  'height' => 57],
            'catalog_list'               => ['width' => 228, 'height' => 228],
            'catalog_origin'             => ['width' => 1200, 'height' => 1600],
            'catalog_product_additional' => ['width' => 66,  'height' => 66],
            'catalog_product_main'       => ['width' => 750, 'height' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%run_shop_photos}}';
    }
}
