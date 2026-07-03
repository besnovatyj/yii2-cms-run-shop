<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\backend;

use DomainException;
use Besnovatyj\Images\helpers\ImageActionsMap;
use Besnovatyj\Kernel\controller\ControllerTrait;
use Besnovatyj\RunShop\entities\product\Modification;
use Besnovatyj\RunShop\entities\product\Photo;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\forms\backend\product\PriceForm;
use Besnovatyj\RunShop\forms\backend\product\ProductCreateForm;
use Besnovatyj\RunShop\forms\backend\product\ProductEditForm;
use Besnovatyj\RunShop\forms\backend\product\QuantityForm;
use Besnovatyj\RunShop\forms\backend\search\ProductSearch;
use Besnovatyj\RunShop\image\ProductImageOwner;
use Besnovatyj\RunShop\repositories\ProductRepository;
use Besnovatyj\RunShop\services\manage\ProductManageService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProductController extends Controller
{
    use ControllerTrait;

    private ProductManageService $service;
    private ProductRepository $productRepo;

    public function __construct($id, $module, ProductManageService $service, ProductRepository $productRepo, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->productRepo = $productRepo;
    }

    /**
     * Фото товара управляются модулем изображений (standalone actions).
     */
    public function actions(): array
    {
        return ImageActionsMap::get(
            Photo::class,
            fn(int $id) => new ProductImageOwner($this->productRepo->get($id), $this->productRepo),
        );
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
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): Response|string
    {
        $product = $this->findModel($id);

        $modificationsProvider = new ActiveDataProvider([
            'query' => $product->getModifications()->orderBy('name'),
            'key' => function (Modification $modification) use ($product) {
                return [
                    'product_id' => $product->id,
                    'id' => $modification->id,
                ];
            },
            'pagination' => false,
        ]);

        return $this->render('view', [
            'product' => $product,
            'modificationsProvider' => $modificationsProvider,
        ]);
    }

    /**
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $form = new ProductCreateForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $product = $this->service->create($form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        if ($form->hasErrors()) {
            $errors = $form->getErrorSummary(true);
            Yii::$app->session->addFlash('error', $errors);
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $product = $this->findModel($id);

        $form = new ProductEditForm($product);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($product->id, $form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        if ($form->hasErrors()) {
            $errors = $form->getErrorSummary(true);
            Yii::$app->session->addFlash('error', $errors);
        }
        return $this->render('update', [
            'model' => $form,
            'product' => $product,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionPrice(int $id): Response|string
    {
        $product = $this->findModel($id);

        $form = new PriceForm($product);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->changePrice($product->id, $form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (\DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('price', [
            'model' => $form,
            'product' => $product,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionQuantity(int $id): Response|string
    {
        $product = $this->findModel($id);

        $form = new QuantityForm($product);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->changeQuantity($product->id, $form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (\DomainException $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('quantity', [
            'model' => $form,
            'product' => $product,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->service->remove($id);
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionActivate(int $id): Response
    {
        try {
            $this->service->activate($id);
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDraft(int $id): Response
    {
        try {
            $this->service->draft($id);
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @param int $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
    }
}
