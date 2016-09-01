<?php

namespace common\models;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use Yii;

/**
 * This is the model class for table "orderdetails".
 *
 * @property integer $id
 * @property integer $stock_item_id
 * @property string $name
 * @property string $contact
 * @property string $street
 * @property string $town
 * @property string $city
 * @property string $postcode
 * @property string $country
 * @property string $sop
 * @property string $po
 * @property string $orderlineid

 */
class Orderdetails extends \yii\db\ActiveRecord
{
    /**
     * MAX USER PO LENGTH
     * ==================
     * The maximum number of characters a user can use for their purchase order number
     */
    const MAXUSERPOLENGTH = 27 ;

    public $quantity ;
    public $prodcode ;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orderdetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stock_item_id'], 'integer'],
            [['name', 'contact', 'sop','po', 'orderlineid'], 'string', 'max' => 50],
            [['street', 'town', 'city', 'postcode', 'country'], 'string', 'max' => 200]
        ];
    }
    /**
     *
     * @return type
     */
    public function behaviors() {
        return [
            [
                'class' => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
            //Taggable::className(),

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'stock_item_id' => Yii::t('app', 'Stock Item ID'),
            'name' => Yii::t('app', 'Name'),
            'contact' => Yii::t('app', 'Contact'),
            'street' => Yii::t('app', 'Street'),
            'town' => Yii::t('app', 'Town'),
            'city' => Yii::t('app', 'City'),
            'postcode' => Yii::t('app', 'Postcode'),
            'country' => Yii::t('app', 'Country'),
            'sop' => Yii::t('app', 'Sop'),
            'po' => Yii::t('app', 'PO'),
            'orderlineid' => Yii::t('app', 'Orderlineid'),
        ];
    }

    public function getStockitem()
    {
        return $this->hasOne(StockItem::className(), ['id' => 'stock_item_id']);
    }
    
    /**
     * 
     * @return common\models\Account
     */
    public function getAccount(){
        return $this->stockitem->stockroom->account;
    }
    
    public function getCustomer(){
        return $this->hasOne(Customer::className(), ['name' => 'name']);
    }

    /**
     * GET RAW PO
     * ==========
     * Returns the customer purchase order number, after stripping off the
     * internal additions if this was added via the shop.
     *
     * @return string
     */
    public function getrawpo() {
        return (substr($this->po, -10, 4) == 'EDR:') ? substr($this->po, 0, -10) : $this->po ;
    }
    
    public function getOrderline(){
        return $this->hasOne(OrderLineT::className(), ['line_id' => 'orderlineid']);
    }
}
