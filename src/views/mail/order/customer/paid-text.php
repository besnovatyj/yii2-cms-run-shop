<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $order \Besnovatyj\RunShop\entities\order\Order */

echo "Ваш заказ №{$order->id} оплачен. Спасибо за покупку!\n";
echo "Сумма: {$order->getTotalCost()}\n";
