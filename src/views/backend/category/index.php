<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\TreeManager\Manager\TreeDataSource;
use Besnovatyj\TreeManager\Manager\TreeWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View           $this
 * @var string         $title
 * @var TreeDataSource $treeDataSource
 */

$this->title                   = $title;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-tree-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <?= TreeWidget::widget([
                'dataSource' => $treeDataSource,
                'endpoints'  => [
                    'loadChildren'   => Url::to(['/RunShop/backend/category/load-children']),
                    'createNode'     => Url::to(['/RunShop/backend/category/create']),
                    'updateNode'     => Url::to(['/RunShop/backend/category/update']),
                    'deleteNode'     => Url::to(['/RunShop/backend/category/delete']),
                    'moveNode'       => Url::to(['/RunShop/backend/category/move']),
                    'toggleStatus'   => Url::to(['/RunShop/backend/category/toggle-status']),
                    'checkIntegrity' => Url::to(['/RunShop/backend/category/check-integrity']),
                ],
                'serverForms' => [
                    'enabled'       => true,
                    'display'       => 'modal',
                    'errorStrategy' => 'both',
                    'operations'    => [
                        'create' => true,
                        'edit'   => true,
                    ],
                    'getFormUrl' => Url::to(['/RunShop/backend/category/get-form']),
                ],
                'permissions' => [
                    'canCreate' => true,
                    'canUpdate' => true,
                    'canDelete' => true,
                    'canMove'   => true,
                ],
                'titleField'        => 'title',
                'enablePersistence' => true,
                'storageKey'        => 'RunShop-category-tree-state',
                'containerOptions'  => [
                    'class' => 'RunShop-category-tree-widget',
                ],
            ]) ?>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<CSS
.RunShop-category-tree-widget {
    min-height: 400px;
}
CSS
);
?>
