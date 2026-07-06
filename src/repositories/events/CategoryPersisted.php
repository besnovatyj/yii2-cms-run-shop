<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\repositories\events;

use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\DomainEvents\EntityEvent;

/**
 * Событие: категория создана или обновлена.
 *
 * Расширяет EntityEvent: в очередь сериализуется только id категории, а в воркере
 * Category лениво загружается из БД через findEntity() (свежая, полностью
 * сохранённая сущность). Потребители должны читать сущность через getCategory().
 */
class CategoryPersisted extends EntityEvent
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function getCategory(): Category
    {
        /** @var Category $category */
        $category = $this->getEntity();
        return $category;
    }

    protected function findEntity(int $id): ?Category
    {
        return Category::findOne($id);
    }
}
