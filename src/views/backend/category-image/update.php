<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\forms\backend\CategoryPhotoForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $category Category */
/* @var $model CategoryPhotoForm */

$this->title = 'Изображение категории: ' . $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Изображения категорий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $category->name;
?>
<div class="card rounded-0">
    <div class="card-header"><?= Html::encode($this->title) ?></div>
    <div class="card-body">
        <?php if ($category->photo): ?>
            <div class="mb-3">
                <img src="<?= Html::encode($category->getThumbUrl('photo', 'admin')) ?>" alt="">
                <div class="mt-2">
                    <?= Html::a('Удалить изображение', ['delete', 'id' => $category->id], [
                        'class' => 'btn btn-sm btn-outline-danger rounded-0',
                        'data-method' => 'post',
                        'data-confirm' => 'Удалить изображение категории?',
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <?= $form->field($model, 'photo')->fileInput(['accept' => 'image/*']) ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success rounded-0']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
