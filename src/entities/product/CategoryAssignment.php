<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\product;

use yii\db\ActiveRecord;

/**
 * @property int $product_id;
 * @property int $category_id;
 */
class CategoryAssignment extends ActiveRecord
{
    public static function create(int $categoryId): self
    {
        $assignment = new static();
        $assignment->category_id = $categoryId;
        return $assignment;
    }

    public function isForCategory(int $id): bool
    {
        return $this->category_id == $id;
    }

    public static function tableName(): string
    {
        return '{{%run_shop_cat_prod_asgmt}}';
    }
}
