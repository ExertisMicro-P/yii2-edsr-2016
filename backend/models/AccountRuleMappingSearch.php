<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AccountRuleMapping;

/**
 * AccountRuleMappingSearch represents the model behind the search form about `backend\models\AccountRuleMapping`.
 */
class AccountRuleMappingSearch extends AccountRuleMapping
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'account_rule_id'], 'string'],
            [['assigned'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        if(isset($params['account'])){
            $query = AccountRuleMapping::find()->where(['account_id'=>$params['account']]);
        } else {
            $query = AccountRuleMapping::find();
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'account_id' => $this->account_id,
            'account_rule_id' => $this->account_rule_id,
            'assigned' => $this->assigned,
        ]);

        return $dataProvider;
    }
}
