<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\entities;

use Besnovatyj\RunShop\entities\queries\DiscountQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id            - идентификатор
 * @property int $percent       - процент скидки
 * @property string $name       - название скидки
 * @property string $from_date  - дата начала действия скидки
 * @property string $to_date    - дата окончания действия скидки
 * @property int $status        - статус активности скидки
 * @property int $sort
 */
class Discount extends ActiveRecord
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public static function create($percent, string $name, $from_date, $to_date, $sort, $status): self
    {
        $discount = new static();
        $discount->percent = $percent;
        $discount->name = $name;
        $discount->from_date = $from_date;
        $discount->to_date = $to_date;
        $discount->sort = $sort;
        $discount->status = $status ?: self::STATUS_ACTIVE;
        return $discount;
    }

    public function edit($percent, $name, $from_date, $to_date, $sort, $status): void
    {
        $this->percent = $percent;
        $this->name = $name;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->sort = $sort;
        $this->status = $status;
    }

    public function enable(): void
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function draft(): void
    {
        $this->status = self::STATUS_INACTIVE;
    }

    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isActive(): bool
    {
        $toDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->to_date);
        return ($this->status === self::STATUS_ACTIVE) && $toDate->format('U') > time();
    }

    public static function tableName(): string
    {
        return '{{%run_shop_discounts}}';
    }

    public static function find(): DiscountQuery
    {
        return new DiscountQuery(static::class);
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
