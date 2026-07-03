<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\frontend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use DomainException;
use Besnovatyj\RunShop\repositories\OrderRepository;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OrderController extends Controller
{
    use ControllerTrait;

    private $orders;

    public function __construct($id, $module, OrderRepository $orders, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->orders = $orders;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = $this->orders->findOwnProvider(Yii::$app->user->id);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        if (!$order = $this->orders->findOwn(Yii::$app->user->id, $id)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }

        return $this->render('view', [
            'order' => $order,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionRefreshStatus(int $id): Response
    {
        if (!$order = $this->orders->findOwn(Yii::$app->user->id, $id)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }

        try {
            $order->refreshStatusByMerchant();
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
