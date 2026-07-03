<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\repositories;

use Besnovatyj\DomainEvents\dispatchers\EventDispatcher;
use Besnovatyj\RunShop\entities\order\Order;
use RuntimeException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class OrderRepository
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function get(int $id): Order
    {
        if (!$order = Order::findOne($id)) {
            throw new NotFoundException('Order is not found.');
        }
        return $order;
    }

    public function save(Order $order): void
    {
        if (!$order->save()) {
            throw new RuntimeException('Saving error.');
        }
        $this->dispatcher->dispatchAll($order->releaseEvents());
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function remove(Order $order): void
    {
        if (!$order->delete()) {
            throw new RuntimeException('Removing error.');
        }
        $this->dispatcher->dispatchAll($order->releaseEvents());
    }

    public function findOwnProvider($userId): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Order::find()
                ->andWhere(['user_id' => $userId])
                ->orderBy(['id' => SORT_DESC]),
            'sort' => false,
        ]);
    }

    public function findOwn(int $userId, $id): ?Order
    {
        /** @var $ownOrder Order */
        $ownOrder = Order::find()->andWhere(['user_id' => $userId, 'id' => $id])->one();
        return $ownOrder;
    }

    public function getByMerchantOrderId(string $merchantOrderId): Order
    {
        if (!$order = Order::find()->andWhere(['merchantOrderId' => $merchantOrderId])->one()) {
            throw new NotFoundException('Order is not found.');
        }
        /** @var $order Order */
        return $order;
    }

}
