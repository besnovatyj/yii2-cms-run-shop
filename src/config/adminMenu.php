<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

return [

    // Заказы
    [
        'label' => 'Заказы',
        'iconClass' => 'bi bi-bag-check me-1',
        'url' => ['/RunShop/backend/order/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/order');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Товары
    [
        'label' => 'Товары',
        'iconClass' => 'bi bi-box-seam me-1',
        'url' => ['/RunShop/backend/product/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/product');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Бренд
    [
        'label' => 'Бренд',
        'iconClass' => 'bi bi-bookmark-star me-1',
        'url' => ['/RunShop/backend/brand/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/brand');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Категории
    [
        'label' => 'Категории',
        'iconClass' => 'bi bi-list-ol me-1',
        'url' => ['/RunShop/backend/category/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/category');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Категории: изображения (не-древовидное поле — отдельно от TreeManager)
    [
        'label' => 'Категории: изображения',
        'iconClass' => 'bi bi-image me-1',
        'url' => ['/RunShop/backend/category-image/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/category-image');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Характеристики
    [
        'label' => 'Характеристики',
        'iconClass' => 'bi bi-sliders me-1',
        'url' => ['/RunShop/backend/characteristic/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/characteristic');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Теги
    [
        'label' => 'Теги',
        'iconClass' => 'bi bi-tags me-1',
        'url' => ['/RunShop/backend/tag/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/tag');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Скидки
    [
        'label' => 'Скидки',
        'iconClass' => 'bi bi-percent me-1',
        'url' => ['/RunShop/backend/discount/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'RunShop/backend/discount');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Магазин',
                    'groupIcon' => 'bi bi-shop',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

];
