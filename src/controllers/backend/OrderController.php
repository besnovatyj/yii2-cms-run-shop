<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\backend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use DomainException;
use Besnovatyj\RunShop\entities\order\Order;
use Besnovatyj\RunShop\forms\backend\order\OrderEditForm;
use Besnovatyj\RunShop\forms\backend\search\OrderSearch;
use Besnovatyj\RunShop\services\manage\OrderManageService;
use Besnovatyj\RunShop\services\YookassaService;
use Yii;
use yii\base\ExitException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OrderController extends Controller
{
    use ControllerTrait;

    private OrderManageService $orderManageService;
    private YookassaService $yookassaService;

    public function __construct($id, $module, YookassaService $yookassaService, OrderManageService $orderManageService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->orderManageService = $orderManageService;
        $this->yookassaService = $yookassaService;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'export' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return Response|\yii\console\Response
     */
    public function actionExport(): Response|\yii\console\Response
    {
        $query = Order::find()->orderBy(['id' => SORT_DESC]);

        $objPHPExcel = new \PHPExcel();

        $worksheet = $objPHPExcel->getActiveSheet();

        foreach ($query->each() as $row => $order) {
            /** @var Order $order */

            $worksheet->setCellValueByColumnAndRow(0, $row + 1, $order->id);
            $worksheet->setCellValueByColumnAndRow(1, $row + 1, date('Y-m-d H:i:s', $order->created_at));
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $file = tempnam(sys_get_temp_dir(), 'export');
        $objWriter->save($file);

        return Yii::$app->response->sendFile($file, 'report.xlsx');
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'order' => $this->orderManageService->getOrder($id),
        ]);
    }

    public function actionRefreshStatus(int $id): Response
    {
        $order = $this->orderManageService->getOrder($id);
            try {
                $order->refreshStatusByMerchant();
            } catch (DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        return $this->goReferer();
    }

    /**
     * @return array
     * @throws ExitException
     */
    public function actionOrderPaymentStatus(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['status' => 'error'];
        if ($this->isFetchRequest()) {
            $id = Yii::$app->request->post('id');
            if (is_numeric($id)) {
                try {
                    $order = $this->orderManageService->getOrder($id);
                    if (($merchant = $order->getMerchant()) && $paymentInfo = $merchant->getPaymentInfo()) {
                        $response['data'] = $this->renderPartial('order-payment-status', ['order' => $order, 'orderStatus' => $paymentInfo,]);
                    } else {
                        $response['data'] = $this->renderPartial('order-payment-status', ['order' => $order, 'orderStatus' => null,]);;
                    }
                    $response['status'] = 'success';
                    $response['message'] = 'Data received!';
                    return $response;
                } catch (\Exception $e) {
                    $this->ajaxError($e);
                }
            }
        }
        return $response;
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $order = $this->orderManageService->getOrder($id);

        $form = new OrderEditForm($order);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->orderManageService->edit($order->id, $form);
                return $this->redirect(['view', 'id' => $order->id]);
            } catch (DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('update', [
            'model' => $form,
            'order' => $order,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->orderManageService->remove($id);
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

}
