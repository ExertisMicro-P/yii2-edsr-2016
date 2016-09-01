<?php

namespace common\models;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use common\models\ProductLeafletInfo;

use Yii;

/**
 * This is the model class for table "digital_product".
 *
 * @property integer        $id
 * @property string         $partcode
 * @property string         $description
 * @property integer        $is_digital
 * @property string         $faqs
 * @property boolean        $enabled
 * @property integer        $eztorm_id
 * @property string         $image_url
 * @property string         $display_price_as EZTORMRRP - Use RRP from eZtorm API; FIXED - Use value defined in Digital Product 'fixed_price' column; PRICAT - Use customer PriCat Pricing with Std Pricing fallback
 * @property float $fixed_price Price which should be used if display_price as = FIXED
 *
 * @property CupboardItem[] $cupboardItems
 * @property StockItem[]    $stockItems
 */
class DigitalProduct extends \yii\db\ActiveRecord
{
    const DISPLAY_PRICE_AS_FIXED = 'FIXED';
    const DISPLAY_PRICE_AS_PRICAT = 'PRICAT';
    const DISPLAY_PRICE_AS_EZTORMRRP = 'EZTORMRRP';
    
    /**
     * Cached copy of Product Name from eZtorm API
     *
     * @var string Product Name
     */
    private $_productName;


    /**
     * Cached copy of Boxshot from eZtorm API
     *
     * @var string PBoxshot
     */
    private $_boxShot;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'digital_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['partcode'], 'required'],
            [['is_digital'], 'integer','max'=>1],
            [['eztorm_id'], 'integer'],
            [['image_url'], 'string'],
            [['faqs'], 'string'],
            [['enabled'], 'boolean'],
            [['partcode', 'description', 'image_url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'Digital Product ID'),
            'partcode'    => Yii::t('app', 'Partcode'),
            'description' => Yii::t('app', 'Description'),
            'eztorm_id' => Yii::t('app', 'Eztorm ID'),
            'is_digital'  => Yii::t('app', 'Is Digital'),
            'faqs' => Yii::t('app', 'FAQs'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['item_code' => 'partcode']);
    }


    public function getProductCode_Lookup()
    {
        return $this->hasOne(ProductcodeLookup::className(), ['id' => 'eztorm_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCupboardItems()
    {
        return $this->hasMany(CupboardItem::className(), ['digital_product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockItems()
    {
        return $this->hasMany(StockItem::className(), ['productcode' => 'partcode']);
    }

    public function getNumStockItems()
    {
        return $this->hasMany(StockItem::className(), ['productcode' => 'partcode'])->count();
    }

    public function getMainImage()
    {
        $images = $this->images;
        if ($images && count($images)) {
            return $images[0];
        } else {
            // maybe we can use the image URL
            if (!empty($this->image_url)) {
                $productimage = new ProductImage();
                $productimage->image_url = $this->image_url;
                $productimage->determineDimensions();
                return $productimage;
            }
        }
    }

    public function getMainImageThumbnailTag($scaled = true) {
        if (!($description = $this->description)) {
            $description = 'Product image';
        }

        $image = $this->getMainImage() ;
        if ($image) {
            return $image->getImageThumbnailTag($this->description) ; // , $scaled ? null: 0) ;
        }
        return '<img src="/img/no-boxshot.jpg"
                      height="64"
                      width="64"
                      title="' . htmlspecialchars($description) . '"
                      alt="' . htmlspecialchars($description) . '"
                      data-toggle="tooltip" />';

    }

    public function getMainImageTag($maxWidth = 100, $maxHeight = 100)
    {
        if (!($description = $this->description)) {
            $description = 'Product image';
        }

        $image = $this->getMainImage() ;
        if ($image) {
            return $image->getImageTag($this->description) ;
        }

        return '<img src="/img/no-photo.jpg"
                      height="100"
                      width="100"
                      title="' . htmlspecialchars($description) . '"
                      alt="' . htmlspecialchars($description) . '"
                      data-toggle="tooltip" />';
    }


    public function getImages()
    {
        return $this->hasMany(ProductImage::className(), ['digital_product_id' => 'id']);
    }

    /**
     * GET LEAFLET IMAGE TAG
     * =====================
     * @return string
     */
    public function getLeafletImageTag () {
        if (!$this->productLeafletInfo) {
            $pli           = new ProductLeafletInfo();
            $pli->partcode = $this->partcode;
            $pli->save();
        }

        if ($this->productLeafletInfo) {
            return $this->productLeafletInfo->getLeafletImageTag() ;
        }
        return '<img src="/img/no-photo.jpg"
                      height="64"
                      width="64"
                      title="No Leaflet Available"
                      alt="No Leaflet Available"
                      data-toggle="tooltip" />';

    }

    /**
     * DELETE LEAFLET IMAGE
     * ====================
     * This will call the underlying leaflet model to delete the current image
     * form disk.
     *
     * @return bool
     */
    public function deleteLeafletImage() {
        if ($this->productLeafletInfo) {
            return $this->productLeafletInfo->deleteLeafletImage() ;
        }
        return true ;
    }

    /**
     * SAVE NEW LEAFLET IMAGE
     * ======================
     * @param $leafletName
     */
    public function saveNewLeafletImage($leafletName) {
        if ($this->productLeafletInfo) {
            $this->productLeafletInfo->iamge = $leafletName ;
            $this->productLeafletInfo->save() ;
        }
    }

    /**
     * GET BASE LEAFLET DIRECTORY
     * ==========================
     * THis returns the full path to the directory the leafelt image should be
     * stored in. It's use when a new image is uploaded
     *
     * @return string
     */
    public function getBaseLeafletDirectory () {
        return $this->productLeafeltInfo->getBaseLeafletDirectory() ;
    }

    /**
     * GET ITEM PRICE
     * ==============
     * Returns either the customer specific price or the standard price.
     * or it can also return RRP from eZtorm API, or a fixed priced defined in DigitalProducts
     * @return int
     */
    public function getItemPrice ($accountNumber) {
        /*
        $productPrice = $this->getProductPrice($accountNumber)->one() ;

        return ($productPrice ? $productPrice->item_price : $this->product->cost_price) ;
         *
         */
        //die(\yii\helpers\VarDumper::dumpAsString($this->attributes,99,true));
        switch ($this->display_price_as) {
            case self::DISPLAY_PRICE_AS_EZTORMRRP:
                $price = $this->getProductRRP();
                break;
            case self::DISPLAY_PRICE_AS_FIXED:
                $price = $this->fixed_price;
                break;
            case self::DISPLAY_PRICE_AS_PRICAT:
                $price = \common\components\CustomerPrice::getPrice($accountNumber, $this->partcode);
                break;
        }
        return $price;
    }

    /*
    public function getProductPrice($accountNumber)
    {
        return $this->hasOne(CustomerPrice::className(), ['item_code' => 'partcode'])
                    ->onCondition(['account' => $accountNumber]) ;
    }
     *
     */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductLeafletInfo()
    {
        return $this->hasOne(ProductLeafletInfo::className(), ['partcode' => 'partcode']);
    }


    /**
     * Builds and executes a SQL statement for truncating the DB table.
     * @param string $table the table to be truncated. The name will be properly quoted by the method.
     */
    public static function truncateTable()
    {
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();
        \Yii::$app->getDb()->createCommand()->truncateTable(self::tableName())->execute();
        Yii::trace('Dropped table before file processing' .__METHOD__.':'.__LINE__);
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();
    }

    /**
     * GET EZTORM PRODUCT FROM PARTCODE
     * ================================
     * Actually returns the ProductcodeLookup record, or null
     *
     * @param $partcode
     *
     * @return array|null|ProductCodeLookup
     */
    public static function getEztormProductFromPartcode($partcode){
        $product = DigitalProduct::find()->where(['partcode'=>$partcode])->one();
        if(isset($product)){
            $eztorm = ProductcodeLookup::find()->where(['id'=>$product->eztorm_id])->one();
            return $eztorm;
        }
        Yii::info(__METHOD__ . 'can not find partcode ' . $partcode);
        return null;
    }

 

    public function behaviors() {
        return [
            [
                'class' => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
            //Taggable::className(),

        ];
    }


    /**
     * GET BOXSHOT URL
     * ===============
     * Fetches the Product name for this Stock Item from eZtorm API. This is
     * cached globally, so if nothing is returned, there's a definite error
     *
     * @return string Product Name
     */
    public function getBoxShotURL()
    {
        if ($url = \common\components\DigitalPurchaser::getBoxshot($this->partcode)) {
            return $url ;
        }

        return  '/img/no-boxshot.jpg'; //Yii::t('app', 'Boxshot could not be fetched');
    } // getProductName

    /**
     * GET PRODUCT NAME
     * ================
     * @return string
     */
    public function getProductName() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'Name') ;
    }

    /**
     * GET PRODUCT RRP from eZtorm
     * ================
     * @return string
     */
    public function getProductRRP() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'RRP') ;
    }

    /**
     * GET CATEGORY
     * ============
     * @return string
     */
    public function getCategory() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'category') ;
    }

    /**
     * GET FORMAT
     * ==========
     *
     * @return string
     */
    public function getFormat() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'Format') ;
    }

    /**
     * GET PUBLISHER
     * =============
     *
     * @return string
     */
    public function getPublisher() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'Publisher') ;
    }

    /**
     * GET INFORMATION
     * ===============
     *
     * @return string
     */
    public function getInformation() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'InformationFull') ;
    }

    /**
     * GET REQUIREMENTS
     * ================
     *
     * @return string
     */
    public function getRequirements() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'Requirements') ;
    }

    /**
     * GET PEGI
     * ========
     *
     * @return string
     */
    public function getPegi() {
        return \common\components\DigitalPurchaser::getProductItem($this->partcode, 'PEGI_Age_Others') ;
    }

    /**
     * GET SCREENSHOTS
     * ===============
     *
     * @return array
     */
    public function getScreenshots() {
        return \common\components\DigitalPurchaser::getProductScreenshots($this->partcode) ;
    }

    /**
     * GET GENRES
     * ==========
     *
     * @return array
     */
    public function getGenres() {
        return \common\components\DigitalPurchaser::getProductGenres($this->partcode) ;
    }
    
    
    static function getDisplayPriceAsOptions(){
        
        return [
          'PRICAT' => 'PRICAT',
          'EZTORMRRP' => 'EZTORMRRP',
          'FIXED' => 'FIXED',  
        ];
        
    }

}
