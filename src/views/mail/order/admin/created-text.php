<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $order \Besnovatyj\RunShop\entities\order\Order */

echo "Оформлен новый заказ №{$order->id}.\n";
echo "Покупатель: {$order->customerData->email}, тел. {$order->customerData->phone}\n";
echo "Итого: {$order->getTotalCost()}\n";
