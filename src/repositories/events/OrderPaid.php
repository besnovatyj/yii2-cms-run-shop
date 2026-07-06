<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\repositories\events;

use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\DomainEvents\EntityEvent;

/**
 * Событие: заказ оплачен.
 *
 * Расширяет EntityEvent: в очередь сериализуется только id заказа, а в воркере
 * Order лениво загружается свежим (со связями) через getOrder().
 */
class OrderPaid extends EntityEvent
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
