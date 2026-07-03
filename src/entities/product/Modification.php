<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\product;

use DomainException;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $price
 * @property int $quantity
 */
class Modification extends ActiveRecord
{
    public static function create(string $code, string $name, string $price, int $quantity): self
    {
        $modification = new static();
        $modification->code = $code;
        $modification->name = $name;
        $modification->price = $price;
        $modification->quantity = $quantity;
        return $modification;
    }

    public function edit(string $code, string $name, int $price, int $quantity): void
    {
        $this->code = $code;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function checkout(int $quantity): void
    {
        if ($quantity > $this->quantity) {
            throw new DomainException('Only ' . $this->quantity . ' items are available.');
        }
        $this->quantity -= $quantity;
    }

    public function isIdEqualTo(int $id): bool
    {
        return $this->id == $id;
    }

    public function isCodeEqualTo(string $code): bool
    {
        return $this->code === $code;
    }

    public static function tableName(): string
    {
        return '{{%run_shop_modifications}}';
    }
}
