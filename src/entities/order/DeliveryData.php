<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\order;

class DeliveryData
{
    public $index;
    public $address;

    public function __construct($index, $address)
    {
        $this->index = $index;
        $this->address = $address;
    }
}
