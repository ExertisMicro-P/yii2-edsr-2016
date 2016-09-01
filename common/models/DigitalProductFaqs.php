<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "digital_product_faqs".
 *
 * @property integer $id
 * @property integer $digital_product_id
 * @property string $faqs
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property DigitalProduct $digitalProduct
 */
class DigitalProductFaqs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'digital_product_faqs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['digital_product_id'], 'required'],
            [['digital_product_id', 'created_by', 'updated_by'], 'integer'],
            [['faqs'], 'string'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'digital_product_id' => 'Digital Product ID',
            'faqs' => 'Faqs',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDigitalProduct()
    {
        return $this->hasOne(DigitalProduct::className(), ['id' => 'digital_product_id']);
    }
}
