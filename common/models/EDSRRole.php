<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_role".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $create_time
 * @property string  $update_time
 * @property integer $can_admin
 * @property integer $can_setupuseremail
 *
 */
class EDSRRole extends \amnah\yii2\user\models\Role
{

    const ROLE_ADMIN = 1;
    const ROLE_MAINUSER = 2;
    const ROLE_SUBUSER = 3;
    const CUSTOMER_EMAIL_SETUP = 4;
    const ROLE_INTERNAL = 5;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            //            [['create_time', 'update_time'], 'safe'],
            [['can_admin', 'can_setupuseremail', 'can_user', 'can_customer', 'can_monitor_sales'], 'integer'], // RCH 20141210 This is why we need EDSRRole
            [['can_admin', 'can_setupuseremail', 'can_user', 'can_customer', 'can_monitor_sales'], 'default', 'value' => 0], // RCH 20141210 This is why we need EDSRRole
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => Yii::t('user', 'ID'),
            'name'               => Yii::t('user', 'Name'),
            'create_time'        => Yii::t('user', 'Create Time'),
            'update_time'        => Yii::t('user', 'Update Time'),
            'can_admin'          => Yii::t('user', 'Can Admin'),
            'can_setupuseremail' => Yii::t('user', 'Can Setup User Email'), // RCH 20141210 This is why we need EDSRRole
            'can_user'           => Yii::t('user', 'Is a standard user'), // RCH 20141210 This is why we need EDSRRole
            'can_customer'       => Yii::t('user', 'Is a customer user'), // RCH 20141210 This is why we need EDSRRole
            'can_monitor_sales'  => Yii::t('user', 'Is a Sales Monitor user'), // RCH 20141210 This is why we need EDSRRole
        ];
    }


    public function behaviors()
    {
        parent::behaviors();

        return [
            [
                'class'     => \exertis\savewithaudittrail\SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
        ];
    } // behaviors
}
