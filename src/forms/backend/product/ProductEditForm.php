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
 * @property MetaForm $meta
 * @property CategoriesForm $categories
 * @property TagsForm $tags
 * @property ValueForm[] $values
 */
class ProductEditForm extends CompositeForm
{
    public $brandId;
    public $code;
    public $name;
    public $description;
    public $email_additional_text_html;
    public $email_additional_text_plain_text;
    public $mail_attach;
    public $weight;

    private Product $_product;

    public function __construct(Product $product, $config = [])
    {
        $this->brandId = $product->brand_id;
        $this->code = $product->code;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->weight = $product->weight;
        $this->email_additional_text_html = $product->email_additional_text_html;
        $this->email_additional_text_plain_text = $product->email_additional_text_plain_text;
        $this->mail_attach = $product->mail_attach;
        $this->meta = new MetaForm($product->meta);
        $this->categories = new CategoriesForm($product);
        $this->tags = new TagsForm($product);
        $this->values = array_map(function (Characteristic $characteristic) use ($product) {
            return new ValueForm($characteristic, $product->getValue($characteristic->id));
        }, Characteristic::find()->orderBy('sort')->all());
        $this->_product = $product;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name',], 'required'],
            [['brandId'], 'integer'],
            [['code', 'name'], 'string', 'max' => 255],
            [['code'], 'unique', 'targetClass' => Product::class, 'filter' => $this->_product ? ['<>', 'id', $this->_product->id] : null],
            [['description','email_additional_text_html','email_additional_text_plain_text', 'mail_attach'], 'string'],
            ['weight', 'integer', 'min' => 0],
        ];
    }

    public function brandsList(): array
    {
        return ArrayHelper::map(Brand::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    protected function internalForms(): array
    {
        return ['meta', 'categories', 'tags', 'values'];
    }

    public function attributeLabels()
    {
        return [
            'brandId' => 'Бренд',
            'code' => 'Уникальный код',
            'name' => 'Название',
            'description' => 'Описание',
            'email_additional_text_html' => 'Текст добавляемый к письму, при оформлении заказа (HTML)',
            'email_additional_text_plain_text' => 'Текст добавляемый к письму, при оформлении заказа (Plain text)',
            'mail_attach' => 'Путь к файлу вложения для письма об оплате заказа (допустимы алиасы Yii2)',
            'weight' => 'Вес',
        ];
    }
}
