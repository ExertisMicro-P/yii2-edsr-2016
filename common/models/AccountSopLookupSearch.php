<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountSopLookup;

/**
 * AccountSopLookupSearch represents the model behind the search form about `common\models\AccountSopLookup`.
 */
class AccountSopLookupSearch extends AccountSopLookup
{
    public function rules()
    {
        return [
            [['id', 'created'], 'integer'],
            [['account', 'sop', 'contact', 'name', 'street', 'town', 'city', 'country', 'postcode'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = AccountSopLookup::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created' => $this->created,
        ]);

        $query->andFilterWhere(['like', 'account', $this->account])
            ->andFilterWhere(['like', 'sop', $this->sop])
            ->andFilterWhere(['like', 'contact', $this->contact])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'town', $this->town])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'postcode', $this->postcode]);

        return $dataProvider;
    }
}
