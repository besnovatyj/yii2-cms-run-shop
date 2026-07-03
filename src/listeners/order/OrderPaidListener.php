<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\listeners\order;

use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\repositories\events\OrderPaid;
use Yii;
use yii\mail\MailerInterface;

class OrderPaidListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(OrderPaid $event): void
    {
        $order = $event->order;
        $this->toCustomer($order);
        $this->toAdmin($order);
    }

    protected function toCustomer(Order $order): void
    {
        $message = $this->mailer
            ->compose(
                ['html' => '@Besnovatyj/RunShop/mails/order/customer/paid-html', 'text' => '@Besnovatyj/RunShop/mails/order/customer/paid-text'],
                ['order' => $order]
            )
            ->setTo($order->customerData->email)
            ->setSubject(Yii::$app->params['frontend_app_name'] . ': Ваш заказ успешно оплачен');

        foreach ($order->items as $item) {
            if (!empty($item->product->mail_attach) && is_file(Yii::getAlias($item->product->mail_attach))) {
                $message->attach(Yii::getAlias($item->product->mail_attach),['fileName' => 'Инструкция к видео.pdf']);
            }
        }

        $sent = $message->send();

        if ($sent) {
            $infoData = [
                'subject' => 'Отправлено письмо о смене статуса заказа № ' . $order->id,
                'to' => 'customer',
                'email' => $order->customerData->email,
                'status' => 'paid',
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
                ['html' => '@Besnovatyj/RunShop/mails/order/admin/paid-html', 'text' => '@Besnovatyj/RunShop/mails/order/admin/paid-text'],
                ['order' => $order]
            )
            ->setTo($admin_email)
            ->setSubject(Yii::$app->params['frontend_app_name'] . ': Оплачен новый заказ')
            ->send();

        if ($sent) {
            $infoData = [
                'subject' => 'Отправлено письмо о смене статуса заказа № ' . $order->id,
                'to' => 'admin',
                'email' => $admin_email,
                'status' => 'paid',
            ];
            \Yii::info($infoData, 'RunShop');
        }

        if (!$sent) {
            throw new \RuntimeException('Ошибка отправки E-mail.');
        }
    }
}
