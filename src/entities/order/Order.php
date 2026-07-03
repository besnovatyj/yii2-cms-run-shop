<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\order;

use Besnovatyj\DomainEvents\AggregateRoot;
use Besnovatyj\DomainEvents\EventTrait;
use DomainException;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Besnovatyj\RunShop\repositories\events\OrderCanceled;
use Besnovatyj\RunShop\repositories\events\OrderCreated;
use Besnovatyj\RunShop\repositories\events\OrderPaid;
use modules\shp\entities\DeliveryMethod;
use modules\user\entities\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * @property int $id
 * @property int $created_at
 * @property string $idempotenceKey
 * @property string $merchantOrderId - если нет, то мерчант еще не знает о данном заказе
 * @property int $user_id
 * @property int $delivery_method_id
 * @property string $delivery_method_name
 * @property int $delivery_cost
 * @property string $payment_method
 * @property int $cost
 * @property int $note
 * @property string $current_status
 * @property string $cancel_reason
 * @property CustomerData $customerData
 * @property DeliveryData $deliveryData
 *
 * @property OrderItem[] $items
 * @property Status[] $statuses
 */
class Order extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    public $customerData;
    public $deliveryData;
    public $statuses = [];

    public static function create(int $userId, CustomerData $customerData, array $items, int $cost, string $note): self
    {
        $order = new static();
        $order->user_id = $userId;
        $order->customerData = $customerData;
        $order->items = $items;
        $order->cost = $cost;
        $order->note = $note;
        $order->created_at = time();
        $order->idempotenceKey = uniqid($order->created_at, true);
        $order->addStatus(Status::PENDING);
        $order->recordEvent(new OrderCreated($order)); // TODO: Вынос в сервис `$entity->recordEvent()`. Там после этого `$repo->save();`
        return $order;
    }

    public function edit(CustomerData $customerData, string $note): void
    {
        $this->customerData = $customerData;
        $this->note = $note;
    }

    public function setMerchantOrderId(string $id): void
    {
        $this->merchantOrderId = $id;
    }

    public function setDeliveryInfo(DeliveryMethod $method, DeliveryData $deliveryData): void
    {
        $this->delivery_method_id = $method->id;
        $this->delivery_method_name = $method->name;
        $this->delivery_cost = $method->cost;
        $this->deliveryData = $deliveryData;
    }

    public function pay($method): void
    {
        if ($this->isPaid()) {
            throw new DomainException('Заказ уже оплачен.');
        }
        $this->payment_method = $method;
        $this->addStatus(Status::SUCCEEDED);
        $this->recordEvent(new OrderPaid($this)); // TODO: Вынос в сервис `$entity->recordEvent()`. Там после этого `$repo->save();`
    }

    public function cancel(string $reason): void
    {
        if ($this->isCanceled()) {
            throw new DomainException('Заказ уже отменён.');
        }
        $this->cancel_reason = $reason;
        $this->addStatus(Status::CANCELED);
        $this->recordEvent(new OrderCanceled($this)); // TODO: Вынос в сервис `$entity->recordEvent()`. Там после этого `$repo->save();`
    }

    public function getTotalCost(): int
    {
        return $this->cost + $this->delivery_cost;
    }

    public function canBePaid(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    /**
     * Юкасса даёт на повторную оплату заказа 24 часа,
     * а мы разрешим любой созданный заказ оплачивать только в течение 12 часов
     * see self::idempotenceKey
     * @return bool - Вернёт true, если после создания заказа прошло меньше 12 часов
     */
    public function isExpired(): bool
    {
        return ($this->created_at + (60 * 60 * 12)) < time();
    }

    public function isPending(): bool
    {
        return $this->current_status == Status::PENDING;
    }

    public function isPaid(): bool
    {
        return $this->current_status == Status::SUCCEEDED;
    }

    public function isCanceled(): bool
    {
        return $this->current_status == Status::CANCELED;
    }

    private function addStatus($value): void
    {
        $this->statuses[] = new Status($value, time());
        $this->current_status = $value;
    }

    public function getMerchant(): ?OrderMerchant
    {
        return OrderMerchant::create($this);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id']);
    }

    public function getDeliveryMethod(): ActiveQuery
    {
        return $this->hasMany(DeliveryMethod::class, ['id' => 'delivery_method_id']);
    }

    public function getItems(): ActiveQuery
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => SaveRelationsBehavior::class,
                'relations' => ['items'],
            ],
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function afterFind(): void
    {

        $this->statuses = array_map(function ($row) {
            return new Status(
                $row['value'],
                $row['created_at']
            );
        }, Json::decode($this->getAttribute('statuses_json')));

        $this->customerData = new CustomerData(
            $this->getAttribute('customer_firstName'),
            $this->getAttribute('customer_lastName'),
            $this->getAttribute('customer_email'),
            $this->getAttribute('customer_phone')
        );

//        $this->deliveryData = new DeliveryData(
//            $this->getAttribute('delivery_index'),
//            $this->getAttribute('delivery_address')
//        );

        parent::afterFind();
    }

    public function refreshStatusByMerchant(): void
    {   // TODO поскольку юкасса не дает данные о времени смены статуса платежа,
        //  то получение статуса необходимо делать по веб-хуку,
        //  тогда можно будет установить более-менее реальную дату смены статуса
        if ($this->merchantOrderId) {
            $paymentInfo = $this->getMerchant()->getPaymentInfo();
            if ($paymentInfo && $this->current_status != $paymentInfo->status)
                $this->addStatus($paymentInfo->status);
            $this->save();
            $this->refresh();
        }
    }

    public function beforeSave($insert): bool
    {
        $this->setAttribute('statuses_json', Json::encode(array_map(function (Status $status) {
            return [
                'value' => $status->value,
                'created_at' => $status->created_at,
            ];
        }, $this->statuses)));

        $this->setAttribute('customer_firstName', $this->customerData->firstName);
        $this->setAttribute('customer_lastName', $this->customerData->lastName);
        $this->setAttribute('customer_email', $this->customerData->email);
        $this->setAttribute('customer_phone', $this->customerData->phone);

//        $this->setAttribute('delivery_index', $this->deliveryData->index);
//        $this->setAttribute('delivery_address', $this->deliveryData->address);

        return parent::beforeSave($insert);
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'Номер заказа',
            'note' => 'Комментарий',
            'created_at' => 'Дата создания',
            'current_status' => 'Статус',
            'cost' => 'Сумма',
        ];
    }

    public static function tableName(): string
    {
        return '{{%run_shop_orders}}';
    }

}
