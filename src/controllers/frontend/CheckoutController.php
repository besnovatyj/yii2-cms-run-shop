<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\frontend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use DomainException;
use Exception;
use Besnovatyj\RunShop\cart\Cart;
use Besnovatyj\RunShop\forms\frontend\order\OrderForm;
use Besnovatyj\RunShop\readModels\ProductReadRepository;
use Besnovatyj\RunShop\services\CartService;
use Besnovatyj\RunShop\services\OrderService;
use Besnovatyj\RunShop\services\YookassaService;
use Yii;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CheckoutController extends Controller
{
    use ControllerTrait;

    private OrderService $orderService;
    private Cart $cart;
    private YookassaService $yookassaService;
    private ProductReadRepository $products;
    private CartService $cartService;

    public function __construct(
        $id, $module,
        OrderService $orderService,
        YookassaService $yookassaService,
        Cart $cart,
        CartService $cartService,
        ProductReadRepository $products,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->orderService = $orderService;
        $this->yookassaService = $yookassaService;
        $this->cart = $cart;
        $this->cartService = $cartService;
        $this->products = $products;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionAddToCart(int $id): Response
    {
        // Для быстрой покупки автоматом добавляем и сразу редирект на оформление
        if (!$product = $this->products->find($id)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }
        if (!$product->modifications) {
            try {
                $this->cartService->add($product->id, null, 1);
                Yii::$app->session->addFlash('success', 'Добавлено в корзину');
                return $this->redirect('/RunShop/checkout/index');
            } catch (DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->goReferer();
    }

    public function actionIndex(): Response|string
    {
        if (count($this->cart->getItems()) <= 0) {
            return $this->goHome();
        }
        $form = new OrderForm($this->cart->getWeight());
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                if ($userId = Yii::$app->user->id) { // Для аутентифицированных
                    $order = $this->orderService->checkout($form, $userId);
                    return $this->redirect(['/user/cabinet/order/view', 'id' => $order->id]);
                } elseif (Yii::$app->user->isGuest) { // Для гостей
                    $user = $this->orderService->findOrCreateAnonUser($form);
                    $order = $this->orderService->checkout($form, $user->id);
                    Yii::$app->response->cookies->add(new \yii\web\Cookie([ // See Yookassa::pay()
                        'name' => 'anon',
                        'value' => hash("sha256", $order->idempotenceKey, false),
                    ]));

                    return $this->redirect(['/RunShop/yookassa/invoice', 'id' => $order->id]);

//                    if ($order->canBePaid()) { // TODO исп-ть функц-л из Yookassa::invoice() ?
//                        $returnUrl = Yii::$app->get('frontendUrlManager')->createAbsoluteUrl(['/RunShop/yookassa/pay', 'id' => $order->id]);
//                        $response = $this->yookassaService->createPaymentRequest($order, $returnUrl);
//                        if ($response !== null) {
//                            $this->orderService->setMerchantOrderId($order, $response->id); // TODO проверять ли в ответе мерчанта совпадение с нашим заказом? (id, idempotence-key )
//                            $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
//                            return $this->redirect($confirmationUrl); // редирект на страницу оплаты
//                        }
//                    } else {
//                        Yii::$app->session->addFlash('error', 'Заказ больше не может быть оплачен.');
//                    }
                }
            } catch (Exception $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('index', [
            'cart' => $this->cart,
            'model' => $form,
        ]);
    }

}
