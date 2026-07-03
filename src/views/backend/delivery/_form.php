<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\forms\backend\DeliveryMethodForm;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap5\ActiveForm;

/* @var $this View */
/* @var $model DeliveryMethodForm */
/* @var $form ActiveForm */
?>

<div class="method-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="box box-default">
        <div class="box-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'cost')->textInput() ?>
            <?= $form->field($model, 'minWeight')->textInput() ?>
            <?= $form->field($model, 'maxWeight')->textInput() ?>
            <?= $form->field($model, 'sort')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
