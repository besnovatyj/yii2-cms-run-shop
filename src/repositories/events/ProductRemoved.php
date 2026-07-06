<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\repositories\events;

use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\DomainEvents\EntityEvent;

/**
 * Событие: товар удалён.
 *
 * Расширяет EntityEvent: в очередь сериализуется только id. ВНИМАНИЕ: при жёстком
 * удалении строки к моменту обработки в воркере findEntity() вернёт null и
 * getProduct() бросит исключение — потребитель, которому нужна сама сущность,
 * заработает корректно только при переходе на мягкое удаление (soft-delete).
 */
class ProductRemoved extends EntityEvent
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
