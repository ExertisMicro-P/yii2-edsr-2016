<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use exertis\savewithaudittrail\models\Audittrail;

/**
 * AudittrailSearch represents the model behind the search form about `exertis\savewithaudittrail\models\Audittrail`.
 */
class AudittrailSearch extends Audittrail
{
    public function rules()
    {
        return [
            [['id', 'record_id'], 'integer'],
            [['table_name', 'message', 'timestamp', 'username'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Audittrail::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'record_id' => $this->record_id,
            'timestamp' => $this->timestamp,
        ]);

        $query->andFilterWhere(['like', 'table_name', $this->table_name])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'username', $this->username]);

        $query->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
}
