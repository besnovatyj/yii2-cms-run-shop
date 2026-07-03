<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\queries;

use yii\db\ActiveQuery;

class DiscountQuery extends ActiveQuery
{
    public function active(): DiscountQuery
    {
        return $this->andWhere(['status' => true]);
    }
}
