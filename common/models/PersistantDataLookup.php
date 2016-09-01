<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "persistant_data_lookup".
 *
 * @property string $name
 * @property string $value
 */
class PersistantDataLookup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'persistant_data_lookup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','value'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'value' => 'Value'
        ];
    }

    static function initData(){
        $pd= PersistantDataLookup::find()
           ->where(['name' => 'ztormCatalogueLookup'])
                            ->one();
        if($pd == null){  //create record.
            $pd = new PersistantDataLookup();
            $pd->name = 'ztormCatalogueLookup';
            $pd->value = '0';
            $pd->save();
        }
    }

    static function getZtormTimeStamp(){
        $pd= PersistantDataLookup::find()
           ->where(['name' => 'ztormCatalogueLookup'])
                            ->one();
        return $pd;
    }

    static function saveZtormCatalogueLookupdate(){
        $pd= PersistantDataLookup::find()
           ->where(['name' => 'ztormCatalogueLookup'])
                            ->one();
        $pd->value = (string)time();
        $pd->save();
    }

    /**
     * serviceAlert should be expressed as a JSON object:
     * e.g. {"type":'success', 'message': Earlier issues have
     * @return boolean
     */
    static function getServiceAlert() {
        $pd= PersistantDataLookup::find()
           ->where(['name' => 'serviceAlert'])
                            ->one();
        if (!empty($pd)) {
            return \yii\helpers\Json::decode($pd['value']);
        } else {
            return false;
        }
    }

}
