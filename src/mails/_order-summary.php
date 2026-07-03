<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use yii\helpers\Html;

/* @var $order \Besnovatyj\RunShop\entities\order\Order */
?>
<p><strong>Заказ №<?= Html::encode((string) $order->id) ?></strong> от <?= date('d.m.Y H:i', (int) $order->created_at) ?></p>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse">
    <thead>
    <tr>
        <th align="left">Товар</th>
        <th align="right">Кол-во</th>
        <th align="right">Цена</th>
        <th align="right">Сумма</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($order->items as $item): ?>
        <tr>
            <td><?= Html::encode($item->product->name ?? ('#' . $item->product_id)) ?></td>
            <td align="right"><?= (int) $item->quantity ?></td>
            <td align="right"><?= (int) $item->price ?></td>
            <td align="right"><?= (int) $item->getCost() ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<p>Доставка<?= $order->delivery_method_name ? ' (' . Html::encode($order->delivery_method_name) . ')' : '' ?>: <?= (int) $order->delivery_cost ?></p>
<p><strong>Итого к оплате: <?= (int) $order->getTotalCost() ?></strong></p>

<p>
    Покупатель: <?= Html::encode(trim($order->customerData->firstName . ' ' . $order->customerData->lastName)) ?><br>
    E-mail: <?= Html::encode($order->customerData->email) ?><br>
    Телефон: <?= Html::encode($order->customerData->phone) ?>
</p>
<?php if ($order->note): ?>
    <p>Комментарий: <?= Html::encode((string) $order->note) ?></p>
<?php endif; ?>
