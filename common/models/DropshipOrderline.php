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
            [['dropship_id', 'customer_partcode', 'oracle_partcode', 'quantity', 'price'], 'required'],
            [['dropship_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
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
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted At',
            'deleted_by' => 'Deleted By',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDropshipOrderlines()
    {
        return $this->hasMany(DropshipOrderline::className(), ['dropship_id' => 'id']);
    }


}
