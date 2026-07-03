<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\forms\backend\DiscountForm;
use Besnovatyj\RunShop\helpers\DiscountHelper;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model DiscountForm */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model) ?>
<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
            </div>
            <div class="col-lg-1">
                <?= $form->field($model, 'percent')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
            </div>
            <div class="col-lg-1">
                <?= $form->field($model, 'sort')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'status')->dropDownList(DiscountHelper::statusList(), ['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'from_date')
                    ->widget(\kartik\widgets\DateTimePicker::class, [
                        'model' => $model,
                        'language' => 'ru',
                        'options' => ['class' => 'form-control rounded-0', 'autocomplete' => 'off',],
                        'pluginOptions' => [
                            'startView' => 2,
                            'minViewMode' => 0,
                            'maxViewMode' => 3,
                            'autoclose' => true,
                            'todayBtn' => true,
                            'format' => 'yyyy-mm-dd hh:ii:ss',
                        ],
                    ]);
                ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'to_date')
                    ->widget(\kartik\widgets\DateTimePicker::class, [
                        'model' => $model,
                        'language' => 'ru',
                        'options' => ['class' => 'form-control rounded-0', 'autocomplete' => 'off',],
                        'pluginOptions' => [
                            'startView' => 2,
                            'minViewMode' => 0,
                            'maxViewMode' => 3,
                            'autoclose' => true,
                            'todayBtn' => true,
                            'format' => 'yyyy-mm-dd hh:ii:ss',
                        ],
                    ]);
                ?>
            </div>
        </div>
    </div>
    <div class="card-footer clearfix">
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn  btn-block btn-success']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>












