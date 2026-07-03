<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\backend\product;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Meta\MetaForm;
use Besnovatyj\RunShop\entities\Brand;
use Besnovatyj\RunShop\entities\product\Characteristic;
use Besnovatyj\RunShop\entities\product\Product;
use yii\helpers\ArrayHelper;

/**
 * @property PriceForm $price
 * @property QuantityForm $quantity
 * @property MetaForm $meta
 * @property CategoriesForm $categories
 * @property PhotosForm $photos
 * @property TagsForm $tags
 * @property ValueForm[] $values
 */
class ProductCreateForm extends CompositeForm
{
    public $brandId = 0;
    public $code = '';
    public $name = '';
    public $description = '';
    public $weight = '';
    public $email_additional_text_html;
    public $email_additional_text_plain_text;
    public $mail_attach = '';

    public function __construct($config = [])
    {
        $this->price = new PriceForm();
        $this->quantity = new QuantityForm();
        $this->meta = new MetaForm();
        $this->categories = new CategoriesForm();
        $this->photos = new PhotosForm();
        $this->tags = new TagsForm();
        $this->values = array_map(function (Characteristic $characteristic) {
            return new ValueForm($characteristic);
        }, Characteristic::find()->orderBy('sort')->all());
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['code', 'name'], 'string', 'max' => 255],
            [['brandId'], 'integer'],
//            ['code', 'unique', 'targetClass' => Product::class, 'targetAttribute' => ['code','code_1','code_2',]],
            [['code'], 'unique', 'targetClass' => Product::class],
            [['description', 'email_additional_text_html', 'email_additional_text_plain_text', 'mail_attach'], 'string'],
            ['weight', 'integer', 'min' => 0],
        ];
    }

    public function brandsList(): array
    {
        return ArrayHelper::map(Brand::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'Id',
            'code' => 'Уникальный код',
            'description' => 'Описание',
            'category.name' => 'Название категории',
            'price_old' => 'Старая цена',
            'price_new' => 'Новая цена',
            'rating' => 'Рейтинг',
            'status' => 'Статус',
            'email_additional_text_html' => 'Текст добавляемый к письму, при оформлении заказа (HTML)',
            'email_additional_text_plain_text' => 'Текст добавляемый к письму, при оформлении заказа (Plain text)',
            'mail_attach' => 'Путь к файлу вложения для письма об оплате заказа (допустимы алиасы Yii2)',
            'quantity' => 'Количество',
        ];
    }

    protected function internalForms(): array
    {
        return ['price', 'quantity', 'meta', 'photos', 'categories', 'tags', 'values'];
    }
}
