<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DigitalProduct;
use common\models\ProductLeafletInfo;

/**
 * DigitalProductSearch represents the model behind the search form about `common\models\DigitalProduct`.
 */
class DigitalProductSearch extends DigitalProduct
{
    public $productName; // support filtering by relation productName in the Shop

    public function rules()
    {
        return [
            [['id', 'is_digital'], 'integer'],
            [['productName'], 'string'],  // support filtering by relation productName in the Shop
            [['partcode', 'description', 'display_price_as', 'fixed_price'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = DigitalProduct::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('productLeafletInfo') ;

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'is_digital' => $this->is_digital,
        ]);

        $query->andFilterWhere(['like', 'digital_product.partcode', $this->partcode])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'display_price_as', $this->display_price_as])
            ->andFilterWhere(['like', 'fixed_price', $this->fixed_price]);


        return $dataProvider;
    }



    /**
     * SEARCH SHOP
     * ===========
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function searchShop($params)
    {
        $query = DigitalProduct::find()
            ->joinWith('productCode_Lookup')
            ->join('LEFT JOIN', 'ztorm_catalogue_cache', '`ztorm_catalogue_cache`.`RealProductId` = `productCode_Lookup`.`product_id`')
            ->where(['enabled' => 1]);
        
        $accRules = \backend\models\AccountRuleMapping::find()->where(['account_id'=>Yii::$app->user->identity->account_id])->one();
        // RCH 20160422
        // handle case when account has no rules associated with it
        if (!empty($accRules)) {
            $accRuleIds = explode(',', $accRules->account_rule_id);

            $rules = '';

            foreach ($accRuleIds as $ruleIds){
                $rules[] = \backend\models\AccountRule::findOne(['id'=>$ruleIds])->ruleQuery;
            }

            $rule = '('.implode(') OR (', $rules).')';
            $query->andWhere($rule);
        } else {
            \Yii::error(__METHOD__.': WARNING! Account '.Yii::$app->user->identity->account_id.' has no AccountRuleMappings set up');
        }
        
        //echo $query->createCommand()->rawSql;
        //\yii\helpers\VarDumper::dump($query->createCommand()->rawSql, 99, true);
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'description' => SORT_ASC
                ]
            ],
            'pagination' => [
                'pagesize' => 30
            ]
        ]);

        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params)
         * statement below
         */
        $dataProvider->setSort([
            'defaultOrder' => [
                'description' => SORT_ASC
            ],
            'attributes'   => [
                'id',
                'partcode',
                'description'
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {

            return $dataProvider;
        }

        if (!empty($this->id)) {
            $query->andFilterWhere([
                'id'              => $this->id,
            ]);
        }

        if (!empty($this->partcode)) {
            $query->andFilterWhere(['like', 'partcode', $this->partcode]) ;
        }


        $joins = array();

//        $joins['product'] = function ($q) {
//            $q->where('product_t.item_code = digital_product.partcode');
//        };

        if ($this->description) {
            $query->andWhere(['LIKE', 'digital_product.description', $this->description]);
        }


        if (!empty($joins)) {
            $query->joinWith($joins);
        }

        // RCH 20151015
        if (!empty($this->productName)) {
            $query->andWhere(['LIKE', 'productCode_Lookup.name', $this->productName]);
        }


        $query->orderBy(['id' => SORT_DESC]);


        return $dataProvider;
    } // search


}
