<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\repositories;

use Besnovatyj\RunShop\entities\Discount;

class DiscountRepository
{
    public function get(int $id): Discount
    {
        if (!$brand = Discount::findOne($id)) {
            throw new NotFoundException('Discount is not found.');
        }
        return $brand;
    }

    public function save(Discount $brand): void
    {
        if (!$brand->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(Discount $brand): void
    {
        if (!$brand->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}
