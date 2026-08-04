<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\controllers\frontend;

use Besnovatyj\RunShop\forms\frontend\AddToCartForm;
use Besnovatyj\RunShop\forms\frontend\ReviewForm;
use Besnovatyj\RunShop\forms\frontend\search\SearchForm;
use Besnovatyj\RunShop\readModels\BrandReadRepository;
use Besnovatyj\RunShop\readModels\CategoryReadRepository;
use Besnovatyj\RunShop\readModels\ProductReadRepository;
use Besnovatyj\RunShop\readModels\TagReadRepository;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CatalogController extends Controller
{
    private ProductReadRepository $products;
    private CategoryReadRepository $categories;
    private BrandReadRepository $brands;
    private TagReadRepository $tags;

    public function __construct(
        $id,
        $module,
        ProductReadRepository $products,
        CategoryReadRepository $categories,
        BrandReadRepository $brands,
        TagReadRepository $tags,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->products = $products;
        $this->categories = $categories;
        $this->brands = $brands;
        $this->tags = $tags;
    }

//    public function actionIndex(): string
//    {
//        $dataProvider = $this->products->getAll();
//        $category = $this->categories->getRoot();
//
//        return $this->render('index', [
//            'category' => $category,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory(string $slug): string
    {
        if (!$category = $this->categories->findBySlug($slug)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }
        // TODO во фронтэнде отдельный запрос на получение товаров через виджет
        $dataProvider = $this->products->getAllByCategory($category);

        return $this->render('category', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
//    public function actionSearch(): string
//    {
//        $form = new SearchForm();
//        $form->load(\Yii::$app->request->queryParams);
//        $form->validate();
//
//        $dataProvider = $this->products->search($form);
//
//        return $this->render('search', [
//            'dataProvider' => $dataProvider,
//            'searchForm' => $form,
//        ]);
//    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
//    public function actionBrand(int $id): string
//    {
//        if (!$brand = $this->brands->find($id)) {
//            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
//        }
//
//        $dataProvider = $this->products->getAllByBrand($brand);
//
//        return $this->render('brand', [
//            'brand' => $brand,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTag(int $id): string
    {
        if (!$tag = $this->tags->find($id)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }

        // Массив категорий с отфильтрованными по тегу товарами // TODO во фронтэнде отдельный запрос на получение товаров через виджет
        $dataProvider = $this->categories->getAllWithProductsFilteredByTag($tag);

        return $this->render('tag', [
            'tag' => $tag,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionProduct(int $id): string
    {
        if (!$product = $this->products->find($id)) {
            throw new NotFoundHttpException('Запрашиваемая вами страница не существует.');
        }

        $cartForm = new AddToCartForm($product);
        $reviewForm = new ReviewForm();

        return $this->render('product', [
            'product' => $product,
            'cartForm' => $cartForm,
            'reviewForm' => $reviewForm,
        ]);
    }
}
