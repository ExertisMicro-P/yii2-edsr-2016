<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EmailedItem;

/**
 * EmailedItemSearch represents the model behind the search form about `common\models\EmailedItem`.
 */
class EmailedItemSearch extends EmailedItem
{
    
    public $emailedUser;
    public $emailedUserEmail;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'emailed_user_id', 'stock_item_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'emailedUser', 'emailedUserEmail'], 'safe'],
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
        $query = EmailedItem::find()->where(['stock_item_id'=>$params['id']])->joinWith('emailedUser');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['emailedUser'] = [
            'asc' => ['emailed_user.name' => SORT_ASC],
            'desc' => ['emailed_user.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['emailedUserEmail'] = [
            'asc' => ['emailed_user.email' => SORT_ASC],
            'desc' => ['emailed_user.email' => SORT_DESC],
        ];
        

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        //var_dump($this->id); die();
        
        $query->andFilterWhere(['like', 'emailed_user.name', $this->emailedUser])
              ->andFilterWhere(['like', 'emailed_user.email', $this->emailedUserEmail]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'stock_item_id' => $this->stock_item_id,
        ]);
        

        return $dataProvider;
    }
}
