<?php

namespace common\models;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

/**
 * This is the model class for table "testcustomer".
 *
 * @property integer $id
 * @property string $account
 * @property string $status
 * @property string $company
 */
class Testcustomer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'testcustomer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['account'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 3],
            [['company'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => 'Account',
            'status' => 'Status',
            'company' => 'Company',
        ];
    }
    
    static public function truncateMe(){
         truncateTable(self::tableName() );
    }
}
