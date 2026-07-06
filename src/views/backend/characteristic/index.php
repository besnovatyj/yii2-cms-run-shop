<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\product\Characteristic;
use Besnovatyj\RunShop\forms\backend\search\CharacteristicSearch;
use Besnovatyj\RunShop\helpers\CharacteristicHelper;
use Besnovatyj\User\components\Helper;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use yii\data\ActiveDataProvider;
use Besnovatyj\Backend\Widgets\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel CharacteristicSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Characteristics';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('Create Characteristic', ['create'], ['class' => 'btn  btn-success']) ?>
</p>
<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}",
            'columns' => [
                'id',
                'sort',
                [
                    'attribute' => 'name',
                    'value' => function (Characteristic $model) {
                        return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'type',
                    'filter' => $searchModel->typesList(),
                    'value' => function (Characteristic $model) {
                        return CharacteristicHelper::typeName($model->type);
                    },
                ],
                [
                    'attribute' => 'required',
                    'filter' => $searchModel->requiredList(),
                    'format' => 'boolean',
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
<!-- /.card -->
