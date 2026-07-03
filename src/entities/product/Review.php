<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\product;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $created_at
 * @property int $user_id
 * @property int $product_id
 * @property int $vote
 * @property string $text
 * @property bool $active
 */
class Review extends ActiveRecord
{
    public static function create(int $userId,int $product_id, int $vote, string $text): self
    {
        $review = new static();
        $review->user_id = $userId;
        $review->product_id = $product_id;
        $review->vote = $vote;
        $review->text = $text;
        $review->created_at = time();
        $review->active = false;
        return $review;
    }

    public function edit(int $vote, string $text): void
    {
        $this->vote = $vote;
        $this->text = $text;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function draft(): void
    {
        $this->active = false;
    }

    public function isActive(): bool
    {
        return $this->active === true;
    }

    public function getRating(): bool
    {
        return $this->vote;
    }

    public function isIdEqualTo(int $id): bool
    {
        return $this->id == $id;
    }

    public static function tableName(): string
    {
        return '{{%run_shop_reviews}}';
    }
}
