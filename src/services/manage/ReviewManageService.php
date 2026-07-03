<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\services\manage;

use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\entities\product\Review;
use Besnovatyj\RunShop\forms\backend\product\ReviewEditForm;
use Besnovatyj\RunShop\repositories\ProductRepository;
use DomainException;

/**
 * Сервис управления отзывами (явные AR-операции, без SaveRelationsBehavior).
 */
class ReviewManageService
{
    public function __construct(private readonly ProductRepository $products) {}

    public function edit(int $id, int $reviewId, ReviewEditForm $form): void
    {
        $product = $this->products->get($id);
        $review  = $this->getReview($product, $reviewId);
        $review->edit($form->vote, $form->text);
        $review->save();
        $this->recalcRating($product);
    }

    public function activate(int $id, int $reviewId): void
    {
        $product = $this->products->get($id);
        $review  = $this->getReview($product, $reviewId);
        $review->activate();
        $review->save();
        $this->recalcRating($product);
    }

    public function draft(int $id, int $reviewId): void
    {
        $product = $this->products->get($id);
        $review  = $this->getReview($product, $reviewId);
        $review->draft();
        $review->save();
        $this->recalcRating($product);
    }

    public function remove(int $id, int $reviewId): void
    {
        $product = $this->products->get($id);
        $review  = $this->getReview($product, $reviewId);
        $review->delete();
        $this->recalcRating($product);
    }

    private function getReview(Product $product, int $reviewId): Review
    {
        $review = Review::findOne(['id' => $reviewId, 'product_id' => $product->id]);
        if (!$review) {
            throw new DomainException('Review is not found.');
        }
        return $review;
    }

    /**
     * Пересчитывает рейтинг товара по активным отзывам (из БД).
     */
    private function recalcRating(Product $product): void
    {
        $amount = 0;
        $total  = 0;
        foreach (Review::find()->andWhere(['product_id' => $product->id])->all() as $review) {
            if ($review->isActive()) {
                $amount++;
                $total += $review->getRating();
            }
        }
        $product->rating = $amount ? $total / $amount : null;
        $this->products->save($product);
    }
}
