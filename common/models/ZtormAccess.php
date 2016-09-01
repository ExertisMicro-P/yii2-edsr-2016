<?php

namespace common\models;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use Yii;

/**
 * This is the model class for table "productcode_lookup".
 *
 * @property integer $id
 * @property integer $storeid
 * @property string $storealias
 * @property string $url
 * @property string $type
 */
class ZtormAccess extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ztormaccess';
    }

    public function behaviors() {
        return [
            [
                'class' => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
           // Taggable::className(),

        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'storeid'], 'integer'],
            [['type','keycode','url','storealias'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'storeid' => 'store id',
            'type' => 'type',
            'storealias' => 'store Alias',
            'url'=>'url'
        ];
    }
    
    public function getstorekey(){
        return $this->keycode;
    }
    public function gettype(){
        return $this->type;
    }
    public function getstoreID(){
        return $this->storeid;
    }
    public function getstorealias(){
        return $this->storealias;
    }
    public function geturl(){
        return $this->url;
    }
    
}
