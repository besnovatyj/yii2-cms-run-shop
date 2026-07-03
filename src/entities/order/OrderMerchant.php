<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\order;

use Yii;

/**
 * Класс получает информацию о платеже у мерчанта.
 *
 * @see \YooKassa\Model\Payment\PaymentInterface
 */
class OrderMerchant
{
    private $client;
    private $_order;

    /**
     * Если доступен merchantOrderId, то возвращает заполненный объект.
     * Если нет, то мерчант еще не в курсе о данном заказе
     */
    public static function create(Order $order): ?static
    {
        if ($order->merchantOrderId) {
            $orderMerchant = new static();
            $orderMerchant->client = new \YooKassa\Client();
            $orderMerchant->client->setAuth(Yii::$app->params['yookassaAgentId'], Yii::$app->params['yookassaPrivateKey']);

            $userAgent = $orderMerchant->client->getApiClient()->getUserAgent();
            $userAgent->setFramework('Yii2', Yii::getVersion());
            $orderMerchant->_order = $order;
            return $orderMerchant;
        }
        return null;
    }

    public function getPaymentInfo(): ?\YooKassa\Model\Payment\PaymentInterface
    {
        return $this->client->getPaymentInfo($this->_order->merchantOrderId);
    }

    /**
     * @see \YooKassa\Model\Payment\PaymentStatus
     */
    public function getStatus(): ?string
    {
        $paymentInfo = $this->getPaymentInfo();
        return $paymentInfo?->getStatus();
    }

    private function __construct()
    {// Защищаем от создания через new Singleton
    }

    private function __clone()
    {// Защищаем от создания через клонирование
    }

    public function __wakeup()
    {
        throw new \Exception('deny');
    }
}
