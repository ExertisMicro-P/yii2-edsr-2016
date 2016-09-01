<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StockItem;

/**
 * StockItemSearch represents the model behind the search form about `common\models\StockItem`.
 */
class StockItemSearch extends StockItem
{

    public $description;
    public $sop, $orderdetailspo;
    public $customerExertisAccountNumber, $customerName;
    public $emailedUserName;
    public $emailedUserAddress;

    public function rules()
    {
        return [
            [['id', 'stockroom_id'], 'integer'],
            [['status', 'productcode', 'description', 'customerExertisAccountNumber', 'orderdetailspo'], 'string'],
            [['sop','eztorm_order_id'], 'integer'],
            [['timestamp_added', 'productcode', 'description', 'sop', 'customerExertisAccountNumber', 'orderdetailspo', 'customerName'], 'safe'],
            [['emailedUserName'], 'safe'],
            [['emailedUserAddress'], 'safe'],
        ];
    }

    /**
     * SCENARIOS
     * =========
     *
     * @return array
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = StockItem::find()->where(['status' => [StockItem::STATUS_NOT_PURCHASED, StocKItem::STATUS_PURCHASED]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'timestamp_added' => SORT_DESC,
                    'productcode'     => 'asc',
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
                'timestamp_added' => SORT_DESC,
                'productcode'     => 'asc',
            ],
            'attributes'   => [
                'id',
                'status',
                'stockroom_id',
                'productcode',
                'timestamp_added',

                'orderdetailspo' => [
                    'asc'   => ['orderdetails.po' => SORT_ASC],
                    'desc'  => ['orderdetails.po' => SORT_DESC],
                    'label' => 'PO',
                ],


                'description'    => [
                    'asc'   => ['digital_product.description' => SORT_ASC],
                    'desc'  => ['digital_product.description' => SORT_DESC],
                    'label' => 'Description'
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            /**
             * The following line will allow eager loading with digital product
             * data to enable sorting after the initial loading of the grid.
             */
            $query->joinWith(['digitalProduct']);

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'              => $this->id,
            'stockroom_id'    => $this->stockroom_id,
            'timestamp_added' => $this->timestamp_added,
            //'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'productcode', $this->productcode]);
        
        $query->andFilterWhere(['stock_item.status' => $this->status]);
        
        $query->andFilterWhere(['like', 'eztorm_order_id', $this->eztorm_order_id]);
        
        // RCH 2015
        // ignore any which are spare and waiting to be re-used
        $query->andFilterWhere(['not in', 'spare', [StockItem::KEY_SPARE, StockItem::KEY_HIDDEN_FROM_ALL]]);
        
        // RCH 20160229
        // Allow purchase but hide from customer - only visible to Russell
        if (Yii::$app->user->id==10) {
            $query->orWhere(['=', 'spare', StockItem::KEY_HIDDEN_FROM_ALL_EXCEPT_RUSSELL]);                     
        } else {
            $query->andWhere(['not', ['spare' => StockItem::KEY_HIDDEN_FROM_ALL_EXCEPT_RUSSELL]]);            
        }



        $joins = array();

//        $joins['product'] = function ($q) {
//            $q->where('product_t.item_code LIKE "%' . $this->productcode . '%"');
//        };

        if ($this->productcode) {
            $joins['digitalProduct'] = function ($q) {
                $q->where('digital_product.partcode LIKE "%' . $this->productcode . '%"');
            };
        }

        $query->andFilterWhere(['LIKE', 'digital_product.description', $this->description]);

        $joins[] = 'orderdetails';
        if ($this->orderdetailspo) {
            $query->andWhere(['LIKE', 'orderdetails.po', $this->orderdetailspo]);
        }


        if (!empty($joins)) {
            $query->joinWith($joins);
        }

        $query->orderBy(['id' => SORT_DESC]);


        return $dataProvider;
    } // search


    /**
     * SEARCH GROUPED
     * ==============
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function searchGrouped($params)
    {
        $query = StockItem::find()->groupBy('productcode'); // ->with('digitalProduct');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params)
         * statement below
         */
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'status',
                'stockroom_id',
                'productcode',
                'timestamp_added',

                'orderdetailspo' => [
                    'asc'   => ['orderdetails.po' => SORT_ASC],
                    'desc'  => ['orderdetails.po' => SORT_DESC],
                    'label' => 'PO',
                ],
                'description'    => [
                    'asc'   => ['digital_product.description' => SORT_ASC],
                    'desc'  => ['digital_product.description' => SORT_DESC],
                    'label' => 'Description'
                ]
            ]
        ]);
        if (!($this->load($params) && $this->validate())) {
            /**
             * The following line will allow eager loading with digital product
             * data to enable sorting after the initial loading of the grid.
             */
            $query->joinWith(['digitalProduct']);

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'              => $this->id,
            'stockroom_id'    => $this->stockroom_id,
            'timestamp_added' => $this->timestamp_added,
            'status'          => $this->status,
        ]);

        $query->andFilterWhere(['like', 'productcode', $this->productcode]);


        $joins['digitalProduct'] = function ($q) {
            $q->where('digital_product.partcode LIKE "%' . $this->productcode . '%"');
        };

        if ($this->description) {
            $query->andWhere(['LIKE', 'digital_product.description', $this->description]);
        }

        $joins[] = 'orderdetails';
        if ($this->orderdetailspo) {
            $query->andWhere(['LIKE', 'orderdetails.po', $this->orderdetailspo]);
        }
        
        // RCH 2015
        // ignore any which are spare and waiting to be re-used
        $query->andWhere(['not', ['spare' => StockItem::KEY_SPARE]]);
        $query->andWhere(['not', ['spare' => StockItem::KEY_HIDDEN_FROM_ALL]]);
        
        // RCH 20160229
        // Allow purchase but hide from customer - only visible to Russell
        if (Yii::$app->user->id==10) {
            $query->orWhere(['not', ['spare' => StockItem::KEY_HIDDEN_FROM_ALL_EXCEPT_RUSSELL]]);         
            
        }

        if (!empty($joins))
            $query->joinWith($joins);


        return $dataProvider;
    } // searchGrouped

    /**
     * SEARCH EMAILED
     * ==============
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function searchEmailed($params)
    {
        $query = StockItem::find()->with('emailedUser')
            ->where(['NOT IN', 'stock_item.status', [StockItem::STATUS_PURCHASED, StockItem::STATUS_NOT_PURCHASED]])->orderBy('timestamp_added DESC'); //16052016 dominikjaross ->groupBy('status') removed.

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'timestamp_added '      => SORT_DESC
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
                'usedAt' => SORT_DESC
            ],
            'attributes' => [
                'id',
                'stockroom_id',
                'status',
                'productcode',
                'description',
                'description'        => [
                    'asc'   => ['digital_product.description' => SORT_ASC],
                    'desc'  => ['digital_product.description' => SORT_DESC],
                    'label' => 'Description'
                ],
                'emailedUserName'    => [
                    'asc'   => ['emailed_user.name' => SORT_ASC],
                    'desc'  => ['emailed_user.name' => SORT_DESC],
                    'label' => 'Emailed To...'
                ],
                'emailedUserAddress' => [
                    'asc'   => ['emailed_user.email' => SORT_ASC],
                    'desc'  => ['emailed_user.email' => SORT_DESC],
                    'label' => 'Emailed Adr'
                ],
                'usedAt'             => [
                    'asc'   => ['emailed_user.created_at' => SORT_ASC],
                    'desc'  => ['emailed_user.created_at' => SORT_DESC],
                    'label' => 'Used At'
                ],
                'rawpo'              => [
                    'asc'   => ['orderdetails.po' => SORT_ASC],
                    'desc'  => ['orderdetails.po' => SORT_DESC],
                    'label' => 'PO'
                ],
            ]
        ]);


        if (!($this->load($params) && $this->validate())) {
            /**
             * The following line will allow eager loading with digital product
             * data to enable sorting after the initial loading of the grid.
             */
            $query->joinWith(['digitalProduct']);

            $query->joinWith(['emailedUser']);

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'              => $this->id,
            'stockroom_id'    => $this->stockroom_id,
            'timestamp_added' => $this->timestamp_added,
//            'status'          => $this->status,
        ]);
        
        // RCH 2015
        // ignore any which are spare and waiting to be re-used
        $query->andWhere(['not', ['spare' => StockItem::KEY_SPARE]]);
        $query->andWhere(['not', ['spare' => StockItem::KEY_HIDDEN_FROM_ALL]]);
        
        // RCH 20160229
        // Allow purchase but hide from customer - only visible to Russell
        if (Yii::$app->user->id==10) {
            $query->orWhere(['=', 'spare', StockItem::KEY_HIDDEN_FROM_ALL_EXCEPT_RUSSELL]);         
            
        }


        $joins = array();

        $query->andFilterWhere(['like', 'productcode', $this->productcode])
            ->andFilterWhere(['LIKE', 'status', $this->status]);

        if ($this->description) {
            $query->andWhere(['LIKE', 'digital_product.description', $this->description]);
        }

        $joins[] = 'emailedUser';
        if ($this->emailedUserName) {
            $query->joinWith(['emailedUser' => function ($q) {
                $q->where('emailed_user.name LIKE "%' . $this->emailedUserName . '%"');
            }]);
        }

        if ($this->emailedUserAddress) {
            $query->joinWith(['emailedUser' => function ($q) {
                $q->where('emailed_user.email LIKE "%' . $this->emailedUserAddress . '%"');
            }]);
        }

        $joins[] = 'orderdetails';
        if ($this->orderdetailspo) {
            $query->andWhere(['LIKE', 'orderdetails.po', $this->orderdetailspo]);
        }


        // -------------------------------------------------------------------
        // NOTE: ***** Without the following join condition, the attempt to
        // ***** read from partcode or description fails
        // ***** The basic joinWith doesn't solve this
        // -------------------------------------------------------------------
        $query->joinWith(['digitalProduct' => function ($q) {
            $q->where('digital_product.partcode LIKE "%' . $this->productcode . '%"');
        }]);


        if (!empty($joins)) {
            $query->joinWith($joins);
        }

        return $dataProvider;

    } // searchEmailed


    /**
     * SEARCH SALES
     * ============
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function searchSales($params)
    {

        $query = StockItem::find(); // ->with('digitalProduct');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params)
         * statement below
         */
        $dataProvider->setSort([
                'defaultOrder' => ['id' => SORT_DESC],

                'attributes'   => [
                    'id'                           => ['default' => SORT_ASC],
                    'productcode',
                    'status',

                    'description'                  => [
                        'asc'   => ['digital_product.description' => SORT_ASC],
                        'desc'  => ['digital_product.description' => SORT_DESC],
                        'label' => 'Description'
                    ],

                    'sop'                          => [
                        'asc'   => ['orderdetails.sop' => SORT_ASC],
                        'desc'  => ['orderdetails.sop' => SORT_DESC],
                        'label' => 'SOP',
                    ],

                    'orderdetailspo'               => [
                        'asc'   => ['orderdetails.po' => SORT_ASC],
                        'desc'  => ['orderdetails.po' => SORT_DESC],
                        'label' => 'PO',
                    ],

                    'customerExertisAccountNumber' => [
                        'asc'   => ['customer.exertis_account_number' => SORT_ASC],
                        'desc'  => ['customer.exertis_account_number' => SORT_DESC],
                        'label' => 'Account',
                    ],
                    'customerName'                 => [
                        'asc'   => ['customer.name' => SORT_ASC],
                        'desc'  => ['customer.name' => SORT_DESC],
                        'label' => 'Account Name',

                    ],
                    'timestamp_added',
                ]
            ]
        );

        if (!($this->load($params) && $this->validate())) {
            /**
             * The following line will allow eager loading with digital product
             * data to enable sorting after the initial loading of the grid.
             */
            $query->joinWith(['digitalProduct', 'orderdetails', 'stockroom.account', 'stockroom.account.customer']);

            return $dataProvider;
        }

        $query->andFilterWhere([
            'stock_item.id'   => $this->id,
            'stockroom_id'    => $this->stockroom_id,
            'timestamp_added' => $this->timestamp_added,
        ]);

        $query->andFilterWhere(['like', 'productcode', $this->productcode])
            ->andFilterWhere(['like', 'stock_item.status', $this->status]);


        $joins = array();

        $joins['digitalProduct'] = function ($q) {
            $q->where('digital_product.partcode LIKE "%' . $this->productcode . '%"');
        };

        if ($this->description) {
            $query->andWhere(['LIKE', 'digital_product.description', $this->description]);
        }

        $joins[] = 'orderdetails';
        if ($this->sop) {
            $query->andWhere(['LIKE', 'orderdetails.sop', $this->sop]);
        }
        if ($this->orderdetailspo) {
            $query->andWhere(['LIKE', 'orderdetails.po', $this->orderdetailspo]);
        }
        $joins[] = 'stockroom.account';

        if ($this->customerExertisAccountNumber) {
            $query->andWhere(['LIKE', 'account.customer_exertis_account_number', $this->customerExertisAccountNumber]);
        }

        $joins[] = 'stockroom.account.customer';
        if ($this->customerName) {
            $query->andWhere(['LIKE', 'customer.name', $this->customerName]);
        }


        if (!empty($joins)) {
            $query->joinWith($joins);
        }

        return $dataProvider;
    } // searchSales
}
