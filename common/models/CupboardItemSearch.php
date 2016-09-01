<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CupboardItem;

/**
 * CupboardItemSearch represents the model behind the search form about `common\models\CupboardItem`.
 */
class CupboardItemSearch extends CupboardItem
{
    public function rules()
    {
        return [
            [['id', 'cupboard_id', 'digital_product_id'], 'integer'],
            [['timestamp_added'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CupboardItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'cupboard_id' => $this->cupboard_id,
            'digital_product_id' => $this->digital_product_id,
            'timestamp_added' => $this->timestamp_added,
        ]);

        return $dataProvider;
    }
}
