<?php
namespace common\models;

use Yii;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use yii\db\Expression;

/**
 * This is the model class for EDSR models.
 *
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DigitalProduct $digitalProduct
 */
class BaseModel extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
//                'value'      => function () {
//                    return date("Y-m-d H:i:s");
//                },
                'value' => new Expression('NOW()'),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],

            ],
            'blameable' => [
                'class' => 'yii\behaviors\BlameableBehavior',
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'savewithaudittrail' =>
                [
                    'class' => SaveWithAuditTrailBehavior::className(),
                    'userClass' => '\common\models\gauth\GAUser',
                ]

        ];
    }

    public function getCreated()
    {
        if ($this->created_at) {
            $when = strtotime($this->created_at) ;
            return date('d-m-Y H:i:s', $when) ;
        }
        return null ;
    }

    public function getUpdated()
    {
        if ($this->updated_at) {
            $when = strtotime($this->updated_at) ;
            return date('d-m-Y H:i:s', $when) ;
        }
        return null ;
    }



}
