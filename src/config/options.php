<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

// Все опции должны быть определены при установке (подключении) модуля в приложение
return [
    'run_shop_catalog_title' => [
        'path' => 'modules.RunShop.params.catalog_title',
        'label' => '[RunShop] Catalog title',
        'rules' => [
            ['required']
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'run_shop_catalog_meta_description' => [
        'path' => 'modules.RunShop.params.catalog_meta_description',
        'label' => '[RunShop] Catalog meta description',
        'rules' => [
            ['required']
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'run_shop_catalog_meta_keywords' => [
        'path' => 'modules.RunShop.params.catalog_meta_keywords',
        'label' => '[RunShop] Catalog meta keywords',
        'rules' => [
            ['required']
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
];
