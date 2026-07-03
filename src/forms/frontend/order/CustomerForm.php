<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\frontend\order;

use yii\base\Model;
use yii\web\IdentityInterface;

class CustomerForm extends Model
{
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $phone;

    public function __construct($config = [])
    {

        /** @var IdentityInterface $user */
        $user = \Yii::$app->user->identity;
        if ($user) {
            $this->email = $user->email ?: '';
            $this->phone = $user->phone ?: '';
            $this->firstName = $user->profile->firstName ?: '';
            $this->lastName = $user->profile->lastName ?: '';
        } else {
            $this->email = '';
            $this->phone = '';
            $this->firstName = '';
            $this->lastName = '';
        }

        parent::__construct($config);
    }

//    public function init(): void
//    {
//        parent::init();
//    }

    public function rules(): array
    {
        return [
            [['firstName', 'lastName', 'email'], 'required'],
            [['firstName', 'lastName'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 200], // используется в кач-ве идентификатора у мерчанта (ограничение 200 символов)
            ['phone', 'string', 'max' => 15],
//            ['phone', PhoneValidator::class],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'firstName' => 'Имя',
            'lastName' => 'Фамилия',
            'email' => 'E-mail',
            'phone' => 'Телефон',
        ];
    }
}
