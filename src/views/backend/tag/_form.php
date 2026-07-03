<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\forms\backend\TagForm;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap5\ActiveForm;

/* @var $this View */
/* @var $model TagForm */
/* @var $form ActiveForm */
?>

<div class="tag-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="box box-default">
        <div class="box-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class'=>'form-control rounded-0']) ?>
            <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'class'=>'form-control rounded-0']) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
