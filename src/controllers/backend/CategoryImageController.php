<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\controllers\backend;

use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\forms\backend\CategoryPhotoForm;
use Besnovatyj\RunShop\repositories\CategoryRepository;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Управление изображениями категорий.
 *
 * Отдельный контроллёр для не-древовидных полей категории (изображение): структуру дерева ведёт
 * TreeManager ({@see CategoryController}), а картинку — здесь (одиночное фото, как у поста в блоге).
 * Дерево остаётся деревом.
 */
class CategoryImageController extends Controller
{
    private CategoryRepository $categories;

    public function __construct($id, $module, CategoryRepository $categories, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->categories = $categories;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Плоский список категорий (по дереву) с превью и ссылкой на редактирование изображения.
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Category::find()->orderBy(['sort_order' => SORT_ASC, 'lft' => SORT_ASC]),
            'pagination' => false,
            'sort' => false,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $category = $this->findModel($id);
        $form = new CategoryPhotoForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->photo) {
            $category->setPhoto($form->photo);
            $this->categories->save($category);
            Yii::$app->session->setFlash('success', 'Изображение обновлено.');
            return $this->redirect(['update', 'id' => $category->id]);
        }

        return $this->render('update', [
            'category' => $category,
            'model' => $form,
        ]);
    }

    /**
     * @throws NotFoundHttpException|Throwable
     */
    public function actionDelete(int $id): Response
    {
        $category = $this->findModel($id);
        $category->removePhoto();
        $this->categories->save($category);
        Yii::$app->session->setFlash('success', 'Изображение удалено.');
        return $this->redirect(['update', 'id' => $category->id]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Category
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Категория не найдена.');
    }
}
