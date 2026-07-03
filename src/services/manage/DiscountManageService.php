<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services\manage;

use Besnovatyj\RunShop\entities\Discount;
use Besnovatyj\RunShop\forms\backend\DiscountForm;
use Besnovatyj\RunShop\repositories\DiscountRepository;

class DiscountManageService
{
    private $discounts;

    public function __construct(DiscountRepository $discounts)
    {
        $this->discounts = $discounts;
    }

    public function create(DiscountForm $form): Discount
    {
        $discount = Discount::create(
            $form->percent,
            $form->name,
            $form->from_date,
            $form->to_date,
            $form->status,
            $form->sort,
        );
        $this->discounts->save($discount);
        return $discount;
    }

    public function edit(int $id, DiscountForm $form): void
    {
        $discount = $this->discounts->get($id);
        $discount->edit(
            $form->percent,
            $form->name,
            $form->from_date,
            $form->to_date,
            $form->sort,
            $form->status,
        );
        $this->discounts->save($discount);
    }
    public function activate($id): void
    {
        $discount = $this->discounts->get($id);
        $discount->enable();
        $this->discounts->save($discount);
    }

    public function draft($id): void
    {
        $discount = $this->discounts->get($id);
        $discount->draft();
        $this->discounts->save($discount);
    }

    public function remove(int $id): void
    {
        $discount = $this->discounts->get($id);
        $this->discounts->remove($discount);
    }
}
