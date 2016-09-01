<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "session_order".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $product_id
 * @property string $photo
 * @property string $partcode
 * @property string $description
 * @property integer $quantity
 * @property string $cost
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property DigitalProduct $product
 * @property Account $account
 */
class SessionOrder extends \yii\db\ActiveRecord
{
    public $total ;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'product_id', 'photo', 'partcode', 'description', 'quantity', 'cost'], 'required'],
            [['account_id', 'product_id', 'quantity', 'created_by', 'updated_by'], 'integer'],
            [['cost'], 'number'],
            [['photo', 'partcode'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'product_id' => 'Product ID',
            'photo' => 'Photo',
            'partcode' => 'Partcode',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'cost' => 'Cost',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(DigitalProduct::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }


    public function getAccountName () {
        return $this->account->customer_exertis_account_number ;
    }

}
