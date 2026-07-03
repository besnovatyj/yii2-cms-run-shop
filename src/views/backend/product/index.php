<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\forms\backend\search\ProductSearch;
use Besnovatyj\RunShop\helpers\ProductHelper;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ProductSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('Create Product', ['create'], ['class' => 'btn  btn-success']) ?>
</p>
<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}",
            'rowOptions' => static function (Product $model) {
                return $model->quantity <= 0 ? ['style' => 'background: #fdc'] : [];
            },
            'columns' => [
                [
                    'value' => static function (Product $model) {
                        return $model->mainPhoto ? Html::img($model->mainPhoto->getThumbUrl('file', 'admin')) : null;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'width: 100px'],
                ],
                'id',
                [
                    'attribute' => 'name',
                    'value' => static function (Product $model) {
                        return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                    },
                    'format' => 'raw',
                ],
                'code',
                [
                    'attribute' => 'category_id',
                    'filter' => $searchModel->categoriesList(),
                    'value' => 'category.name',
                ],
                [
                    'attribute' => 'price_new',
                    'value' => static function (Product $model) {
                        return \Yii::$app->formatter->asCurrency($model->price_new);
                    },
                ],
                'quantity',
                [
                    'attribute' => 'status',
                    'filter' => $searchModel->statusList(),
                    'value' => static function (Product $model) {
                        return ProductHelper::statusLabel($model->status);
                    },
                    'format' => 'raw',
                ],
            ],
        ]); ?>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <nav aria-label="" class="nav-pagination">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->getPagination(),
            ]) ?>
        </nav>
    </div>
</div>
<!-- /.card -->

