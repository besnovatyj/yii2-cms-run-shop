<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\order;

use YooKassa\Model\Payment\PaymentStatus;

class Status extends PaymentStatus
{
    public $value;
    public $created_at;

    public function __construct(string $value, int $created_at)
    {
        $this->value = $value;
        $this->created_at = $created_at;
    }
}
