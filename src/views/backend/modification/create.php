<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\forms\backend\product\ModificationForm;
use yii\web\View;

/* @var $this View */
/* @var $product Product */
/* @var $model  ModificationForm */

$this->title = 'Create Modification';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['/RunShop/backend/product/index']];
$this->params['breadcrumbs'][] = ['label' => $product->name, 'url' => ['/RunShop/backend/product/view', 'id' => $product->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
