<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\repositories\events;

use Besnovatyj\RunShop\entities\order\Order;

class OrderCanceled
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
