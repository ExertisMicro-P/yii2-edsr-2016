<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "session_delivering".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $session_id
 * @property integer $stockitem_id
 * @property string $photo
 * @property string $partcode
 * @property string $description
 * @property integer $quantity
 * @property string $po
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property StockItem $stockitem
 * @property Account $account
 */
class SessionDelivering extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session_delivering';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'session_id', 'stockitem_id', 'photo', 'partcode', 'description', 'quantity'], 'required'],
            [['account_id', 'stockitem_id', 'quantity', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['session_id'], 'string', 'max' => 128],
            [['photo', 'po'], 'string', 'max' => 255],
            [['partcode'], 'string', 'max' => 20],
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
            'session_id' => 'Session ID',
            'stockitem_id' => 'Stockitem ID',
            'photo' => 'Photo',
            'partcode' => 'Partcode',
            'description' => 'Description',
            'quantity' => 'Quantity',
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
    public function getStockitem()
    {
        return $this->hasOne(StockItem::className(), ['id' => 'stockitem_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }
}
