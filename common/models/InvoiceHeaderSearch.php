<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\InvoiceHeader;
use yii\web\Session;

/**
 * InvoiceSearch represents the model behind the search form about `app\models\InvoiceHeader`.
 */
class InvoiceHeaderSearch extends InvoiceHeader
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ih_header_row_id', 'ih_invoice_type_int', 'ih_invoice_status_int'], 'integer'],
            [['ih_invoice_number', 'ih_invoice_type', 'ih_account_number', 'ih_001', 'ih_002', 'ih_003', 'ih_004', 'ih_005', 'ih_006', 'ih_007', 'ih_008', 'ih_009', 'ih_010', 'ih_011', 'ih_012', 'ih_013', 'ih_014', 'ih_015', 'ih_016', 'ih_017', 'ih_018', 'ih_019', 'ih_020', 'ih_021', 'ih_022', 'ih_023', 'ih_024', 'ih_025', 'ih_026', 'ih_027', 'ih_028', 'ih_029', 'ih_030', 'ih_031', 'ih_032', 'ih_033', 'ih_034', 'ih_import_date', 'ih_import_line', 'ih_invoice_status', 'active_flag', 'created', 'last_updated'], 'safe'],
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
        
        $user = gauth\GAUser::findById(Yii::$app->user->identity->id);
        $account = $user->account;
        $query = InvoiceHeader::find()->where(['ih_account_number' => $account->customer_exertis_account_number])
                ->andWhere(['LIKE', 'ih_023', 'EDR'])
                ->andWhere(['NOT LIKE', 'ih_invoice_number', '500'])
                ->orderBy('ih_header_row_id DESC');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
	'pagination' => [ 'pageSize'=>20],
        ]);
        
        
       if (!($this->load($params) && $this->validate())) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ih_header_row_id' => $this->ih_header_row_id,
            'ih_import_date' => $this->ih_import_date,
            'ih_invoice_type_int' => $this->ih_invoice_type_int,
            'ih_invoice_status_int' => $this->ih_invoice_status_int,
            'created' => $this->created,
            'last_updated' => $this->last_updated,
        ]);
        
        if(!empty($params['date_range']) && $params['date_range'] != 'Filter By Date'){
            $date_range = $params['date_range'];
            $dates = explode(" ", $date_range);
            $query->andFilterWhere(['between', 'ih_import_date', $dates[0], $dates[2]]);
        }
        
       

        $query->andFilterWhere(['like', 'ih_invoice_number', $this->ih_invoice_number])
            ->andFilterWhere(['like', 'ih_invoice_type', $this->ih_invoice_type])
            ->andFilterWhere(['like', 'ih_account_number', $this->ih_account_number])
            ->andFilterWhere(['like', 'ih_001', $this->ih_001])
            ->andFilterWhere(['like', 'ih_002', $this->ih_002])
            ->andFilterWhere(['like', 'ih_003', $this->ih_003])
            ->andFilterWhere(['like', 'ih_004', $this->ih_004])
            ->andFilterWhere(['like', 'ih_006', $this->ih_006])
            ->andFilterWhere(['like', 'ih_007', $this->ih_007])
            ->andFilterWhere(['like', 'ih_008', $this->ih_008])
            ->andFilterWhere(['like', 'ih_009', $this->ih_009])
            ->andFilterWhere(['like', 'ih_010', $this->ih_010])
            ->andFilterWhere(['like', 'ih_011', $this->ih_011])
            ->andFilterWhere(['like', 'ih_012', $this->ih_012])
            ->andFilterWhere(['like', 'ih_013', $this->ih_013])
            ->andFilterWhere(['like', 'ih_014', $this->ih_014])
            ->andFilterWhere(['like', 'ih_015', $this->ih_015])
            ->andFilterWhere(['like', 'ih_016', $this->ih_016])
            ->andFilterWhere(['like', 'ih_017', $this->ih_017])
            ->andFilterWhere(['like', 'ih_018', $this->ih_018])
            ->andFilterWhere(['like', 'ih_019', $this->ih_019])
            ->andFilterWhere(['like', 'ih_020', $this->ih_020])
            ->andFilterWhere(['like', 'ih_021', $this->ih_021])
            ->andFilterWhere(['like', 'ih_022', $this->ih_022])
            ->andFilterWhere(['like', 'ih_023', $this->ih_023])
            ->andFilterWhere(['like', 'ih_024', $this->ih_024])
            ->andFilterWhere(['like', 'ih_025', $this->ih_025])
            ->andFilterWhere(['like', 'ih_026', $this->ih_026])
            ->andFilterWhere(['like', 'ih_027', $this->ih_027])
            ->andFilterWhere(['like', 'ih_028', $this->ih_028])
            ->andFilterWhere(['like', 'ih_029', $this->ih_029])
            ->andFilterWhere(['like', 'ih_030', $this->ih_030])
            ->andFilterWhere(['like', 'ih_031', $this->ih_031])
            ->andFilterWhere(['like', 'ih_032', $this->ih_032])
            ->andFilterWhere(['like', 'ih_033', $this->ih_033])
            ->andFilterWhere(['like', 'ih_034', $this->ih_034])
            ->andFilterWhere(['like', 'ih_import_line', $this->ih_import_line])
            ->andFilterWhere(['like', 'ih_invoice_status', $this->ih_invoice_status])
            ->andFilterWhere(['like', 'active_flag', $this->active_flag]);

        return $dataProvider;
    }
}
