<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\listeners\product;

use Besnovatyj\RunShop\repositories\events\ProductRemoved;
use Besnovatyj\RunShop\services\search\ProductIndexer;
use yii\caching\Cache;
use yii\caching\TagDependency;

class ProductSearchRemoveListener
{
    private $indexer;
    private $cache;

    public function __construct(ProductIndexer $indexer, Cache $cache)
    {
        $this->indexer = $indexer;
        $this->cache = $cache;
    }

    public function handle(ProductRemoved $event): void
    {
        // ВНИМАНИЕ: getProduct() перезагружает товар из БД и при жёстком удалении
        // бросит исключение (строки уже нет). Заработает после перехода на
        // мягкое удаление; слушатель пока в резерве (не подключён в Bootstrap).
        $product = $event->getProduct();
        $this->indexer->remove($product);
        TagDependency::invalidate($this->cache, ['products']);
    }
}
