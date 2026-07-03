<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\helpers;

use Exception;
use Besnovatyj\RunShop\entities\order\Status;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use YooKassa\Model\Payment\PaymentStatus;

class OrderHelper
{

    /**
     * @throws Exception
     */
    public static function statusName(int $status): string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    public static function statusList(): array
    {
        return [
            Status::PENDING => 'Ожидает оплаты',
            Status::SUCCEEDED => 'Оплачен',
            Status::CANCELED => 'Отменён',
        ];
    }

    /**
     * @throws Exception
     */
    public static function statusLabel($status): string
    {
        $class = match ($status) {
            Status::PENDING => 'badge rounded-0 badge-warning',
            Status::SUCCEEDED => 'badge rounded-0 bg-success',
            Status::CANCELED => 'badge rounded-0 bg-secondary',
            default => 'badge rounded-0 bg-secondary',
        };

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }
}
