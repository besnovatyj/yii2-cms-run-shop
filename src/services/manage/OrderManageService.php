<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services\manage;

use Besnovatyj\RunShop\entities\order\CustomerData;
use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\forms\backend\order\OrderEditForm;
use Besnovatyj\RunShop\repositories\OrderRepository;
use yii\web\NotFoundHttpException;

class OrderManageService
{
    private $orders;

    public function __construct(OrderRepository $orders)
    {
        $this->orders = $orders;
    }

    public function edit(int $id, OrderEditForm $form): void
    {
        $order = $this->orders->get($id);

        $order->edit(
            new CustomerData(
                $form->customer->firstName,
                $form->customer->lastName,
                $form->customer->email,
                $form->customer->phone,
            ),
            $form->note
        );

        $this->orders->save($order);
    }

    public function remove(int $id): void
    {
        $order = $this->orders->get($id);
        $this->orders->remove($order);
    }

    public function getOrder(int $id): Order
    {
        $model = Order::findOne($id);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
    }
}
