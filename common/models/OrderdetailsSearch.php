<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Orderdetails;

/**
 * OrderdetailsSearch represents the model behind the search form about `common\models\Orderdetails`.
 */
class OrderdetailsSearch extends Orderdetails
{
    public $customer;
    public function rules()
    {
        return [
            [['id', 'stock_item_id'], 'integer'],
            [['name', 'contact', 'street', 'town', 'city', 'postcode', 'country', 'sop', 'customer', 'po'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Orderdetails::find()->groupBy('po')->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['customer.exertis_account_number' => SORT_ASC],
            'desc' => ['customer.exertis_account_number' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'stock_item_id' => $this->stock_item_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'contact', $this->contact])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'town', $this->town])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'customer.exertis_account_number', $this->customer])
            ->andFilterWhere(['like', 'postcode', $this->postcode])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'po', $this->po])
            ->andFilterWhere(['like', 'sop', $this->sop]);

        return $dataProvider;
    }
}
