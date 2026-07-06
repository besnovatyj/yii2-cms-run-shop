<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\repositories\events;

use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\DomainEvents\EntityEvent;

/**
 * Событие: категория удалена.
 *
 * Расширяет EntityEvent: в очередь сериализуется только id. ВНИМАНИЕ: при
 * жёстком удалении строки к моменту обработки в воркере findEntity() вернёт null
 * и getCategory() бросит исключение — потребитель, которому нужна сама сущность,
 * появится корректно только при переходе на мягкое удаление (soft-delete).
 */
class CategoryRemoved extends EntityEvent
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
