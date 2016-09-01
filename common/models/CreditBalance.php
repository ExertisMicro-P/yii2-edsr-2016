<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "credit_balance".
 *
 * @property integer $id
 * @property string $account
 * @property string $credit_balance
 * @property string $credit_limit
 * @property string $created_at
 * @property string $updated_at
 */
class CreditBalance extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'credit_limit_t';
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
            [['available_credit', 'overall_credit_limit'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['account_number'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_number' => 'Account',
            'available_credit' => 'Credit Balance',
            'overall_credit_limit' => 'Credit Limit',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
