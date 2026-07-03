<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\File\widgets\CkeditorCustomWidget;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\forms\backend\product\ProductEditForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $product Product */
/* @var $model ProductEditForm */

$this->title = 'Update Product: ' . $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $product->name, 'url' => ['view', 'id' => $product->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model) ?>
    <div class="card rounded-0">
        <div class="card-header">Common</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'brandId')->dropDownList($model->brandsList(), ['prompt' => 'Не выбрано', 'class' => 'custom-select rounded-0']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
                </div>
            </div>
            <?= $form->field($model, 'description')->widget(CkeditorCustomWidget::class, [
                'language' => 'ru',
                'fmDefaultPath' => '/static/origin/RunShop/products/' . $product->id,
            ]) ?>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
        </div>
    </div>

    <div class="card rounded-0">
        <div class="card-header">Настройки писем отправляемых при оплате заказов</div>
        <div class="card-body">
            <?= $form->field($model, 'email_additional_text_html')->textarea(['rows' => 4, 'class' => 'form-control rounded-0']) ?>
            <?= $form->field($model, 'email_additional_text_plain_text')->textarea(['rows' => 4, 'class' => 'form-control rounded-0']) ?>
            <?= $form->field($model, 'mail_attach')->textInput(['maxlength' => true, 'class' => 'form-control rounded-0']) ?>
        </div>
        <div class="card-footer clearfix">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card rounded-0">
                <div class="card-header">Категории</div>
                <div class="card-body">
                    <?= $form->field($model->categories, 'main')->dropDownList($model->categories->categoriesList(), ['prompt' => 'Не выбрано', 'class' => 'custom-select rounded-0']) ?>
                    <?= $form->field($model->categories, 'others')->checkboxList($model->categories->categoriesList()) ?>
                </div>
                <div class="card-footer">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-0">
                <div class="card-header">Теги</div>
                <div class="card-body">
                    <?= $form->field($model->tags, 'existing')->checkboxList($model->tags->tagsList()) ?>
                    <?= $form->field($model->tags, 'textNew')->textInput(['class' => 'form-control rounded-0']) ?>
                </div>
                <div class="card-footer">
                </div>
            </div>
        </div>
    </div>
    <div class="card rounded-0">
        <div class="card-header">Характеристики</div>
        <div class="card-body">
            <?php foreach ($model->values as $i => $value): ?>
                <?php if ($variants = $value->variantsList()): ?>
                    <?= $form->field($value, '[' . $i . ']value')->dropDownList($variants, ['prompt' => 'Не выбрано', 'class' => 'custom-select rounded-0']) ?>
                <?php else: ?>
                    <?= $form->field($value, '[' . $i . ']value')->textInput(['class' => 'form-control rounded-0']) ?>
                <?php endif ?>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
        </div>
    </div>
    <div class="card rounded-0">
        <div class="card-header">SEO</div>
        <div class="card-body">
            <?= $form->field($model->meta, 'title')->textInput(['class' => 'form-control rounded-0']) ?>
            <?= $form->field($model->meta, 'description')->textarea(['rows' => 2, 'class' => 'form-control rounded-0']) ?>
            <?= $form->field($model->meta, 'keywords')->textInput(['class' => 'form-control rounded-0']) ?>
        </div>
        <div class="card-footer">
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn  btn-block btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>
