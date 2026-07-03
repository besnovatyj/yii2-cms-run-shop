<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services;


use Besnovatyj\RunShop\entities\order\Status;
use Besnovatyj\RunShop\repositories\OrderRepository;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Payment\PaymentInterface;
use YooKassa\Model\Payment\PaymentStatus;

class YKHookService
{
    private $orders;
    private $orderService;

    public function __construct(
        OrderRepository $orders,
        OrderService    $orderService,
    )
    {
        $this->orders = $orders;
        $this->orderService = $orderService;
    }

    public function changeOrderStatus(array $requestBody): void
    {
        /**
         * $requestBody
         * { "type": "notification",
         *   "event": "payment.succeeded",
         *   "object": {
         *     "id": "22d6d597-000f-5000-9000-145f6df21d6f",
         *     "status": "succeeded",
         *     "paid": true,
         *     "amount": {
         *       "value": "2.00",
         *       "currency": "RUB"
         *     },
         *     "authorization_details": {
         *       "rrn": "10000000000",
         *       "auth_code": "000000",
         *       "three_d_secure": {
         *         "applied": true
         *       }
         *     },
         *     "created_at": "2018-07-10T14:27:54.691Z",
         *     "description": "Заказ №72",
         *     "expires_at": "2018-07-17T14:28:32.484Z",
         *     "metadata": {},
         *     "payment_method": {
         *       "type": "bank_card",
         *       "id": "22d6d597-000f-5000-9000-145f6df21d6f",
         *       "saved": false,
         *       "card": {
         *         "first6": "555555",
         *         "last4": "4444",
         *         "expiry_month": "07",
         *         "expiry_year": "2021",
         *         "card_type": "MasterCard",
         *         "issuer_country": "RU",
         *         "issuer_name": "Sberbank"
         *       },
         *       "title": "Bank card *4444"
         *   },
         *   "refundable": false,
         *   "test": false
         *  }
         * }
         */
        match ($requestBody['event']) {
            // Следуя документации, в нашем случае возможны только два статуса ответа yookassa - успешна или отменена
            NotificationEventType::PAYMENT_SUCCEEDED => $this->setSucceeded($requestBody),
            NotificationEventType::PAYMENT_CANCELED => $this->setCanceled($requestBody),
            default => throw new \DomainException('Неизвестный статус заказа у вебхука' . $requestBody['event']),
        };

    }

    private function setSucceeded($requestBody): void
    {
        $notification = new NotificationSucceeded($requestBody);
        // Получите объект платежа
        $payment = $notification->getObject();
        $order = $this->orders->getByMerchantOrderId($payment->id);
        if ($order->current_status != PaymentStatus::SUCCEEDED) {
            $this->orderService->pay($order, $payment);
        }
    }

    private function setCanceled(array $requestBody): void
    {
        $notification = new NotificationCanceled($requestBody);
        // Получите объект платежа
        $payment = $notification->getObject();
        $order = $this->orders->getByMerchantOrderId($payment->id);
        if ($order->current_status != PaymentStatus::CANCELED) {
            $this->orderService->cancel($order, 'Неуспех оплаты или отмена магазином');
        }
    }
}










