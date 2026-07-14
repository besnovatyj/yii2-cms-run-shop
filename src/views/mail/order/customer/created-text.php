<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $order \Besnovatyj\RunShop\entities\order\Order */

echo "Ваш заказ №{$order->id} успешно создан и ожидает оплаты.\n";
echo "Итого к оплате: {$order->getTotalCost()}\n";
