<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services;

use Besnovatyj\DomainEvents\TransactionManager;
use Besnovatyj\RunShop\cart\Cart;
use Besnovatyj\RunShop\cart\CartItem;
use Besnovatyj\RunShop\entities\order\CustomerData;
use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\entities\order\OrderItem;
use Besnovatyj\RunShop\forms\frontend\order\OrderForm;
use Besnovatyj\RunShop\repositories\DeliveryMethodRepository;
use Besnovatyj\RunShop\repositories\OrderRepository;
use Besnovatyj\RunShop\repositories\ProductRepository;
use Besnovatyj\User\Module;
use Besnovatyj\User\repositories\UserRepository;
use Yii;
use yii\base\Exception;

class OrderService
{
    private $cart;
    private $orders;
    private $products;
    private $users;
    private $deliveryMethods;
    private $transaction;

    public function __construct(
        Cart                     $cart,
        OrderRepository          $orders,
        ProductRepository        $products,
        UserRepository           $users,
        DeliveryMethodRepository $deliveryMethods,
        TransactionManager       $transaction
    )
    {
        $this->cart = $cart;
        $this->orders = $orders;
        $this->products = $products;
        $this->users = $users;
        $this->deliveryMethods = $deliveryMethods;
        $this->transaction = $transaction;
    }

    /**
     * @throws Exception
     */
    public function checkout(OrderForm $form, int $userId): Order
    {
        $user = $this->users->find($userId);

        $products = [];
        $items = array_map(function (CartItem $item) use (&$products) {
            $product = $item->getProduct();
            $product->checkout($item->getModificationId(), $item->getQuantity());
            $products[] = $product;
            return OrderItem::create(
                $product,
                $item->getModificationId(),
                $item->getPrice(),
                $item->getQuantity()
            );
        }, $this->cart->getItems());

        $order = Order::create(
            $user->id,
            new CustomerData(
                $form->customer->firstName,
                $form->customer->lastName,
                $form->customer->email,
                $form->customer->phone,
            ),
            $items,
            $this->cart->getCost()->getTotal(),
            $form->note
        );

        $this->transaction->wrap(function () use ($order, $products, $items) {
            $this->orders->save($order);

            // Явно сохраняем позиции заказа (без SaveRelationsBehavior).
            foreach ($items as $orderItem) {
                $orderItem->order_id = $order->id;
                if (!$orderItem->save()) {
                    throw new \RuntimeException('Ошибка сохранения позиции заказа.');
                }
            }

            // Списание остатков: товар + его модификации (checkout мутировал их в памяти).
            foreach ($products as $product) {
                $this->products->save($product);
                foreach ($product->modifications as $modification) {
                    $modification->save();
                }
            }

            $this->cart->clear();
        });

        return $order;
    }

    public function setMerchantOrderId(Order $order, string $id): void
    {
        $order->setMerchantOrderId($id);
        $this->orders->save($order);
    }

    public function pay(Order $order, \YooKassa\Model\Payment\PaymentInterface $paymentInfo): bool
    {
        if ($order->isPending() && $paymentInfo->paid) {
            $order->pay($paymentInfo->payment_method->type . '_' . $paymentInfo->payment_method->title);
            Yii::info('Заказ №' . $order->id . ' Оплачен', 'yookassa');
            $this->orders->save($order);
        }
        return true;
    }

    public function cancel(Order $order, string $reason = ''): void
    {
        $order->cancel($reason);
        $this->orders->save($order);
    }

    /**
     * @throws Exception
     */
    public function findOrCreateAnonUser(OrderForm $form): ?\Besnovatyj\User\entities\User
    {
        // TODO сюда попадают только не аутентифицированные

        // Для гостей, которых еще нет в базе, создаем пользователя перед созданием заказа и прикрепляем его к заказу сразу
        // (высылаем письмо с данными заказа и отдельное письмо ссылкой для подтверждения регистрации на сайте).
        // --------------
        // Для гостей, которые уже есть в базе, прикрепляем заказ к пользователю только после оплаты заказа
        // (высылаем письмо только с данными заказа) Номер заказа и данные юзера должна вернуть ЮКасса.
        // --------------
        // Аутентификацию разрешаем на сайте только после подтверждения почтового адреса (регистрация реализована через sign метод)
        // Без подтверждения аккаунта только ОДНОРАЗОВАЯ страница с данными по оплате заказа и информация о том что высланы два письма на почту.

        // Если не аутентифицирован, но e-mail в базе уже есть
        $user = $this->users->findAnyByEmail($form->customer->email);
        // Если не аутентифицирован и e-mail в базе отсутствует, то создаем запрос на создание юзера с подтверждением через e-mail
        if (!$user) {
            $userModule = Yii::$app->getModule('user');
            $user = $userModule->createUserSilently($form->customer);

            // В классе модуля не должно лежать этого метода
            // public function createUserSilently(\Besnovatyj\RunShop\forms\frontend\order\CustomerForm $customer): \Besnovatyj\User\entities\User
            //    {
            //        $form = new SignupForm();
            //        $form->load([
            //            'SignupForm' => [
            //                'username' => $customer->email,
            //                'email' => $customer->email,
            //                'phone' => $customer->phone,
            //                'password' => Yii::$app->security->generateRandomString(),
            //                'profile' => [
            //
            //                ],
            //            ],
            //            'ProfileEditForm' => [
            //                'firstName' => $customer->firstName,
            //                'lastName' => $customer->lastName ?: ''
            //            ]
            //        ]);
            //
            //        if ($form->validate()) {
            //            return $this->signupService->signup($form);
            //        } else {
            //            $err = 'Не удалось проверить регистрационные данные';
            //            if ($form->hasErrors()) {
            //                $errors = $form->getErrorSummary(true);
            //                $err = implode(' + ', $errors);
            //            }
            //            throw new \DomainException($err);
            //        }
            //    }


        }
        return $user;
    }

}













