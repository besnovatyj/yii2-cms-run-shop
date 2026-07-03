<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use kartik\file\FileInput;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\entities\product\Value;
use Besnovatyj\RunShop\forms\backend\product\PhotosForm;
use Besnovatyj\RunShop\helpers\ProductHelper;
use yii\bootstrap5\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $product Product */
/* @var $photosForm PhotosForm */
/* @var $modificationsProvider ActiveDataProvider */

$this->title = $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php if ($product->isActive()): ?>
        <?= Html::a('Скрыть', ['draft', 'id' => $product->id], ['class' => 'btn  btn-primary', 'data-method' => 'post']) ?>
    <?php else: ?>
        <?= Html::a('Активировать', ['activate', 'id' => $product->id], ['class' => 'btn  btn-success', 'data-method' => 'post']) ?>
    <?php endif; ?>
    <?= Html::a('Редактировать', ['update', 'id' => $product->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Удалить', ['delete', 'id' => $product->id], [
        'class' => 'btn  btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
</p>
<div class="row">
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">Общая информация</div>
            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $product,
                    'attributes' => [
                        'id',
                        [
                            'attribute' => 'status',
                            'value' => ProductHelper::statusLabel($product->status),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'brand_id',
                            'value' => ArrayHelper::getValue($product, 'brand.name'),
                        ],
                        'code',
                        'name',
                        [
                            'attribute' => 'category_id',
                            'value' => ArrayHelper::getValue($product, 'category.name'),
                        ],
                        [
                            'label' => 'Other categories',
                            'value' => implode(', ', ArrayHelper::getColumn($product->categories, 'name')),
                        ],
                        [
                            'label' => 'Tags',
                            'value' => implode(', ', ArrayHelper::getColumn($product->tags, 'name')),
                        ],
                        'quantity',
                        [
                            'attribute' => 'price_new',
                            'value' => \Yii::$app->formatter->asCurrency($product->price_new),
                        ],
                        [
                            'attribute' => 'price_old',
                            'value' => \Yii::$app->formatter->asCurrency($product->price_old),
                        ],
                    ],
                ]) ?>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <?= Html::a('Change Price', ['price', 'id' => $product->id], ['class' => 'btn  btn-primary']) ?>
                <?= Html::a('Change Quantity', ['quantity', 'id' => $product->id], ['class' => 'btn  btn-primary']) ?>
            </div>
        </div>
        <!-- /.card -->
    </div>
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">Характеристики</div>
            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $product,
                    'attributes' => array_map(function (Value $value) {
                        return [
                            'label' => $value->characteristic->name,
                            'value' => $value->value,
                        ];
                    }, $product->values),
                ]) ?>
            </div>
            <div class="card-footer">
            </div>
        </div>
    </div>
</div>

<div class="card rounded-0">
    <div class="card-header">Описание</div>
    <div class="card-body">
        <?= Yii::$app->formatter->asHtml($product->description, [
            'Attr.AllowedRel' => array('nofollow'),
            'HTML.SafeObject' => true,
            'Output.FlashCompat' => true,
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
        ]) ?>
    </div>
    <div class="card-footer">
    </div>
</div>

<div class="card rounded-0">
    <div class="card-header">Настройки писем отправляемых при оплате заказов</div>
    <div class="card-body">
        <hr/>
        <p>Добавляемый к письму текст:</p>
        <?= Yii::$app->formatter->asHtml($product->email_additional_text_html, [
            'Attr.AllowedRel' => array('nofollow'),
            'HTML.SafeObject' => true,
            'Output.FlashCompat' => true,
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
        ]) ?>
        <hr/>
        <p>Добавляемый к письму файл:</p>
        <?= $product->mail_attach ?>
        <hr/>
    </div>
    <div class="card-footer clearfix">

    </div>
</div>

<!-- /.card -->

<!--<div class="card rounded-0">-->
<!--    <div class="card-header">-->
<!--        Modifications-->
<!--    </div>-->
<!-- /.card-header -->
<!--    <div class="card-body">-->
<!--        <p>-->
<?php //echo Html::a('Add Modification', ['/RunShop/backend/modification/create', 'product_id' => $product->id], ['class' => 'btn btn-success rounded-0']) ?>
<!--        </p>-->
<?php
//        echo \yii\grid\GridView::widget([
//            'dataProvider' => $modificationsProvider,
//            'columns' => [
//                'code',
//                'name',
//                [
//                    'attribute' => 'price',
//                    'value' => function (Modification $model) {
//                        return \Yii::$app->formatter->asCurrency($model->price);
//                    },
//                ],
//                'quantity',
//                [
//                    'class' => ActionColumn::class,
//                    'controller' => '/RunShop/backend/modification',
//                    'template' => '{update} {delete}',
//                ],
//            ],
//        ]);
?>
<!--    </div>-->
<!-- /.card-body -->
<!--    <div class="card-footer clearfix">-->
<!--    </div>-->
<!--</div>-->

<div class="card rounded-0">
    <div class="card-header">SEO</div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $product,
            'attributes' => [
                [
                    'attribute' => 'meta.title',
                    'value' => $product->meta->title,
                ],
                [
                    'attribute' => 'meta.description',
                    'value' => $product->meta->description,
                ],
                [
                    'attribute' => 'meta.keywords',
                    'value' => $product->meta->keywords,
                ],
            ],
        ]) ?>
    </div>
    <div class="card-footer">
    </div>
</div>

<div class="card card-primary card-outline card-outline-tabs rounded-0">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link rounded-0 active blue" id="standart-photos-tab" data-toggle="pill"
                   href="#standart-photos" role="tab" aria-controls="standart-photos"
                   aria-selected="true">Photos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0 green" id="edit-photos-tab" data-toggle="pill"
                   href="#edit-photos" role="tab" aria-controls="edit-photos"
                   aria-selected="false">Edit photos</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-four-tabContent">
            <div class="tab-pane fade active show" id="standart-photos" role="tabpanel"
                 aria-labelledby="standart-photos-tab">
                <div class="row">
                    <?php foreach ($product->photos as $photo): ?>
                        <div class="col-md-2 col-xs-3" style="text-align: center">
                            <div class="btn-group">
                                <?= Html::a('<span class="fa fa-arrow-left"></span>', ['move-photo-up', 'id' => $product->id, 'photo_id' => $photo->id], [
                                    'class' => 'btn  btn-secondary',
                                    'data-method' => 'post',
                                ]); ?>
                                <?= Html::a('<span class="fa fa-trash"></span>', ['delete-photo', 'id' => $product->id, 'photo_id' => $photo->id], [
                                    'class' => 'btn  btn-secondary',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Remove photo?',
                                ]); ?>
                                <?= Html::a('<span class="fa fa-arrow-right"></span>', ['move-photo-down', 'id' => $product->id, 'photo_id' => $photo->id], [
                                    'class' => 'btn  btn-secondary',
                                    'data-method' => 'post',
                                ]); ?>
                            </div>
                            <div>
                                <?= Html::a(
                                    Html::img($photo->getThumbUrl('file', 'thumb'), ['class' => 'img-thumbnail mt-3 mb-3',]),
                                    $photo->getUploadUrl('file'),
                                    ['target' => '_blank']
                                ) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data'],
                ]); ?>

                <?= $form->field($photosForm, 'files[]')->label(false)->widget(FileInput::class, [
                    'options' => [
                        'accept' => 'image/*',
                        'multiple' => true,
                    ]
                ]) ?>
                <?= Html::submitButton('Upload', ['class' => 'btn  btn-block btn-success']) ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="tab-pane fade" id="edit-photos" role="tabpanel"
                 aria-labelledby="edit-photos-tab">

            </div>
        </div>
    </div>
    <!-- /.card -->
    <div class="card-footer clearfix">
    </div>
</div>
