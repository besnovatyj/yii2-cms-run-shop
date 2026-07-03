<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\forms\backend\BrandForm;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap5\ActiveForm;

/* @var $this View */
/* @var $model BrandForm */
/* @var $form ActiveForm */
?>

<div class="brand-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="box box-default">
        <div class="box-header with-border">Common</div>
        <div class="box-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class'=>'form-control rounded-0']) ?>
            <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'class'=>'form-control rounded-0']) ?>

        </div>
    </div>

    <div class="box box-default">
        <div class="box-header with-border">SEO</div>
        <div class="box-body">
            <?= $form->field($model->meta, 'title')->textInput(['class'=>'form-control rounded-0']) ?>
            <?= $form->field($model->meta, 'description')->textarea(['rows' => 2, 'class'=>'form-control rounded-0']) ?>
            <?= $form->field($model->meta, 'keywords')->textInput(['class'=>'form-control rounded-0']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
