<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_tag".
 *
 * @property integer $id
 * @property integer $frequency
 * @property string $name
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'frequency' => Yii::t('app', 'Frequency'),
            'name' => Yii::t('app', 'Name'),
        ];
    }
}
