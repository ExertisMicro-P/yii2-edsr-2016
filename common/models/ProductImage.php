<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product_image".
 *
 * @property integer $id
 * @property integer $digital_product_id
 * @property string $image_url
 * @property string $image_tn
 * @property integer $width
 * @property integer $height
 * @property string $create_at
 * @property string $updated_at
 *
 * @property DigitalProduct $digitalProduct
 */
class ProductImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['digital_product_id', 'image_url', 'image_tn'], 'required'],
            [['digital_product_id', 'width', 'height'], 'integer'],
            [['create_at', 'updated_at'], 'safe'],
            [['image_url', 'image_tn'], 'string', 'max' => 255]
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
            'image_url' => 'Image Url',
            'image_tn' => 'Image Tn',
            'width' => 'Width',
            'height' => 'Height',
            'create_at' => 'Create At',
            'updated_at' => 'Updated At',
        ];
    }


    public function getImageThumbnailTag($description='', $maxWidth = 64, $maxHeight = 64)
    {
        return $this->getScaledImageTag($this->image_tn ? $this->image_tn : $this->image_url, $maxWidth, $maxHeight, $description) ;
    }

    public function getImageTag($description='', $maxWidth = 160, $maxHeight = 160)
    {
        return $this->getScaledImageTag($this->image_url, $maxWidth, $maxHeight, $description) ;
    }

    /**
     * GET SCALED IMAGE TAG
     * ====================
     * @param        $src
     * @param        $maxWidth
     * @param        $maxHeight
     * @param string $description
     *
     * @return string
     */
    private function getScaledImageTag ($src, $maxWidth, $maxHeight, $description='') {

        // ----------------------------------------------------------
        // If no max specified, the height is controlled by css
        // ----------------------------------------------------------
        if (!$maxHeight || !$maxWidth) {
            $sizeDetails = '' ;

        } else {
            if ($maxHeight <= $this->height ||
                $maxWidth  <= $this->width) {

                $ratioh = $maxHeight / $this->height ;
                $ratiow = $maxWidth  / $this->width  ;

                $ratio  = min($ratioh, $ratiow) ;

                $width  = $this->width  * $ratio ;
                $height = $this->height * $ratio ;

            } else {
                $width  = $this->width ;
                $height = $this->height ;
            }
            $height = 60; // quick fix
            $sizeDetails =
                ' height="' . $height . '" ' ; // .
                    // quick fix ' width="' . $width. '" ' .
        }

        return '<img src="' . $src . '" ' .
                    $sizeDetails .
                    ' title="' . htmlspecialchars($description) . '" ' .
                    ' alt="' . htmlspecialchars($description) . '" ' .
                    ' data-toggle="tooltip" />';
    }




    public function determineDimensions() {

        return; // quick fix

        try {
            $imageInfo = getimagesize($this->image_url);

            if ($imageInfo) {
                $this->width  = $imageInfo[0];
                $this->height = $imageInfo[1];
            }
        } catch (Exception $ex) {
            $this->width  = 0;
            $this->height = 0;

        }


    } // _determineDimensions


    /**
     * BEFORE SAVE
     * ===========
     * Attempts to determine the width and height of the image

     * @param bool $isInserting
     *
     * @return bool
     */
    public function beforeSave ($isInserting) {

        $this->determineDimensions();

        return parent::beforeSave($isInserting);
    } // _determineDimensions

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDigitalProduct()
    {
        return $this->hasOne(DigitalProduct::className(), ['id' => 'digital_product_id']);
    }
}
