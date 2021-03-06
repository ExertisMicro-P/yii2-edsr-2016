<?php

namespace common\models\gauth;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\gauth\GAUser;

/**
 * GAUserSearch represents the model behind the search form about `common\models\gauth\GAUser`.
 */
class GAUserSearch extends GAUser
{
    public $account;

    public function rules()
    {
        return [
            [['id', 'role_id', 'status', 'account_id'], 'integer'],
            [['email', 'new_email', 'username', 'password', 'auth_key', 'api_key', 'login_ip', 'login_time', 'create_ip', 'create_time', 'update_time', 'ban_time', 'ban_reason', 'uuid', 'account'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GAUser::find();
        $query->joinWith(['account']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'role_id' => $this->role_id,
            'status' => $this->status,
            'login_time' => $this->login_time,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'ban_time' => $this->ban_time,
            'account_id' => $this->account_id,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'new_email', $this->new_email])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'login_ip', $this->login_ip])
            ->andFilterWhere(['like', 'create_ip', $this->create_ip])
            ->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
            ->andFilterWhere(['like', 'uuid', $this->uuid]);

        return $dataProvider;
    }
}
