<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\readModels;

use Besnovatyj\RunShop\entities\Brand;

class BrandReadRepository
{
    public function find($id): ?Brand
    {
        return Brand::findOne($id);
    }

    /**
     * @param string $name
     * @return Brand|null
     */
    public function findByName(string $name): ?Brand
    {
        return Brand::find()->andWhere(['name' => $name])->one();
    }
}
