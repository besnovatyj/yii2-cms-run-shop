<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\frontend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use DomainException;
use Besnovatyj\RunShop\forms\frontend\AddToCartForm;
use Besnovatyj\RunShop\readModels\ProductReadRepository;
use Besnovatyj\RunShop\services\CartService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends Controller
{
    use ControllerTrait;

    private ProductReadRepository $products;
    private CartService $service;

    public function __construct($id, $module, CartService $service, ProductReadRepository $products, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->products = $products;
        $this->service = $service;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
//                    'quantity' => ['POST'], // TODO при переходе к Fetch запросам раскомментировать
//                    'remove' => ['POST'],   // TODO при переходе к Fetch запросам раскомментировать
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $cart = $this->service->getCart();

        return $this->render('index', [
            'cart' => $cart,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionAdd(int $id): Response|string
    {
        if (!$product = $this->products->find($id)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }

        if (!$product->modifications) {
            try {
                $this->service->add($product->id, null, 1);
                Yii::$app->session->setFlash('success', 'Добавлено в корзину');
//                return $this->goReferer();
                return $this->redirect(['index']);
            } catch (DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }

        $form = new AddToCartForm($product);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->add($product->id, $form->modification, $form->quantity);
                return $this->redirect(['index']);
            } catch (DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }

        return $this->render('add', [
            'product' => $product,
            'model' => $form,
        ]);
    }

    public function actionQuantity($id): Response
    {
        try {
            $this->service->set($id, (int)Yii::$app->request->post('quantity'));
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->redirect(['index']);
    }

    public function actionRemove($id): Response
    {
        try {
            $this->service->remove($id);
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->goReferer();
    }

    public function actionClear(): Response
    {
        try {
            $this->service->clear();
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->goReferer();
    }
}
