<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $order \Besnovatyj\RunShop\entities\order\Order */

echo "Заказ №{$order->id} оплачен.\n";
echo "Покупатель: {$order->customerData->email}\n";
echo "Сумма: {$order->getTotalCost()}\n";
