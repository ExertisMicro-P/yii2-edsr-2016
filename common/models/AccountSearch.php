<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Account;

/**
 * AccountSearch represents the model behind the search form about `common\models\Account`.
 */
class AccountSearch extends Account
{
    public $customer;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['eztorm_user_id', 'customer_exertis_account_number', 'timestamp', 'customer'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Account::find()
                    ->joinWith('customer');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Account::tableName().'.id'        => $this->id,
            'timestamp' => $this->timestamp,
        ]);

        $query->andFilterWhere(['like', 'eztorm_user_id', $this->eztorm_user_id])
            ->andFilterWhere(['like', 'customer_exertis_account_number', $this->customer_exertis_account_number]);

        $joins = array();

        if ($this->customer) {
            $query->andWhere(['LIKE', 'customer.name', $this->customer]);
            $joins['customer'] = function ($q) {
                $q->where('customer.name LIKE "%' . $this->customer . '%"');
            };
        }

        if (!empty($joins))
            $query->joinWith($joins);

        return $dataProvider;
    }

}
