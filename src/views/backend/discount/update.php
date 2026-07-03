<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\Discount;
use Besnovatyj\RunShop\forms\backend\DiscountForm;
use yii\web\View;

/* @var $this View */
/* @var $discount Discount */
/* @var $model DiscountForm */

$this->title = 'Update Discount: ' . $discount->name;
$this->params['breadcrumbs'][] = ['label' => 'Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $discount->name, 'url' => ['view', 'id' => $discount->id]];
$this->params['breadcrumbs'][] = 'Update';
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

