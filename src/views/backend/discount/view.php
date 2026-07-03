<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\Discount;
use Besnovatyj\RunShop\helpers\DiscountHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $discount Discount */

$this->title = $discount->name;
$this->params['breadcrumbs'][] = ['label' => 'Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php if ($discount->isEnabled()): ?>
        <?= Html::a('Включена', ['draft', 'id' => $discount->id], ['class' => 'btn  btn-success', 'data-method' => 'post']) ?>
    <?php else: ?>
        <?= Html::a('Выключена', ['activate', 'id' => $discount->id], ['class' => 'btn  btn-secondary', 'data-method' => 'post']) ?>
    <?php endif; ?>
    <?= Html::a('Update', ['update', 'id' => $discount->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $discount->id], [
        'class' => 'btn  btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $discount,
            'attributes' => [
                'name',
                'sort',
                'percent',
                'from_date:datetime',
                'to_date:datetime',
                [
                    'attribute' => 'status',
                    'value' => DiscountHelper::statusName($discount->status) ,
                ],
            ],
        ]) ?>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">

    </div>
</div>
<!-- /.card -->
