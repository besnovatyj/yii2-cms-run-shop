<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\frontend\search;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\RunShop\entities\Brand;
use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\entities\product\Characteristic;
use Besnovatyj\RunShop\entities\product\Product;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property ValueForm[] $values
 */
class SearchForm extends CompositeForm
{
    public $text;
    public $category;
    public $brand;

    public function __construct(array $config = [])
    {
        $this->values = array_map(function (Characteristic $characteristic) {
            return new ValueForm($characteristic);
        }, Characteristic::find()->orderBy('sort')->all());
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['text'], 'string'],
            [['category', 'brand'], 'integer'],
        ];
    }

    public function categoriesList(): array
    {
        return ArrayHelper::map(Category::find()->andWhere(['>', 'depth', 0])->orderBy('lft')->asArray()->all(), 'id', function (array $category) {
            return ($category['depth'] > 1 ? str_repeat('-- ', $category['depth'] - 1) . ' ' : '') . $category['name'];
        });
    }

    public function brandsList(): array
    {
        return ArrayHelper::map(Brand::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    public function formName(): string
    {
        return '';
    }

    public function getColumns()
    {
        // $this->values;    // значения всех имеющихся в базе характеристик, уже заполненных из формы поиска
        // $category->characteristics // если выбрана категория, то здесь все привязанные к ней характеристики
        // $this->values->characteristic
        if (is_numeric($this->category) && ($this->category != 0 || $this->category != NULL)) {
            $category = Category::find()->andWhere(['id' => (int)$this->category])->one();
            $columns = [];
            /** @var \Besnovatyj\RunShop\entities\characteristic $characteristic */
            try {
                if (is_object($category)) {
                    foreach ($category->characteristicsChecked as $characteristic) {
                        $columns[] = [
                            'label' => $characteristic->getTitleByCategory($category->id),//$characteristic->name,
                            'value' => function (Product $model) use ($characteristic) {
                                foreach ($model->values as $value) {
                                    if ($value->characteristic_id == $characteristic->id) {
                                        return Html::encode($value->value);
                                    }
                                }
                            },
                            'format' => 'raw'
                        ];
                    }
                }
            } catch (ErrorException $e) {
                throw new \Error($e->getMessage(), $e->getCode());
            }
            return $columns;
        }
        return [];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Search by name',
            'code' => 'Code',
            'category' => 'Category',
            'brand' => 'Brand',
        ];
    }

    protected function internalForms(): array
    {
        return ['values'];
    }
}
