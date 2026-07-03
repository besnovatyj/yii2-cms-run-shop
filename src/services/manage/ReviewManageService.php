<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services\manage;

use Besnovatyj\RunShop\forms\backend\product\ReviewEditForm;
use Besnovatyj\RunShop\repositories\ProductRepository;

class ReviewManageService
{
    private $products;

    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    public function edit(int $id, $reviewId, ReviewEditForm $form): void
    {
        $product = $this->products->get($id);
        $product->editReview(
            $reviewId,
            $form->vote,
            $form->text
        );
        $this->products->save($product);
    }

    public function activate(int $id, $reviewId): void
    {
        $product = $this->products->get($id);
        $product->activateReview($reviewId);
        $this->products->save($product);
    }

    public function draft(int $id, $reviewId): void
    {
        $product = $this->products->get($id);
        $product->draftReview($reviewId);
        $this->products->save($product);
    }

    public function remove(int $id, $reviewId): void
    {
        $product = $this->products->get($id);
        $product->removeReview($reviewId);
        $this->products->save($product);
    }
}
