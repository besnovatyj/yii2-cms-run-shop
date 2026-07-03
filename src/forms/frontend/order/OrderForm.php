<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\frontend\order;

use Besnovatyj\Forms\CompositeForm;

/**
 * @property DeliveryForm $delivery
 * @property CustomerForm $customer
 */
class OrderForm extends CompositeForm
{
    public $note;
    public $reCaptcha;

    public function __construct(int $weight = 0, array $config = [])
    {
//        $this->delivery = new DeliveryForm($weight);
        $this->customer = new CustomerForm();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return array_filter([
            [['note'], 'string'],
            \Yii::$app->user->isGuest ? [['reCaptcha'], ReCaptchaValidator2::class, 'message' => 'Invalid captcha value'] : false,
        ]);
    }

    public function attributeLabels(): array
    {
        return [
            'note' => 'Дополнительная информация',
        ];
    }

    protected function internalForms(): array
    {
        return [
//            'delivery',
            'customer'
        ];
    }
}
