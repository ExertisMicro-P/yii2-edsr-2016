<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pricat_t".
 *
 * @property integer $id
 * @property string $account_number
 * @property string $item_code
 * @property string $customer_item_code
 * @property double $sell_price
 * @property string $active_flag
 * @property string $created
 * @property string $last_updated
 */
class Pricat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pricat_t';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('creditDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sell_price'], 'number'],
            [['created', 'last_updated'], 'safe'],
            [['account_number'], 'string', 'max' => 50],
            [['item_code', 'customer_item_code'], 'string', 'max' => 500],
            [['active_flag'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_number' => 'Account Number',
            'item_code' => 'Item Code',
            'customer_item_code' => 'Customer Item Code',
            'sell_price' => 'Sell Price',
            'active_flag' => 'Active Flag',
            'created' => 'Created',
            'last_updated' => 'Last Updated',
        ];
    }
}
