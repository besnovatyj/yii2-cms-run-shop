<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\listeners\product;

use Besnovatyj\RunShop\repositories\events\ProductPersisted;
use Besnovatyj\RunShop\services\search\ProductIndexer;
use yii\caching\Cache;
use yii\caching\TagDependency;

class ProductSearchPersistListener
{
    private $indexer;
    private $cache;

    public function __construct(ProductIndexer $indexer, Cache $cache)
    {
        $this->indexer = $indexer;
        $this->cache = $cache;
    }

    public function handle(ProductPersisted $event): void
    {
        $product = $event->getProduct();
        if ($product->isActive()) {
            $this->indexer->index($product);
        } else {
            $this->indexer->remove($product);
        }
        TagDependency::invalidate($this->cache, ['products']);
    }
}
