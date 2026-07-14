<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $order \Besnovatyj\RunShop\entities\order\Order */

echo "Ваш заказ №{$order->id} отменён";
echo $order->cancel_reason ? ": {$order->cancel_reason}\n" : ".\n";
