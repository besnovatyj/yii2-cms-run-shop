<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use YooKassa\Model\Payment\PaymentInterface;
use YooKassa\Model\Payment\PaymentStatus;

/* @var $orderStatus PaymentInterface */
/** @see PaymentStatus */

?>
<?php if (!$orderStatus): ?>
    <div class="alert alert-dismissible">
        <h5><i class="icon fas fa-times"></i>Нет данных от мерчанта</h5>
    </div>
<?php elseif ($orderStatus->status === PaymentStatus::SUCCEEDED): ?>
    <div class="table-responsive">
        <table class="table table-bordered" style="margin-bottom: 0">
            <caption>
                Успешно оплачен и подтвержден магазином
            </caption>
            <thead>
            <tr>
                <th class="text-left">Название параметра</th>
                <th class="text-left">Значение параметра</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-left">
                    Идентификатор заказа в системе приема платежей
                </td>
                <td class="text-left">
                    <?= $orderStatus->id ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Текущее состояние платежа
                </td>
                <td class="text-left">
                    <?= $orderStatus->status ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Сумма заказа (валюта)
                </td>
                <td class="text-left">
                    <?= $orderStatus->amount->value ?> (<?= $orderStatus->amount->currency ?>)
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Описание транзакции
                </td>
                <td class="text-left">
                    <?= $orderStatus->description ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Способ проведения платежа
                </td>
                <td class="text-left">
                    <?php var_dump($orderStatus->payment_method->type, $orderStatus->payment_method->title, $orderStatus->payment_method->saved); ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Время создания заказа (DateTime object)
                </td>
                <td class="text-left">
                    <?php var_dump($orderStatus->created_at) ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Признак оплаты заказа
                </td>
                <td class="text-left">
                    <?= $orderStatus->paid ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Возможность провести возврат по API
                </td>
                <td class="text-left">
                    <?= $orderStatus->refundable ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Состояние регистрации фискального чека
                </td>
                <td class="text-left">
                    <?= $orderStatus->receiptRegistration ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Метаданные платежа указанные мерчантом<br/>
                    (order_id = $order->id . '_' . $order->created_at)
                </td>
                <td class="text-left">
                    <?php
                    foreach ($orderStatus->metadata->toArray() as $name => $value) {
                        echo $name . ': ' . $value . '<br />';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Признак тестовой операции
                </td>
                <td class="text-left">
                    <?= $orderStatus->test ?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Сумма платежа, которую получит магазин (валюта)
                </td>
                <td class="text-left">
                    <?= $orderStatus->incomeAmount->value ?> (<?= $orderStatus->incomeAmount->currency ?>)
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Идентификатор покупателя в вашей системе, например электронная почта или номер телефона
                </td>
                <td class="text-left">
                    <?= $orderStatus->merchantCustomerId ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php elseif ($orderStatus->status === PaymentStatus::PENDING): ?>
    <div class="alert alert-warning alert-dismissible">
        <h5><i class="icon fas fa-clock"></i>Ожидает оплаты покупателем</h5>
    </div>
<?php elseif ($orderStatus->status === PaymentStatus::CANCELED): ?>
    <div class="alert alert-warning alert-dismissible">
        <h5><i class="icon fas fa-clock"></i>Неуспех оплаты или отменен магазином</h5>
        <p class="mt-30"><b><?= $orderStatus->cancellation_details->getReason() ?></b></p>
    </div>
<?php endif; ?>
