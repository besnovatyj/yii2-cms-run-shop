<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\backend;

use Besnovatyj\RunShop\entities\Discount;
use yii\base\Model;

/**
 * @var string $name - Название
 * @var int $percent - процент скидки
 * @var int $from_date - дата начала действия
 * @var int $to_date - дата окончания действия
 * @var int $status - статус активности
 * @var int $sort - сортировка
 */
class DiscountForm extends Model
{
    public $name;
    public $percent;
    public $from_date;
    public $to_date;
    public $status;
    public $sort;
    private Discount $_discount;

    public function __construct(Discount $discount = null, $config = [])
    {
        if ($discount) {
            $this->percent = $discount->percent;
            $this->name = $discount->name;
            $this->from_date = $discount->from_date;
            $this->to_date = $discount->to_date;
            $this->status = $discount->status;
            $this->sort = $discount->sort;

            $this->_discount = $discount;
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name', 'percent', 'status'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['percent', 'sort'], 'integer'],
            [['from_date', 'to_date',], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['status'], 'in', 'range' => [Discount::STATUS_ACTIVE, Discount::STATUS_INACTIVE]],
//            [['name'], 'unique', 'targetClass' => Discount::class, 'filter' => !empty($this->_discount) ? ['<>', 'id', $this->_discount->id] : null]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'percent' => 'Процент',
            'from_date' => 'Дата начала',
            'to_date' => 'Дата окончания',
            'status' => 'Статус',
            'sort' => 'Сортировка',
        ];
    }


}
