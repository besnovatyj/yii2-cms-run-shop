<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $this yii\web\View */
/* @var $order \Besnovatyj\RunShop\entities\order\Order */
?>
<h2>Заказ №<?= (int) $order->id ?> оплачен</h2>
<?= $this->render('@Besnovatyj/RunShop/mails/_order-summary', ['order' => $order]) ?>
