<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\frontend\search;

use Besnovatyj\RunShop\entities\product\Characteristic;
use yii\base\Model;

/**
 * @property int $id
 */
class ValueForm extends Model
{
    public $from;
    public $to;
    public $equal;

    private Characteristic $_characteristic;

    public function __construct(Characteristic $characteristic, $config = [])
    {
        $this->_characteristic = $characteristic;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return array_filter([
            $this->_characteristic->isString() ? ['equal', 'string'] : false,
            $this->_characteristic->isInteger() || $this->_characteristic->isFloat() ? [['from', 'to'], 'integer'] : false
        ]);
    }

    public function isFilled(): bool
    {
        return !empty($this->from) || !empty($this->to) || !empty($this->equal);
    }

    public function variantsList(): array
    {
        return $this->_characteristic->variants ? array_combine($this->_characteristic->variants, $this->_characteristic->variants) : [];
    }

    public function getCharacteristicName(): string
    {
        return $this->_characteristic->name;
    }

    public function getId(): int
    {
        return $this->_characteristic->id;
    }

    public function formName(): string
    {
        return 'v';
    }
}
