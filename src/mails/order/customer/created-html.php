<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order \Besnovatyj\RunShop\entities\order\Order */
?>
<h2>Ваш заказ успешно создан</h2>
<p>Здравствуйте, <?= Html::encode($order->customerData->firstName) ?>!</p>
<p>Мы получили ваш заказ и ожидаем оплату.</p>
<?= $this->render('@Besnovatyj/RunShop/mails/_order-summary', ['order' => $order]) ?>
