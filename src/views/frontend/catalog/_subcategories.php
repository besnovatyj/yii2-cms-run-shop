<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\RunShop\entities\category\Category;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $category Category */

$children = $category->getChildrenList();
?>

<?php if ($children): ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php foreach ($children as $child): ?>
                <a href="<?= Html::encode(Url::to(['/RunShop/catalog/category', 'slug' => $child->slug])) ?>"><?= Html::encode($child->name) ?></a> &nbsp;
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
