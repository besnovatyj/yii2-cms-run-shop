<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\controllers\backend;

use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\forms\backend\CategoryForm;
use Besnovatyj\TreeManager\Manager\controllers\TreeController;
use Besnovatyj\TreeManager\Manager\services\TreeServiceInterface;
use Besnovatyj\TreeManager\Manager\TreeDataSource;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

/**
 * Контроллер управления категориями товаров (дерево, TreeManager).
 */
class CategoryController extends TreeController
{
    /**
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    public function __construct($id, $module, $config = [])
    {
        /** @var TreeServiceInterface $treeManager */
        $treeManager       = Yii::$container->get('runshop.tree.manager');
        $this->treeManager = $treeManager;

        $this->dataSource = new TreeDataSource(
            Category::class,
            static function (Category $model): array {
                return [
                    'id'    => $model->id,
                    'title' => $model->name,
                    'slug'  => $model->slug,
                ];
            },
            'sort_order'
        );

        $this->createFormClass = CategoryForm::class;
        $this->updateFormClass = CategoryForm::class;
        $this->formView        = '_form';
        $this->indexTitle      = 'Категории товаров';

        parent::__construct($id, $module, $config);
    }
}
