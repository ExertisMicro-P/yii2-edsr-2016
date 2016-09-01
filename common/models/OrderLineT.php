<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_line_t".
 *
 * @property integer $id
 * @property string $order_number
 * @property string $line_id
 * @property string $line_number
 * @property string $ordered_item
 * @property string $inventory_item_id
 * @property integer $ordered_quantity
 * @property double $tax_value
 * @property double $unit_selling_price
 * @property string $descriptions
 * @property string $status
 * @property string $created
 *
 * @property OrderHeaderT $orderNumber
 */
class OrderLineT extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_line_t';
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
            [['order_number'], 'required'],
            [['order_number', 'line_id', 'line_number', 'inventory_item_id', 'ordered_quantity'], 'integer'],
            [['tax_value', 'unit_selling_price'], 'number'],
            [['created'], 'safe'],
            [['ordered_item'], 'string', 'max' => 500],
            [['descriptions'], 'string', 'max' => 1000],
            [['status'], 'string', 'max' => 50],
            [['order_number'], 'exist', 'skipOnError' => true, 'targetClass' => OrderHeaderT::className(), 'targetAttribute' => ['order_number' => 'order_number']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number' => 'Order Number',
            'line_id' => 'Line ID',
            'line_number' => 'Line Number',
            'ordered_item' => 'Ordered Item',
            'inventory_item_id' => 'Inventory Item ID',
            'ordered_quantity' => 'Ordered Quantity',
            'tax_value' => 'Tax Value',
            'unit_selling_price' => 'Unit Selling Price',
            'descriptions' => 'Descriptions',
            'status' => 'Status',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderNumber()
    {
        return $this->hasOne(OrderHeaderT::className(), ['order_number' => 'order_number']);
    }
}
