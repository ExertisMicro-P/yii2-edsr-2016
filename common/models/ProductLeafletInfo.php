<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product_leaflet_info".
 *
 * @property integer $id
 * @property string  $partcode
 * @property string  $image
 * @property double  $key_xcoord
 * @property double  $key_ycoord
 * @property integer $created_at
 * @property integer $updated_at
 */
class ProductLeafletInfo extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_leaflet_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['partcode'], 'required'],
            [['key_xcoord', 'key_ycoord', 'name_xcoord', 'name_ycoord'], 'number'],
            [['partcode', 'image'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'partcode'   => 'Partcode',
            'image'      => 'Image',
            'key_xcoord' => 'Key Xcoord',
            'key_ycoord' => 'Key Ycoord',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(DigitalProduct::className(), ['partcode' => 'partcode']);
    }

    /**
     * GET BASE LEAFLET DIRECTORY
     * ==========================
     * THis returns the full path to the directory the leafelt image should be
     * stored in. It's use when a new image is uploaded
     *
     * @return string
     */
    public function getBaseLeafletDirectory() {
        $path     = Yii::$app->params['uploadPath'] . 'product_leaflets/' . $this->partcode . '/' ;
        $fullPath = Yii::getAlias('@webroot') . '/' . $path;
        Yii::info(__METHOD__.': fullPath = '.$fullPath);

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true) ;
        }

        return $fullPath ;
    }

    /**
     * GET LEAFLET IMAGE FILE NAME
     * ===========================
     * Returns both the web-relative file path and the full server path and
     * file name for the leaflet, or false if there is no file
     *
     * @return bool|string
     */
    public function getLeafletImageFilename($siteArea = '@frontend') {

        $workImg = tempnam(Yii::getAlias($siteArea) . '/runtime/tmp', 'LEF') . '.jpg';

        $temp = fopen($workImg, 'w') ;
        fwrite($temp, $this->image_data) ;
        fclose($temp) ;
        return $workImg ;


        $path     = '/' . Yii::$app->params['uploadPath'] . 'product_leaflets/' . $this->partcode . '/' . $this->image;
        $fullName = Yii::getAlias('@webroot') . '/' . $path;

        if (file_exists($fullName) && is_file($fullName)) {
            return [$path, $fullName] ;
        }
        return false ;
    }

    public function getLeafletWebImageFilename() {
        $path     = '/' . Yii::$app->params['uploadPath'] . 'product_leaflets/' . $this->partcode . '/' . $this->image;
        $fullName = Yii::getAlias('@webroot') . '/' . $path;

        if (file_exists($fullName) && is_file($fullName)) {
            return [$path, $fullName] ;
        }
        return false ;
    }

    /**
     * GET LEAFLET IMAGE TAG
     * =====================
     * Checks if the image exists and returns an <img> tag for it if so, and
     * to a default image it not
     *
     * @return string
     */
    public function getLeafletImageTag()
    {
        $fullName     = $this->getLeafletWebImageFilename() ;
        if ($fullName) {
            return '<img src="' . $fullName[0] . '"
                      height="64"
                      width="64"
                      data-toggle="tooltip" />';
        }

        return '<img src="/img/no-photo.jpg"
                      height="64"
                      width="64"
                      title="No Leaflet Available"
                      alt="No Leaflet Available"
                      data-toggle="tooltip" />';
    }

    /**
     * SAVE NEW LEAFLET IMAGE
     * ======================
     * @param $leafletName
     */
    public function saveNewLeafletImage($leafletName)
    {
        $this->image = $leafletName;
        if (!$this->save()) {
            print_r($this->errors);
        }
    }


    /**
     * DELETE LEAFLET IMAGE
     * ====================
     * This will do as it says :)
     *
     * @return bool
     */
    public function deleteLeafletImage () {
        $file = $this->getLeafletWebImageFilename() ;
        if ($file && file_exists($file[1])) {
            unlink($file[1]) ;
            $this->image = null ;
            $this->save() ;
        }
        return true ;
    }
}
