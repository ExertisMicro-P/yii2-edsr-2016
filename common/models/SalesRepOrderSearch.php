<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SalesRepOrder;

/**
 * StockItemSearch represents the model behind the search form about `common\models\StockItem`.
 */
class SalesRepOrderSearch extends SalesRepOrder
{
    public $accountNumber ;
    public $accountName ;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'created_by'], 'integer'],
            [['accountNumber', 'accountName'], 'string', 'max' => 255],
        ];
    }


    /**
     * SEARCH
     * ======
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SalesRepOrder::find()
                    ->joinWith('account')
                    ->joinWith('orderdetails')
                    ->joinWith('orderdetails.stockitem')
                    ->where(['sales_rep_order.sales_rep_id' => Yii::$app->user->id])
                    ->groupBy('po') ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'timestamp_added' => SORT_DESC,
                    'productcode'     => 'asc'
                ]
            ]
        ]);

        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params)
         * statement below
         */
        $dataProvider->setSort([
            'defaultOrder' => [
                'created_at' => SORT_DESC,
                'account_id' => 'asc',
            ],
            'attributes'   => [
                'id',
                'account_id',
                'created_at',
                'accountnumber',
                'accountname'
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            /**
             * The following line will allow eager loading with digital product
             * data to enable sorting after the initial loading of the grid.
             */

            return $dataProvider;
        }

        $joins = array();

        if (!empty($joins)) {
            $query->joinWith($joins);
        }

        if ($this->accountName) {
            $query->andWhere(['LIKE', 'customer.name', $this->accountName]);
        }

        if ($this->accountNumber) {
            $query->andWhere(['LIKE', 'account.customer_exertis_account_number', $this->accountNumber]);
        }

        $query->orderBy(['created_at' => SORT_DESC]);


        return $dataProvider;
    } // search


}
