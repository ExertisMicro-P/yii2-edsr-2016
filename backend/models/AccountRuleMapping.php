<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "account_rule_mapping".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $account_rule_id
 * @property string $assigned
 */
class AccountRuleMapping extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_rule_mapping';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['assigned', 'account_rule_id'], 'safe'],
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
            'account_rule_id' => 'Account Rule ID',
            'assigned' => 'Assigned',
        ];
    }
}
