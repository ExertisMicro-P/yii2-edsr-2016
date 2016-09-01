<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EDSRRole;

/**
 * RoleSearch represents the model behind the search form about `amnah\yii2\user\models\Role`.
 */
class RoleSearch extends EDSRRole
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['can_admin','can_setupuseremail','can_user','can_customer','can_monitor_sales'], 'boolean'],
            [['name', 'create_time', 'update_time'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = EDSRRole::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'can_admin' => $this->can_admin,
            'can_setupuseremail' => $this->can_setupuseremail,
            'can_user' => $this->can_user,
            'can_customer' => $this->can_customer,
            'can_monitor_sales' => $this->can_monitor_sales,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
