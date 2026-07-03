<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\product\Modification;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\forms\backend\product\ModificationForm;

/* @var $this yii\web\View */
/* @var $product Product */
/* @var $modification Modification */
/* @var $model  ModificationForm */

$this->title = 'Update Modification: ' . $modification->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['/RunShop/backend/product/index']];
$this->params['breadcrumbs'][] = ['label' => $product->name, 'url' => ['/RunShop/backend/product/view', 'id' => $product->id]];
$this->params['breadcrumbs'][] = $modification->name;
?>
<div class="modification-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
