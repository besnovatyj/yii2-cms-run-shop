<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\repositories;

use Besnovatyj\DomainEvents\dispatchers\EventDispatcher;
use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\repositories\events\EntityPersisted;
use Besnovatyj\RunShop\repositories\events\EntityRemoved;
use RuntimeException;

class CategoryRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function get(int $id): Category
    {
        if (!$category = Category::findOne($id)) {
            throw new NotFoundException('Category is not found.');
        }
        return $category;
    }

    public function save(Category $category): void
    {
        if (!$category->save()) {
            throw new RuntimeException('Saving error.');
        }
        $this->dispatcher->dispatch(new EntityPersisted($category));
    }

    public function remove(Category $category): void
    {
        if (!$category->delete()) {
            throw new RuntimeException('Removing error.');
        }
        $this->dispatcher->dispatch(new EntityRemoved($category));
    }
}
