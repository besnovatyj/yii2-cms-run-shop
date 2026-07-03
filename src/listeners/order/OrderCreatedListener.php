<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\listeners\order;

use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\repositories\events\OrderCreated;
use yii\mail\MailerInterface;

class OrderCreatedListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $this->toCustomer($order);
        $this->toAdmin($order);
    }

    protected function toCustomer(Order $order):void
    {
        $sent = $this->mailer
            ->compose(
                ['html' => '@Besnovatyj/RunShop/mails/order/customer/created-html', 'text' => '@Besnovatyj/RunShop/mails/order/customer/created-text'],
                ['order' => $order]
            )
            ->setTo($order->customerData->email)
            ->setSubject(\Yii::$app->params['frontend_app_name'] . ': Ваш заказ успешно создан')
            ->send();

        if ($sent) {
            $infoData = [
                'subject' => 'Отправлено письмо о смене статуса заказа № ' . $order->id,
                'to' => 'customer',
                'email' => $order->customerData->email,
                'status' => 'created',
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
                ['html' => '@Besnovatyj/RunShop/mails/order/admin/created-html', 'text' => '@Besnovatyj/RunShop/mails/order/admin/created-text'],
                ['order' => $order]
            )
            ->setTo($admin_email)
            ->setSubject(\Yii::$app->params['frontend_app_name'] . ': Оформлен новый заказ')
            ->send();

        if ($sent) {
            $infoData = [
                'subject' => 'Отправлено письмо о смене статуса заказа № ' . $order->id,
                'to' => 'admin',
                'email' => $admin_email,
                'status' => 'created',
            ];
            \Yii::info($infoData, 'RunShop');
        }

        if (!$sent) {
            throw new \RuntimeException('Ошибка отправки E-mail.');
        }

    }

}
