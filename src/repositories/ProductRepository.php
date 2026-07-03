<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\repositories;

use Besnovatyj\DomainEvents\dispatchers\EventDispatcher;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\repositories\events\EntityPersisted;
use Besnovatyj\RunShop\repositories\events\EntityRemoved;

class ProductRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function get(int $id): Product
    {
        if (!$product = Product::findOne($id)) {
            throw new NotFoundException('Product is not found.');
        }
        return $product;
    }

    public function existsByBrand(int $id): bool
    {
        return Product::find()->andWhere(['brand_id' => $id])->exists();
    }

    public function existsByMainCategory(int $id): bool
    {
        return Product::find()->andWhere(['category_id' => $id])->exists();
    }

    public function findAllByMainCategory(int $id): ?array
    {
        return Product::find()->andWhere(['category_id' => $id])->all();
    }

    public function save(Product $product): void
    {
        if (!$product->save()) {
            throw new \RuntimeException('Saving error.');
        }
        $this->dispatcher->dispatchAll($product->releaseEvents());
        $this->dispatcher->dispatch(new EntityPersisted($product));
    }

    public function remove(Product $product): void
    {
        // TODO При удалении продукта позиция в заказе с его идентификатором останется - ForeignKey в RunShop_order_items не установлен!!!
        if (!$product->delete()) {
            throw new \RuntimeException('Removing error.');
        }
        $this->dispatcher->dispatchAll($product->releaseEvents());
        $this->dispatcher->dispatch(new EntityRemoved($product));
    }

    public function count(): int
    {
        return Product::find()->count();
    }

    public function deleteAll()
    {
        // Удалять тольк очерез очередь, так как продуктов может быть очень много и нельзя просто очистить таблицу из-за связанных таблиц
    }
}
