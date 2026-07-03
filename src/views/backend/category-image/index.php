<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\category\Category;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Изображения категорий';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card rounded-0">
    <div class="card-header"><?= Html::encode($this->title) ?></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'header' => 'Категория',
                    'format' => 'raw',
                    'value' => static function (Category $category): string {
                        $indent = $category->depth > 0
                            ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', (int) $category->depth) . '— '
                            : '';
                        return $indent . Html::encode($category->name);
                    },
                ],
                [
                    'header' => 'Изображение',
                    'format' => 'raw',
                    'value' => static function (Category $category): string {
                        return $category->photo
                            ? Html::img($category->getThumbUrl('photo', 'admin'), ['alt' => ''])
                            : '<span class="text-muted">—</span>';
                    },
                ],
                [
                    'header' => '',
                    'format' => 'raw',
                    'value' => static function (Category $category): string {
                        return Html::a('Изменить изображение', ['update', 'id' => $category->id], [
                            'class' => 'btn btn-sm btn-outline-primary rounded-0',
                        ]);
                    },
                ],
            ],
        ]) ?>
    </div>
</div>
