<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities\product;

use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * @property int $id
 * @property string $name       - Название характеристики
 * @property string $type       - Тип характериситки
 * @property string $required   - Обязательность заполнения
 * @property string $default    - Значение по умолчанию
 * @property array $variants    - Варианты выпадающего списка
 * @property int $sort          - Сортировка
 */
class Characteristic extends ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';

    public $variants;

    public static function create(string $name, $type, bool $required, $default, array $variants, int $sort): self
    {
        $object = new static();
        $object->name = $name;
        $object->type = $type;
        $object->required = $required;
        $object->default = $default;
        $object->variants = $variants;
        $object->sort = $sort;
        return $object;
    }

    public function edit(string $name, $type, bool $required, $default, array $variants, int $sort): void
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->default = $default;
        $this->variants = $variants;
        $this->sort = $sort;
    }

    public function isString(): bool
    {
        return $this->type === self::TYPE_STRING;
    }

    public function isInteger(): bool
    {
        return $this->type === self::TYPE_INTEGER;
    }

    public function isFloat(): bool
    {
        return $this->type === self::TYPE_FLOAT;
    }

    public function isSelect(): bool
    {
        return count($this->variants) > 0;
    }

    public function afterFind(): void
    {
        $this->variants = array_filter(Json::decode($this->getAttribute('variants_json')));
        parent::afterFind();
    }

    public function beforeSave($insert): bool
    {
        $this->setAttribute('variants_json', Json::encode(array_filter($this->variants)));
        return parent::beforeSave($insert);
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'sort' => 'Sort',
            'required' => 'Is required',
            'default' => 'Default',
            'variants' => 'Variants (if you fill it in, you get a drop-down list)',
        ];
    }

    public static function tableName(): string
    {
        return '{{%run_shop_characteristics}}';
    }
}
