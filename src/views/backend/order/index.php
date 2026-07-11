<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Backend\Widgets\grid\ActionColumn;
use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\forms\backend\search\OrderSearch;
use Besnovatyj\RunShop\helpers\OrderHelper;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}",
            'columns' => [
                [
                    'attribute' => 'id',
                    'value' => static function (Order $model) {
                        return Html::a(Html::encode($model->id), ['view', 'id' => $model->id]);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'width: 70px'],
                ],
                [
                    'attribute' => 'merchant_id',
                    'value' => static function (Order $model) {
                        $msg = 'Не назначен';
                        if ($model->merchantOrderId) {
                            $msg = $model->merchantOrderId;
                        }
                        return Html::encode($msg);
                    },
                    'format' => 'raw',
                ],
               'customerData.email',
                [
                    'attribute' => 'cost',
                    'value' => static function (Order $model) {
                        return Html::encode(Yii::$app->formatter->asCurrency($model->cost));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => static function (Order $model) {
                        return Html::a(Html::encode(Yii::$app->formatter->asDateTime($model->created_at)), ['view', 'id' => $model->id]);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'current_status',
                    'filter' => $searchModel->statusList(),
                    'value' => static function (Order $model) {
                        if ($model->isExpired() && !$model->isPaid()) {
                            return 'Истекло время оплаты';
                        }
                        return Html::a(OrderHelper::statusLabel($model->current_status) . '&nbsp;<i class="fas fa-sync-alt"></i>', \yii\helpers\Url::to(['/RunShop/backend/order/refresh-status', 'id' => $model->id]));
                    },
                    'format' => 'raw',
                ],
                ['class' => ActionColumn::class,
                    'template' => \Besnovatyj\Kernel\security\AccessHelper::filterActionColumn(['view', 'update', 'delete',]),
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
