<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SalesRepOrder;

/**
 * StockItemSearch represents the model behind the search form about `common\models\StockItem`.
 */
class SessionOrderSearch extends SessionOrder
{
    public $accountName ;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'product_id', 'quantity', 'created_by', 'updated_by'], 'integer'],
            [['cost'], 'number'],
            [['photo', 'partcode', 'accountName'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 500]
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
        $query = SessionOrder::find()
                        ->where(['sales_rep_id' => Yii::$app->user->id])
                        ->joinWith('account')
                        ->joinWith('product');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                    'partcode'   => 'asc',
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
                'updated_at' => SORT_DESC
            ],
            'attributes'   => [
                'id',
                'account',

                'updated_at'
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

        if ($this->partcode) {
            $query->andWhere(['LIKE', 'session_order.partcode', $this->partcode]);
        }
        if ($this->description) {
            $query->andWhere(['LIKE', 'session_order.description', $this->description]);
        }
        if ($this->accountName) {
            $query->andWhere(['LIKE', 'account.customer_exertis_account_number', $this->accountName]);
        }

        if (!empty($joins)) {
            $query->joinWith($joins);
        }

        $query->orderBy(['account.customer_exertis_account_number' => SORT_DESC]);


        return $dataProvider;
    } // search

}
