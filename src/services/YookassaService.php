<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services;

use Besnovatyj\RunShop\entities\order\Order;
use Yii;
use yii\base\InvalidConfigException;
use YooKassa\Common\Exceptions\ApiConnectionException;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\AuthorizeException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;
use YooKassa\Model\Receipt\PaymentSubject;
use YooKassa\Request\Payments\CreatePaymentResponse;

class YookassaService
{
    private $client;

    public function __construct()
    {
        $this->client = new \YooKassa\Client();
        $this->client->setAuth(Yii::$app->params['yookassaAgentId'], Yii::$app->params['yookassaPrivateKey']);

        $userAgent = $this->client->getApiClient()->getUserAgent();
        $userAgent->setFramework('Yii2', Yii::getVersion());
    }

    public function getMe(): ?array
    {
        return $this->client->me();
    }

    /**
     * Возвращает весь список платежей от мерчанта
     */
    public function getPayments(): ?\YooKassa\Request\Payments\PaymentsResponse
    {
        $cursor = null;
        $params = [
//            'limit' => 30,
//            'status' => \YooKassa\Model\Payment\PaymentStatus::CANCELED,
//            'payment_method' => \YooKassa\Model\Payment\PaymentMethodType::BANK_CARD,
//            'created_at_gte' => '2021-01-01T00:00:00.000Z',
//            'created_at_lt' => '2024-03-30T23:59:59.999Z',
        ];
        $params['cursor'] = $cursor;
        return $this->client->getPayments($params);
    }

    /**
     * Создает и отправляет запрос платежа
     * @param Order $order
     * @param string $returnUrl
     * @return CreatePaymentResponse|null
     * @throws ApiConnectionException
     * @throws ApiException
     * @throws AuthorizeException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function createPaymentRequest(Order $order, string $returnUrl): ?\YooKassa\Request\Payments\CreatePaymentResponse
    {
        $builder = \YooKassa\Request\Payments\CreatePaymentRequest::builder();
        $builder->setAmount($order->cost)
            ->setCurrency(\YooKassa\Model\CurrencyCode::RUB)
            ->setCapture(true)
            // Макс. длина строки описания платежа 128 символов (YooKassa\Model\PaymentPayment::MAX_LENGTH_DESCRIPTION)
            ->setDescription('Оплата заказа #' . $order->id . ' от ' . Yii::$app->formatter->asDate($order->created_at, 'yyyy-MM-dd'))
            ->setMetadata([
                'customer_firstName' => $order->customerData->firstName,
                'customer_lastName' => $order->customerData->lastName,
                'customer_email' => $order->customerData->email,
                'customer_phone' => $order->customerData->phone,
                'order_id' => $order->id . '_' . $order->created_at,
            ]);

        // Устанавливаем идентификатор покупателя (макс. длина 200 символов) (YooKassa\Model\PaymentPayment::MAX_LENGTH_MERCHANT_CUSTOMER_ID)
        // Указываем то без чего нельзя оформить заказ, без мыла не будут присылаться чеки
        $builder->setMerchantCustomerId($order->customerData->email);

        // Устанавливаем страницу для редиректа после оплаты
        $builder->setConfirmation([
            'type' => \YooKassa\Model\Payment\ConfirmationType::REDIRECT,
            'returnUrl' => $returnUrl,
        ]);

        // Составляем чек
        $builder->setReceiptEmail($order->customerData->email);
        $builder->setReceiptPhone($order->customerData->phone);
        // Добавим товар
        foreach ($order->items as $item) {
            $builder->addReceiptItem(
                $item->product_name,
                $item->price,
                $item->quantity,
                5, // TODO Надо настраивать в админке - статус НДС - (тег в 54 ФЗ — 1199)
                \YooKassa\Model\Receipt\PaymentMode::FULL_PAYMENT,
                PaymentSubject::SERVICE //TODO должно быть указано в товаре - из (тег в 54 ФЗ — 1212)
//                (string)$item->product_id
            );
        }

        // Создаем объект запроса
        $request = $builder->build();

        /** @var $request  \YooKassa\Request\Payments\CreatePaymentRequest */
        $yookassaLog = [
            'Ключ идемпотентности' => [
                $order->idempotenceKey
            ],
            'ИД заказа на сайте' => [
                $order->id
            ],
            'Merchant Order Id' => [
                $order->merchantOrderId
            ],
            'Получатель платежа, если задан' => [
                $request->hasRecipient() ? $request->getRecipient()->getAccountId() : null
            ],
            'Сумма создаваемого платежа' => [
                $request->getAmount()->toArray(),
            ],
            'Описание транзакции' => [
                $request->getDescription(),
            ],
            'Данные фискального чека 54-ФЗ' => [
                'FullName' => $request->hasReceipt() ? $request->getReceipt()->getCustomer()->getFullName() : null,
                'Phone' => $request->hasReceipt() ? $request->getReceipt()->getCustomer()->getPhone() : null,
                'Email' => $request->hasReceipt() ? $request->getReceipt()->getCustomer()->getEmail() : null,
                'Inn' => $request->hasReceipt() ? $request->getReceipt()->getCustomer()->getInn() : null,
                'jsonSerialize' => $request->hasReceipt() ? $request->getReceipt()->getCustomer()->jsonSerialize() : null,
            ],
            'Идентификатор записи о сохраненных платежных данных покупателя' => [
                $request->getPaymentMethodId(),
            ],
            'Данные используемые для создания метода оплаты' => [
                $request->hasPaymentMethodData() ? $request->getPaymentMethodData()->getType() : null,
            ],
            'Способ подтверждения платежа' => [
                $request->hasConfirmation() ? $request->getConfirmation()->getType() : null,
            ],
            'Сохранить платежные данные для последующего использования. Значение true инициирует создание многоразового payment_method' => [
                $request->getSavePaymentMethod(),
            ],
            'Автоматически принять поступившую оплату' => [
                $request->getCapture(),
            ],
            'Метаданные привязанные к платежу' => [
                $request->hasMetadata() ? $request->getMetadata()->toArray(): null,
            ],
            'Идентификатор покупателя в вашей системе, например электронная почта или номер телефона' => [
                $request->getMerchantCustomerId(),
            ],
        ];
        \Yii::info($yookassaLog, 'yookassa');

        /** При создании заказа этот ключ необходимо сохранять в заказе.
         * YooKassa обеспечивает идемпотентность в течение 24 часов после первого запроса, потом повторный запрос будет обработан как новый.
         * @link https://yookassa.ru/developers/using-api/interaction-format#idempotence
         * TODO https://yookassa.ru/developers/using-api/interaction-format#response
         */
        $idempotenceKey = $order->idempotenceKey;
        return $this->client->createPayment($request, $idempotenceKey);
    }

}










