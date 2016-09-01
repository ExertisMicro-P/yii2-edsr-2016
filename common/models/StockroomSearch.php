<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Stockroom;

/**
 * StockroomSearch represents the model behind the search form about `common\models\Stockroom`.
 */
class StockroomSearch extends Stockroom
{
    public $account;

    public function rules()
    {
        return [
            [['id', 'account_id'], 'integer'],
            [['name', 'account'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Stockroom::find();
        $query->joinWith(['account']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['account'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['account.customer_exertis_account_number' => SORT_ASC],
            'desc' => ['account.customer_exertis_account_number' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'account_id' => $this->account_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        $query->andFilterWhere(['like', 'account.customer_exertis_account_number', $this->account]);

        return $dataProvider;
    }
}
