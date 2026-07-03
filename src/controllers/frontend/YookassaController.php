<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\frontend;

use Exception;
use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\repositories\OrderRepository;
use Besnovatyj\RunShop\services\OrderService;
use Besnovatyj\RunShop\services\YookassaService;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use YooKassa\Model\Payment\PaymentStatus;

class YookassaController extends Controller
{
    public $enableCsrfValidation = false;

    private $orders;
    private $orderService;
    private YookassaService $yookassaService;

    public function __construct(
        $id, $module,
        OrderRepository $orders,
        OrderService $orderService,
        YookassaService $yookassaService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->orders = $orders;
        $this->orderService = $orderService;
        $this->yookassaService = $yookassaService;
    }

//    public function behaviors(): array
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['@'], // Для анонов этот же функционал в контроллере CheckoutAnonController
//                    ],
//                ],
//            ],
//        ];
//    }

    /**
     * Создание запроса на оплату через мерчанта
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionInvoice(int $id): \yii\web\Response
    {
        if (Yii::$app->user->isGuest) {
            $order = $this->orders->get($id);
        } else {
            $order = $this->loadModel($id);
        }
        try {
            if ($order->canBePaid()) {
                // Если попытка оплаты повторная, то проверяем статус
                if (($merchant = $order->getMerchant()) && $paymentInfo = $merchant->getPaymentInfo()) {
                    if ($paymentInfo->paid) {
                        Yii::$app->session->addFlash('error', 'Заказ уже оплачен.');
                        return $this->redirect(['/user/cabinet/order/view', 'id' => $order->id]);
                    }
//                    if ($orderStatus->status === PaymentStatus::PENDING) {
//                        Yii::$app->session->addFlash('error', 'Заказ ожидает оплаты покупателем.');
//                    }
                    if ($paymentInfo->status === PaymentStatus::CANCELED) {
                        Yii::$app->session->addFlash('error', $reason = 'Неуспех оплаты или отмена магазином');
                        $this->orderService->cancel($order, $reason);
                    }
                }
                // Ссылка для редиректа с Yookassa при любом результате
                $returnUrl = Yii::$app->get('frontendUrlManager')->createAbsoluteUrl(['/RunShop/yookassa/pay', 'id' => $order->id]);
                $response = $this->yookassaService->createPaymentRequest($order, $returnUrl);
                if ($response !== null) {
                    $this->orderService->setMerchantOrderId($order, $response->id); // TODO проверять ли в ответе мерчанта совпадение с нашим заказом? (id, idempotence-key )
                    $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
                    return $this->redirect($confirmationUrl); // редирект на страницу оплаты
                }
            } else {
                Yii::$app->session->addFlash('error', 'Заказ больше не может быть оплачен.');
            }
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        // Если заказ не может быть оплачен, то перенаправляем
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        } else {
            return $this->redirect(['/user/cabinet/order/view', 'id' => $order->id]);
        }
    }

    /**
     * Обработка ответа от мерчанта после попытки оплаты
     * @throws NotFoundHttpException
     */
    public function actionPay($id)
    {

        if (Yii::$app->user->isGuest) {
            // TODO Статус заказа меняется по веб-хуку. Здесь только отображаем информацию для анона по номеру заказа.
            //  Ставим какую-нибудь уникальную куку в checkoutAnon::index() и проверяем её здесь один раз и сразу удаляем, метод доступен только для тех у кого есть эта кука
            //  Показываем владельцу куки информацию один раз и всё, больше ничего здесь не делаем, всё в ЛК
            try {
                $order = $this->orders->get($id);
                $cookie = Yii::$app->request->cookies->getValue('anon'); // See Checkout::index()
                if ($cookie === hash("sha256", $order->idempotenceKey, false)) {
                    Yii::$app->response->cookies->remove('anon');
                    return $this->render('anon-info', [
                        'order' => $order,
                        'info' => "Информация о заказе выслана на указанный Вами адрес электронной почты и доступна в личном кабинете",
                    ]);
                } else {
                    return  $this->goHome();
                }
            } catch (Exception $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        } else {
            $order = $this->loadModel($id);
            try {
                if ($order->isPending() && ($merchant = $order->getMerchant()) && $paymentInfo = $merchant->getPaymentInfo()) {
                    if ($paymentInfo->paid) { // Если веб-хук еще не отработал
                        $this->orderService->pay($order, $paymentInfo);
                        Yii::$app->session->setFlash('success', 'Заказ успешно оплачен.');
                    }
                } elseif ($order->isPaid()) { // Если веб-хук уже отработал
                    Yii::$app->session->setFlash('success', 'Заказ успешно оплачен.');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка оплаты заказа.');
                }
            } catch (Exception $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
            return $this->redirect(['/user/cabinet/order/view', 'id' => $order->id]);
        }

    }

    /**
     * @throws NotFoundHttpException
     */
    private function loadModel($id): Order
    {
        $userId = \Yii::$app->user->identity->getId();
        if (!$order = $this->orders->findOwn($userId, $id)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }
        return $order;
    }

}
