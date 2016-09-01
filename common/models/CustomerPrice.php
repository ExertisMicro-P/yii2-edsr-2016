<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_prices".
 *
 * @property integer $id
 * @property string $account
 * @property string $item_code
 * @property string $item_price
 */
class CustomerPrice extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_prices';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('customerPricesDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_price'], 'number'],
            [['account'], 'string', 'max' => 20],
            [['item_code'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => 'Account',
            'item_code' => 'Item Code',
            'item_price' => 'Item Price',
        ];
    }
}
