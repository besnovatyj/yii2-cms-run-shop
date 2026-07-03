<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\forms\backend\product\ModificationForm;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model  ModificationForm */
/* @var $form yii\bootstrap5\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model) ?>
<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
        <?= $form->field($model, 'quantity')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
        </div>
    </div>
</div>
<!-- /.card -->
<?php ActiveForm::end(); ?>


