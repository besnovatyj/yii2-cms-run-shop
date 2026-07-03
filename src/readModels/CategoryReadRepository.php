<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\readModels;

use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\entities\Tag;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;

/**
 * Read-side репозиторий категорий (много-корневое дерево TreeManager).
 */
class CategoryReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Category::class);
    }

    /**
     * Корневые категории (много-корневое дерево).
     *
     * @return Category[]
     */
    public function getRoots(): array
    {
        return $this->treeScope->rootsQuery()->all();
    }

    /**
     * @return Category[]
     */
    public function getAll(): array
    {
        return Category::find()->orderBy(['sort_order' => SORT_ASC, 'lft' => SORT_ASC])->all();
    }

    public function getAllWithProductsFilteredByTag(Tag $tag): DataProviderInterface
    {
        $query = Category::find()->alias('c')->orderBy(['c.sort_order' => SORT_ASC, 'c.lft' => SORT_ASC]);
        $query->joinWith(['products p'], false);
        $query->andWhere(['p.status' => Product::STATUS_ACTIVE]);
        $query->joinWith(['products.tagAssignments ta'], false);
        $query->joinWith(['products.mainPhoto'], false);
        $query->andWhere(['ta.tag_id' => $tag->id]);
        $query->groupBy('c.id');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10000, 10000],
            ],
        ]);
    }

    public function find($id): ?Category
    {
        return Category::findOne(['id' => $id]);
    }

    public function findBySlug($slug): ?Category
    {
        return Category::findOne(['slug' => $slug]);
    }

    /**
     * Путь от корня до категории (хлебные крошки, без самой категории).
     *
     * @return Category[]
     */
    public function getPath(Category $category): array
    {
        return $this->treeScope->parentsQuery($category)->all();
    }

    /**
     * Дерево с раскрытыми подкатегориями по пути активной категории (для сайдбара).
     * Корни (depth 0) + для каждого предка активной — его прямые потомки.
     *
     * @return Category[]
     */
    public function getTreeWithSubsOf(?Category $category = null): array
    {
        $query = Category::find()->orderBy(['sort_order' => SORT_ASC, 'lft' => SORT_ASC]);
        if ($category) {
            $criteria = ['or', ['depth' => 0]];
            foreach (ArrayHelper::merge([$category], $this->getPath($category)) as $item) {
                $criteria[] = ['and', ['>', 'lft', $item->lft], ['<', 'rgt', $item->rgt], ['depth' => $item->depth + 1]];
            }
            $query->andWhere($criteria);
        } else {
            $query->andWhere(['depth' => 0]);
        }

        return $query->all();
    }
}
