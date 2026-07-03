<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user \modules\user\entities\User */
/* @var $product \Besnovatyj\RunShop\entities\product\Product */
?>
<h2>Товар снова в наличии</h2>
<p>Товар «<?= Html::encode($product->name) ?>» из вашего списка желаний снова доступен для заказа.</p>
