<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\backend\order;

use Besnovatyj\RunShop\entities\order\Order;
use yii\base\Model;

class CustomerForm extends Model
{
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $phone = '';

    public function __construct(Order $order, array $config = [])
    {
        $this->firstName = $order->customerData->firstName;
        $this->lastName = $order->customerData->lastName;
        $this->phone = $order->customerData->phone;
        $this->email = $order->customerData->email;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['email', 'firstName'], 'required'],
            [['firstName', 'lastName'], 'string', 'max' => 255],
            ['phone', 'string', 'max' => 15],
            [['email'], 'string', 'max' => 200], // используется в кач-ве идентификатора у мерчанта (ограничение 200 символов)
        ];
    }
}
