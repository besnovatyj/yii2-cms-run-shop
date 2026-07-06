<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\repositories\events;

use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\DomainEvents\EntityEvent;

/**
 * Событие: заказ создан.
 *
 * Расширяет EntityEvent: событие записывается в Order::create() ещё до save()
 * (id может быть null), поэтому хранится ссылка на объект, а в очередь
 * сериализуется только id — уже заполненный к моменту push. В воркере Order
 * лениво загружается свежим (со связями items/customerData) через getOrder().
 */
class OrderCreated extends EntityEvent
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function getOrder(): Order
    {
        /** @var Order $order */
        $order = $this->getEntity();
        return $order;
    }

    protected function findEntity(int $id): ?Order
    {
        return Order::findOne($id);
    }
}
