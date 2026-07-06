<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\repositories\events;

use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\DomainEvents\EntityEvent;

/**
 * Событие: товар создан или обновлён.
 *
 * Расширяет EntityEvent: в очередь сериализуется только id товара, а в воркере
 * Product лениво загружается из БД через findEntity(). Потребители должны читать
 * сущность через getProduct().
 */
class ProductPersisted extends EntityEvent
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function getProduct(): Product
    {
        /** @var Product $product */
        $product = $this->getEntity();
        return $product;
    }

    protected function findEntity(int $id): ?Product
    {
        return Product::findOne($id);
    }
}
