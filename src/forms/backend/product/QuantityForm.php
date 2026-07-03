<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\backend\product;

use Besnovatyj\RunShop\entities\product\Product;
use yii\base\Model;

class QuantityForm extends Model
{
    public $quantity = 0;

    public function __construct(Product $product = null, $config = [])
    {
        if ($product) {
            $this->quantity = $product->quantity;
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['quantity'], 'required'],
            [['quantity'], 'integer', 'min' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'quantity' => 'Quantity',
        ];
    }

}
