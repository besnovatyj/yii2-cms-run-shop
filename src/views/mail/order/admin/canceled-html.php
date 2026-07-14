<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order \Besnovatyj\RunShop\entities\order\Order */
?>
<h2>Заказ №<?= (int) $order->id ?> отменён</h2>
<?php if ($order->cancel_reason): ?>
    <p>Причина: <?= Html::encode((string) $order->cancel_reason) ?></p>
<?php endif; ?>
<?= $this->render('@Besnovatyj/RunShop/views/mail/_order-summary', ['order' => $order]) ?>
