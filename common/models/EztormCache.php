<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "eztorm_cache".
 *
 * @property integer $id
 * @property string $partcode
 * @property string $eztorm_id
 * @property string $valid_until
 * @property string $Name
 * @property string $Category
 * @property string $Format
 * @property string $Publisher
 * @property string $InformationFull
 * @property string $Requirements
 * @property integer $PEGI_Age_Others
 * @property string $Boxshot
 * @property string $Screenshots
 * @property string $Genres
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * 
 * @property object $RRP 
 * @property object $RRPCurrency 
 */
class EztormCache extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'eztorm_cache';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eztorm_id'], 'required'],
            [['valid_until', 'created_at', 'updated_at'], 'safe'],
            [['InformationFull'], 'string'],
            [['PEGI_Age_Others', 'created_by', 'updated_by'], 'integer'],
            [['partcode', 'eztorm_id', 'Name', 'Category', 'Format', 'Publisher', 'Requirements', 'Boxshot', 'Screenshots', 'Genres', 'RRP', 'RRPCurrency'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partcode' => 'Partcode',
            'eztorm_id' => 'Eztorm ID',
            'valid_until' => 'Valid Until',
            'Name' => 'Name',
            'Category' => 'Category',
            'Format' => 'Format',
            'Publisher' => 'Publisher',
            'InformationFull' => 'Information Full',
            'Requirements' => 'Requirements',
            'PEGI_Age_Others' => 'Pegi  Age  Others',
            'Boxshot' => 'Boxshot',
            'RRP' => 'RRP',
            'RRPCurrency' => 'RRP Currency',
            'Screenshots' => 'Screenshots',
            'Genres' => 'Genres',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}
