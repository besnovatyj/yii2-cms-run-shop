<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\listeners\category;

use Besnovatyj\RunShop\repositories\events\CategoryPersisted;
use yii\caching\Cache;
use yii\caching\TagDependency;

class CategoryPersistenceListener
{
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function handle(CategoryPersisted $event): void
    {
        // Событие типизировано под категорию — достаточно инвалидировать кеш,
        // сама сущность здесь не нужна (ленивая перезагрузка не запускается).
        TagDependency::invalidate($this->cache, ['categories']);
    }
}
