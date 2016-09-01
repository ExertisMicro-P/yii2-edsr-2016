<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\InvoiceDetail;
/**
 * InvoiceSearch represents the model behind the search form about `app\models\InvoiceHeader`.
 */
class InvoiceDetailSearch extends InvoiceDetail
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_001', 'id_004', 'id_008', 'id_009', 'id_010'], 'safe'],
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
        $headerRowId = $params['id'];
        $invoiceno = \common\models\InvoiceHeader::findOne(['ih_header_row_id' => $headerRowId])->ih_invoice_number;
        $query = InvoiceDetail::find()->where(['id_invoice_number' => $invoiceno]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize'=>20],
        ]);
        
        
       if (!($this->load($params) && $this->validate())) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'id_001', $this->id_001])
            ->andFilterWhere(['like', 'id_004', $this->id_004])
            ->andFilterWhere(['like', 'id_008', $this->id_008])
            ->andFilterWhere(['like', 'id_009', $this->id_009])
            ->andFilterWhere(['like', 'id_010', $this->id_010]);
        return $dataProvider;
    }
}
