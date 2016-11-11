<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dropship_email_details".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $po
 * @property string $email
 * @property string $timestamp
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class DropshipEmailDetails extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dropship_email_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'po', 'email'], 'required'],
            [['account_id', 'created_by', 'updated_by'], 'integer'],
            [['timestamp'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['po', 'email'], 'string', 'max' => 255],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],

            // the email attribute should be a valid email address
            ['email', 'email'],
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
            'po' => 'Po',
            'email' => 'Email',
            'timestamp' => 'Timestamp',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
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
