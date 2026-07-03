<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\helpers;

use Besnovatyj\RunShop\entities\Discount;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class DiscountHelper
{
    public static function statusName(int $status): string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    public static function statusList(): array
    {
        return [
            Discount::STATUS_ACTIVE => 'Вкл',
            Discount::STATUS_INACTIVE => 'Выкл',
        ];
    }

    public static function statusLabel($status): string
    {
        $class = match ($status) {
            Discount::STATUS_INACTIVE => 'badge rounded-0 bg-secondary',
            Discount::STATUS_ACTIVE => 'badge rounded-0 bg-success',
            default => 'badge rounded-0 bg-secondary',
        };

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }
}
