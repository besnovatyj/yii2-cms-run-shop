<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order \Besnovatyj\RunShop\entities\order\Order */
?>
<h2>Ваш заказ отменён</h2>
<p>Здравствуйте, <?= Html::encode($order->customerData->firstName) ?>!</p>
<p>Заказ №<?= (int) $order->id ?> отменён<?= $order->cancel_reason ? ': ' . Html::encode((string) $order->cancel_reason) : '' ?>.</p>
<?= $this->render('@Besnovatyj/RunShop/views/mail/_order-summary', ['order' => $order]) ?>
