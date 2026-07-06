<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use yii\bootstrap5\ActiveForm;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\frontend\UserEditForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model UserEditForm */
/* @var $user User */

$this->title = 'Edit Profile';
$this->params['breadcrumbs'][] = ['label' => 'Cabinet', 'url' => ['cabinet/default/index']];
$this->params['breadcrumbs'][] = 'Profile';
?>
<div class="user-update">

    <div class="row">
        <div class="col-sm-6">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'email')->textInput(['maxLength' => true]) ?>
            <?= $form->field($model, 'phone', ['addon' => ['prepend' => ['content'=>'+']]])->textInput(['maxLength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
