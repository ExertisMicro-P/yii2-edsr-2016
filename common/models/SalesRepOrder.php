<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sales_rep_order".
 *
 * @property integer $id
 * @property integer $sales_rep_id
 * @property integer $account_id
 * @property string $po
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property User $salesRep
 * @property Account $account
 */
class SalesRepOrder extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales_rep_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_rep_id', 'account_id', 'po'], 'required'],
            [['sales_rep_id', 'account_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['po'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sales_rep_id' => 'Sales Rep ID',
            'account_id' => 'Account ID',
            'po' => 'Po',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesRep()
    {
        return $this->hasOne(User::className(), ['id' => 'sales_rep_id']);
    }


    public function getOrderdetails() {
        return $this->hasMany(Orderdetails::className(), ['po' => 'po']) ;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessionOrder()
    {
        return $this->hasOne(SessionOrder::className(), ['sales_rep_id' => 'sales_rep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }


    public function getAccountNumber () {
        return $this->account->customer_exertis_account_number ;
    }

    public function getAccountName() {
        return $this->account->customer->name ;;
    }


}
