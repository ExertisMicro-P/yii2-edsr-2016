<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "account_rule".
 *
 * @property integer $id
 * @property string $ruleName
 * @property string $ruleQuery
 * @property string $created
 */
class AccountRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created'], 'safe'],
            [['ruleName', 'ruleQuery'], 'required'],
            [['ruleName'], 'string', 'max' => 255],
            [['ruleQuery'], 'string', 'max' => 550],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ruleName' => 'Rule Name',
            'ruleQuery' => 'Rule Query',
            'created' => 'Created',
        ];
    }
}
