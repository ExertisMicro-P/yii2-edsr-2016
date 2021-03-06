<?php

namespace common\models;

use Yii;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;

/**
 * This is the model class for table "emailed_user".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $email
 * @property string $name
 * @property string $order_number
 * @property string $created_at
 * @property string $updated_at
 *
 * @property EmailedItem[] $emailedItems
 * @property User $user
 */
class EmailedUser extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emailed_user';
    }

    /**
     * BEHAVIOURS
     * ==========
     *
     * @return array
     */
    public function behaviors() {
        return parent::behaviors() ;
        return [
            array_merge($current,
            [
                'savewithaudittrail' =>
                [
                    'class' => SaveWithAuditTrailBehavior::className(),
                    'userClass' => '\common\models\gauth\GAUser',
                ]
            ]
            )
        ] ;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['email', 'name', 'order_number'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'email' => 'Email',
            'name' => 'Name',
            'order_number' => 'Order Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailedItems()
    {
        return $this->hasMany(EmailedItem::className(), ['emailed_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
