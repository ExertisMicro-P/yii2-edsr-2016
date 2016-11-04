<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_product_mapping".
 *
 * @property integer $id
 * @property string $customer_account_number
 * @property string $customer_partcode
 * @property string $oracle_partcode
 * @property integer $created_at
 * @property integer $updated_at
 */
class CustomerProductMapping extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_product_mapping';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_account_number', 'customer_partcode', 'oracle_partcode', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['customer_account_number'], 'string', 'max' => 20],
            [['customer_partcode', 'oracle_partcode'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_account_number' => 'Customer Account Number',
            'customer_partcode' => 'Customer Partcode',
            'oracle_partcode' => 'Oracle Partcode',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
