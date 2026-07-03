<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\DeliveryMethod;
use Besnovatyj\RunShop\forms\backend\DeliveryMethodForm;
use yii\web\View;

/* @var $this View */
/* @var $method DeliveryMethod */
/* @var $model DeliveryMethodForm */

$this->title = 'Update Delivery Method: ' . $method->name;
$this->params['breadcrumbs'][] = ['label' => 'DeliveryMethods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $method->name, 'url' => ['view', 'id' => $method->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="method-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
