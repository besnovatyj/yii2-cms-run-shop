<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\forms\backend\order\OrderEditForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $order Order */
/* @var $model OrderEditForm */

$this->title = 'Update Order: ' . $order->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $order->id, 'url' => ['view', 'id' => $order->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<?php $form = ActiveForm::begin() ?>
<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= $form->field($model->customer, 'firstName')->textInput(['class'=>'form-control rounded-0']) ?>
        <?= $form->field($model->customer, 'lastName')->textInput(['class'=>'form-control rounded-0']) ?>
        <?= $form->field($model->customer, 'phone')->textInput(['class'=>'form-control rounded-0']) ?>
        <?= $form->field($model->customer, 'email')->textInput(['class'=>'form-control rounded-0']) ?>

        <?= $form->field($model, 'note')->textarea(['rows' => 3, 'class'=>'form-control rounded-0']) ?>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>



