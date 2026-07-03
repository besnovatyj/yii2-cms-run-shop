<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\product;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $characteristic_id - Идентификатор характеристики
 * @property string $value - Значение характериситки для данного продукта
 *
 * @property Characteristic $characteristic
 */
class Value extends ActiveRecord
{
    public static function create(int $characteristicId, string $value): self
    {
        $object = new static();
        $object->characteristic_id = $characteristicId;
        $object->value = $value;
        return $object;
    }

    public static function blank(int $characteristicId): self
    {
        $object = new static();
        $object->characteristic_id = $characteristicId;
        return $object;
    }

    public function change(string $value): void
    {
        $this->value = $value;
    }

    public function isForCharacteristic(int $id): bool
    {
        return $this->characteristic_id == $id;
    }

    public function getCharacteristic(): ActiveQuery
    {
        return $this->hasOne(Characteristic::class, ['id' => 'characteristic_id']);
    }

    public static function tableName(): string
    {
        return '{{%run_shop_values}}';
    }
}
