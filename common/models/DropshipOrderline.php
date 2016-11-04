<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dropship_orderline".
 *
 * @property integer $id
 * @property integer $dropship_id
 * @property string $customer_partcode
 * @property string $oracle_partcode
 * @property string $quantity
 * @property double $price
 * @property integer $created_at
 * @property integer $updated_at
 */
class DropshipOrderline extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dropship_orderline';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dropship_id', 'customer_partcode', 'oracle_partcode', 'quantity', 'price', 'created_at', 'updated_at'], 'required'],
            [['dropship_id', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['customer_partcode', 'oracle_partcode', 'quantity'], 'string', 'max' => 255],
            [['dropship_id'], 'exist', 'skipOnError' => true, 'targetClass' => DropshipEmailDetails::className(), 'targetAttribute' => ['dropship_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dropship_id' => 'Dropship ID',
            'customer_partcode' => 'Customer Partcode',
            'oracle_partcode' => 'Oracle Partcode',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
