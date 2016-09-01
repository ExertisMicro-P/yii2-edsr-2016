<?php

namespace common\models;

use Yii;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;


/**
 * This is the model class for table "emailed_item".
 *
 * @property integer $id
 * @property integer $emailed_user_id
 * @property integer $stock_item_id
 * @property integer $quantity
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property StockItem $stockItem
 * @property EmailedUser $emailedUser
 */
class EmailedItem extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emailed_item';
    }

    /**
     * BEHAVIOURS
     * ==========
     *
     * @return array
     */
    public function behaviors() {
        return [
            [
                'class' => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['emailed_user_id', 'quantity'], 'required'],
            [['emailed_user_id', 'stock_item_id', 'quantity', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emailed_user_id' => 'Emailed User ID',
            'stock_item_id' => 'Stock Item ID',
            'quantity' => 'Quantity',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockItem()
    {
        return $this->hasOne(StockItem::className(), ['id' => 'stock_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailedUser()
    {
        return $this->hasOne(EmailedUser::className(), ['id' => 'emailed_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailedUserEmail()
    {
        return $this->hasOne(EmailedUser::className(), ['id' => 'emailed_user_id']);
    }
}
