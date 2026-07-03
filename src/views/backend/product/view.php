<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Images\widgets\upload\Widget as ImagesUploadWidget;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\entities\product\Value;
use Besnovatyj\RunShop\helpers\ProductHelper;
use yii\bootstrap5\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $product Product */
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

<div class="card rounded-0" id="photos">
    <div class="card-header"><h5>Фотографии</h5></div>
    <div class="card-body">
        <?= ImagesUploadWidget::widget([
            'ownerId'   => $product->id,
            'endpoints' => [
                'getImages'    => Url::to(['/RunShop/backend/product/get-images'], true),
                'setNewSort'   => Url::to(['/RunShop/backend/product/set-new-sort'], true),
                'upload'       => Url::to(['/RunShop/backend/product/add-image'], true),
                'deleteImage'  => Url::to(['/RunShop/backend/product/delete-image'], true),
                'setMainImage' => '/RunShop/backend/product/set-main-image',
            ],
        ]) ?>
    </div>
</div>
