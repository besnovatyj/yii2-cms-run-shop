<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\frontend;

use Exception;
use Besnovatyj\RunShop\repositories\OrderRepository;
use Besnovatyj\RunShop\services\OrderService;
use Besnovatyj\RunShop\services\YKHookService;
use Besnovatyj\RunShop\services\YookassaService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use YooKassa\Model\Notification\NotificationEventType;

class YookassaHookController extends Controller
{
    public $enableCsrfValidation = false;

    private $orders;
    private $orderService;
    private YookassaService $yookassaService;
    private YKHookService $ykHookService;

    public function __construct(
        $id, $module,
        OrderRepository $orders,
        OrderService $orderService,
        YookassaService $yookassaService,
        YKHookService $ykHookService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->orders = $orders;
        $this->orderService = $orderService;
        $this->yookassaService = $yookassaService;
        $this->ykHookService = $ykHookService;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'activate' => ['POST'],
                    'draft' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'], // доступ только неавторизованным
                    ],
                ],
            ],
        ];
    }

    /**
     * Url to set in Yookassa admin panel - https://runislife.ru/RunShop/yookassa-hook/handle
     * Данный метод нужен если юзер у мерчанта не нажал кнопку - вернуться в магазин
     * Если нажата кнопка вернуться в магазин, то там уже обработана смена статуса заказа
     */
    public function actionHandle(): void
    {
        $requestIP = Yii::$app->getRequest()->getUserIP();
        $error = null;
        // Список валидных IP адресов Юкассы: https://yookassa.ru/developers/using-api/webhooks#ip
        $allowedSubnets = [
            '185.71.76.0/27',
            '185.71.77.0/27',
            '77.75.153.0/25',
            '77.75.156.11',
            '77.75.156.35',
            '77.75.154.128/25',
            '2a02:5180::/32',
        ];

        $ipValidator = new yii\validators\IpValidator();
        $ipValidator->setRanges($allowedSubnets);
        $ipValidator->subnet = null; // Set CIDR prefix is optional
        if ($ipValidator->validate($requestIP, $error)) {
            Yii::info('Получен запрос на хук Юкассы с корректного IP адреса : ' . $requestIP, 'yookassa');
        } else {
            \Yii::info('Получен запрос на хук Юкассы с НЕ корректного IP адреса: ' . $requestIP, 'yookassa');
            throw new NotFoundHttpException($error);
        }

        // https://yookassa.ru/developers/using-api/webhooks
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);
        try {
            $this->ykHookService->changeOrderStatus($requestBody);
            \Yii::info(array_merge(['my_log_description' => 'Обработан запрос от Юкассы по смене статуса заказа'], $requestBody), 'yookassa');
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }

        // По идее, если нигде не выскочило исключений, то возвращаем 200
        \Yii::$app->response->setStatusCode(200)->send();

    }

}
