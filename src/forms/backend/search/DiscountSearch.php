<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\forms\backend\search;

use Besnovatyj\RunShop\entities\Discount;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class DiscountSearch extends Model
{
    public $percent;
    public $name;
    public $from_date;
    public $to_date;
    public $active;

    public function rules(): array
    {
        return [
            [['percent', 'from_date', 'to_date'], 'integer'],
            [['active'], 'boolean'],
            [['name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Discount::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sort' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'active' => $this->active,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['>=', 'from_date', $this->from_date ?? null])
            ->andFilterWhere(['<=', 'to_date', $this->to_date ?? null]);

        return $dataProvider;
    }
}
