<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\widgets;

use Besnovatyj\RunShop\readModels\ProductReadRepository;
use yii\base\Widget;

class FeaturedProductsWidget extends Widget
{
    public   $limit;

    private ProductReadRepository $repository;

    public function __construct(ProductReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->repository = $repository;
    }

    public function run(): string
    {
        return $this->render('featured', [
            'products' => $this->repository->getFeatured($this->limit)
        ]);
    }
}
