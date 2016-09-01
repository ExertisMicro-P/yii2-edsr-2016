<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cupboard_item".
 *
 * @property integer $id
 * @property integer $cupboard_id
 * @property integer $digital_product_id
 * @property string $timestamp_added
 *
 * @property Cupboard $cupboard
 * @property DigitalProduct $digitalProduct
 */
class CupboardItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cupboard_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cupboard_id', 'digital_product_id'], 'required'],
            [['cupboard_id', 'digital_product_id'], 'integer'],
            [['timestamp_added'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cupboard_id' => Yii::t('app', 'Cupboard ID'),
            'digital_product_id' => Yii::t('app', 'Digital Product ID'),
            'timestamp_added' => Yii::t('app', 'Timestamp Added'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCupboard()
    {
        return $this->hasOne(Cupboard::className(), ['id' => 'cupboard_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDigitalProduct()
    {
        return $this->hasOne(DigitalProduct::className(), ['id' => 'digital_product_id']);
    }
}
