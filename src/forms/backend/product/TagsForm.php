<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\backend\product;

use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\entities\Tag;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property array $newNames
 */
class TagsForm extends Model
{
    /** @var int[] $existing */
    public $existing = [];
    public $textNew = '';

    public function __construct(?Product $product = null, $config = [])
    {
        if ($product) {
            $this->existing = ArrayHelper::getColumn($product->tagAssignments, 'tag_id');
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['existing', 'each', 'rule' => ['integer']],
            ['existing', 'default', 'value' => []],
            ['textNew', 'string'],
        ];
    }

    public function tagsList(): array
    {
        return ArrayHelper::map(Tag::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    public function getNewNames(): array
    {
        //return array_filter(array_map('trim', preg_split('#\s*,\s*#i', $this->textNew)));
        return array_filter(array_map('trim', explode(',', $this->textNew ?: ''))); // TODO смотри лучше  как в блоге сделано
    }
}
