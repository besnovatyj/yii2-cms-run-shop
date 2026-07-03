<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\forms\backend\product\PriceForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $product Product */
/* @var $model PriceForm */

$this->title = 'Price for Product: ' . $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $product->name, 'url' => ['view', 'id' => $product->id]];
$this->params['breadcrumbs'][] = 'Price';
?>
<?php $form = ActiveForm::begin(); ?>

<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= $form->field($model, 'new')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
        <?= $form->field($model, 'old')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
    </div>
    <div class="card-footer">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
        </div>
    </div>
</div>
<!-- /.card -->
<?php ActiveForm::end(); ?>
