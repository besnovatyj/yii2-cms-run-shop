<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\widgets;

use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\readModels\CategoryReadRepository;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Виджет со списком категорий для сайдбара фронтэнда
 */
class CategoriesWidget extends Widget
{
    public $active;
    private CategoryReadRepository $categories;

    public function __construct(CategoryReadRepository $categories, $config = [])
    {
        parent::__construct($config);
        $this->categories = $categories;
    }

    public function run(): string
    {
        $scope = new TreeQueryScope(Category::class);

        return Html::tag('div', implode(PHP_EOL, array_map(function (Category $category) use ($scope) {
            $indent = ($category->depth > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;', $category->depth) . '- ' : '');
            $active = $this->active
                && ($this->active->id == $category->id || $scope->isDescendantOf($this->active, $category));
            return Html::a(
                $indent . Html::encode($category->name),
                ['/RunShop/catalog/category', 'id' => $category->id],
                ['class' => $active ? 'list-group-item active' : 'list-group-item']
            );
        }, $this->categories->getTreeWithSubsOf($this->active))), [
            'class' => 'list-group',
        ]);
    }
}
