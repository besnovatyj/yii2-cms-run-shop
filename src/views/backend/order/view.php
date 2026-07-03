<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\helpers\OrderHelper;
use Besnovatyj\RunShop\helpers\PriceHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $order Order */

$this->title = 'Order ' . $order->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$this->registerJs(file_get_contents(__DIR__ . '/ajax.js'), $this::POS_END);

?>

<p>
    <?= Html::a('Update', ['update', 'id' => $order->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $order->id], [
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
        <div class="card rounded-0">
            <div class="card-header">Общая информация о заказе</div>
            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $order,
                    'attributes' => [
                        'id',
                        'created_at:datetime',
                        [
                            'attribute' => 'current_status',
//                            'value' => OrderHelper::statusLabel($order->current_status),
                            'value' => Html::a(OrderHelper::statusLabel($order->current_status) . '&nbsp;<i class="fas fa-sync-alt"></i>', \yii\helpers\Url::to(['/RunShop/backend/order/refresh-status', 'id' => $order->id])),
                            'format' => 'raw',
                        ],
                        'user_id',
                        'cost:currency',
                        'note:ntext',
                    ],
                ]) ?>
            </div>
            <div class="card-footer clearfix">
            </div>
        </div>
        <div class="card rounded-0">
            <div class="card-header">Позиции заказа</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" style="margin-bottom: 0">
                        <thead>
                        <tr>
                            <th class="text-left">Название</th>
                            <th class="text-left">Количество</th>
                            <th class="text-right">Стоимость ед.</th>
                            <th class="text-right">Всего</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order->items as $item): ?>
                            <tr>
                                <td class="text-left">
                                    <?= Html::encode($item->product_name) ?>
                                </td>
                                <td class="text-left">
                                    <?= $item->quantity ?>
                                </td>
                                <td class="text-right"><?= \Yii::$app->formatter->asCurrency($item->price) ?></td>
                                <td class="text-right"><?= \Yii::$app->formatter->asCurrency($item->getCost()) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
            </div>
        </div>
        <div class="card rounded-0">
            <div class="card-header">История изменения статуса</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" style="margin-bottom: 0">
                        <thead>
                        <tr>
                            <th class="text-left">Дата</th>
                            <th class="text-left">Статус</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order->statuses as $status): ?>
                            <tr>
                                <td class="text-left">
                                    <?= Yii::$app->formatter->asDatetime($status->created_at) ?>
                                </td>
                                <td class="text-left">
                                    <?= OrderHelper::statusLabel($status->value) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
            </div>
        </div>

        <div class="card rounded-0">
            <div class="card-header">Данные от сервиса приёма платежей</div>
            <div class="card-body"
                 id="data-from-payment-service"
                 data-url="<?= Url::to('/RunShop/backend/order/order-payment-status') ?>"
                 data-id="<?= $order->id ?>">

            </div>
            <div class="card-footer clearfix">
            </div>
        </div>
    </div>
    <div class="card-footer clearfix">
    </div>
</div>

