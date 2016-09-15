<?php

namespace common\models;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use Yii;

/**
 * This is the model class for table "productcode_lookup".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 */
class ProductcodeLookup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productcode_lookup';
    }

    public function behaviors() {
        return [
            [
                'class' => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
           // Taggable::className(),

        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'eztorm_store_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['storealias', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'eZtorm Product ID',
            'name' => 'Name',
            'eztorm_store_id' => 'eZtorm Store ID'
        ];
    }

    public function getProductId(){
        return $this->productId;
    }

    /**
     * Builds and executes a SQL statement for truncating the DB table.
     * @param string $table the table to be truncated. The name will be properly quoted by the method.
     */
    public function truncateTable()
    {
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();
        $this->db->createCommand()->truncateTable(self::tableName())->execute();
        Yii::trace('Dropped table before file processing' .__METHOD__.':'.__LINE__);
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();
    }

    static function findAndReturnModel($productId){
         $item = ProductcodeLookup::findOne(['product_id'=>$productId]);
         if(!isset($item)){
            $item = new ProductcodeLookup();
         }
        return $item;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        $isConsole = Yii::$app instanceof \yii\console\Application ;

        // -------------------------------------------------------------
        // RCH 20160202
        // check cached value in session as this gets hit too many times
        // -------------------------------------------------------------
        if (!$isConsole) {
            $session = Yii::$app->session;
            $session->open();
            if (!empty($session['ZtormAccessStore'])) {
                // use cached value
                return $session['ZtormAccessStore'];
            }
        }

        $type = \Yii::$app->params['storeType'];
       // $store = $item = ZtormAccess::findOne(['storealias'=>$this->storealias,'name'=>$type]);
        $result =             $this->hasOne(ZtormAccess::className(), [
            'storealias'=>'storealias'
            ])->andWhere(ZtormAccess::tableName() .'.type = :type',[':type'=>$type]);

        // ---------------------------------------------------------------
        // cache this value if possible
        // ---------------------------------------------------------------
        if (!$isConsole) {
            $session['ZtormAccessStore'] = $result;
        }
        return $result;
    }


    public function getstock_item(){
        return $this->hasOne(StockItem::className(), ['eztorm_product_id'=>'product_id'])->andWhere(['LIKE', 'stock_item.status', '#']);
    }

    public function getdigital_product(){
        return $this->hasOne(DigitalProduct::className(), ['eztorm_id'=>'id']);
    }
}
