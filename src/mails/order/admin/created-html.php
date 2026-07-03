<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $this yii\web\View */
/* @var $order \Besnovatyj\RunShop\entities\order\Order */
?>
<h2>Оформлен новый заказ №<?= (int) $order->id ?></h2>
<?= $this->render('@Besnovatyj/RunShop/mails/_order-summary', ['order' => $order]) ?>
