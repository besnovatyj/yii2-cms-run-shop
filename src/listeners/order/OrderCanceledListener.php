<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\listeners\order;

use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\repositories\events\OrderCanceled;
use yii\mail\MailerInterface;

class OrderCanceledListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(OrderCanceled $event): void
    {
        $order = $event->order;
        $this->toCustomer($order);
        $this->toAdmin($order);
    }

    protected function toCustomer(Order $order): void
    {
        $sent = $this->mailer
            ->compose(
                ['html' => 'RunShop/order/customer/canceled-html', 'text' => 'RunShop/order/customer/canceled-text'],
                ['order' => $order]
            )
            ->setTo($order->customerData->email)
            ->setSubject(\Yii::$app->params['frontend_app_name'] . ': Ваш заказ отменён')
            ->send();

        if ($sent) {
            $infoData = [
                'subject' => 'Отправлено письмо о смене статуса заказа № ' . $order->id,
                'to' => 'customer',
                'email' => $order->customerData->email,
                'status' => 'canceled',
            ];
            \Yii::info($infoData, 'RunShop');
        }

        if (!$sent) {
            throw new \RuntimeException('Ошибка отправки E-mail.');
        }
    }

    protected function toAdmin(Order $order): void
    {
        $admin_email = 'prorunislife@gmail.com';
        $sent = $this->mailer
            ->compose(
                ['html' => 'RunShop/order/admin/canceled-html', 'text' => 'RunShop/order/admin/canceled-text'],
                ['order' => $order]
            )
            ->setTo($admin_email)
            ->setSubject(\Yii::$app->params['frontend_app_name'] . ': Заказ №' . $order->id . ' отменён')
            ->send();

        if ($sent) {
            $infoData = [
                'subject' => 'Отправлено письмо о смене статуса заказа № ' . $order->id,
                'to' => 'admin',
                'email' => $admin_email,
                'status' => 'canceled',
            ];
            \Yii::info($infoData, 'RunShop');
        }

        if (!$sent) {
            throw new \RuntimeException('Ошибка отправки E-mail.');
        }
    }
}
