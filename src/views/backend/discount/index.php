<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Backend\Widgets\grid\ActionColumn;
use Besnovatyj\RunShop\entities\Discount;
use Besnovatyj\RunShop\forms\backend\search\DiscountSearch;
use Besnovatyj\RunShop\helpers\DiscountHelper;
use Besnovatyj\SwitcherColumn\SwitcherColumn;
use modules\user\components\Helper;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel DiscountSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Discounts';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Create Discount', ['create'], ['class' => 'btn  btn-success']) ?>
</p>

<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}",
            'rowOptions' => static function (Discount $model) {
                $result = match (true) {
                    ($model->percent >= 30 && $model->isActive()) => '#ffc5c5',
                    ($model->percent >= 15 && $model->isActive()) => '#ffe6c5',
                    ($model->percent >= 10 && $model->isActive()) => '#fffdc5',
                    ($model->percent < 10 && $model->isActive()) => '#c5dfff',
                    default => '#fff',
                };
                return ['style' => "background: $result"];
            },
            'columns' => [
                'sort',
                [
                    'attribute' => 'name',
                    'value' => function (Discount $model) {
                        return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                    },
                    'format' => 'raw',
                ],
                'from_date:datetime',
                'to_date:datetime',
                'percent',
                [
                    'class' => SwitcherColumn::class,
                    'attribute' => 'status',
                    'filter' => [true => 'Вкл.', false => 'Выкл.'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function (Discount $model) {
                        return DiscountHelper::statusName($model->status);
                    },
                    'format' => 'raw',
                ],
                ['class' => ActionColumn::class,
                    'template' => Helper::filterActionColumn(['view', 'update', 'delete',]),
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
