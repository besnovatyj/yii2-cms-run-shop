<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\forms\backend\CharacteristicForm;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap5\ActiveForm;

/* @var $this View */
/* @var $model CharacteristicForm */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model) ?>
<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class'=>'form-control rounded-0']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'type')->dropDownList($model->typesList(), ['prompt' => 'Не выбрано', 'class' => 'custom-select rounded-0']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'default')->textInput(['maxlength' => true, 'class'=>'form-control rounded-0']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'sort')->textInput(['maxlength' => true, 'class'=>'form-control rounded-0']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'textVariants')->textarea(['rows' => 6, 'class'=>'form-control rounded-0']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'required')->checkbox() ?>
            </div>
        </div>
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
