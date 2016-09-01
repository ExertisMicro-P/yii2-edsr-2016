<?php

/*
 * Umbrella function operating on the itempurchaser class to do an series of operation :
 * Get the ztorm catalgue.
 * Purchase basket/product/stockitem
 * Get the download url for a stock item
 * Get the install keys for a stock item
 *
 * Author : Helen Kappes
 *
 * Current methods :
 * getAndSaveZtormCatalogue()
 * purchaseProduct(StockItem $stockItem)
 * getMemberFileURL(StockItem $stockItem)
 * getMemberFileURL(StockItem $stockItem)
 * getProductInstallKey(StockItem $stockItem)
 *
 *
 */


namespace common\components;
use Yii;
use common\models\StockItem;
use console\components\ItemPurchaser;
use common\models\PersistantDataLookup;
use common\models\DigitalProduct;
use console\components\ZtormAPI\CurlException;
use console\components\ZtormAPI\ZtormAPIException;
use console\components\EDSRException;
use console\components\FileFeedErrorCodes;

class DigitalPurchaser {

    protected $product;
    public $exception = null;

    /**
     * getAndSaveZtormCatalogue()
     * Umbrella function for getting the ezorm product catalogue and saving it to the
     * ezorm product lookup table.
     */
    public function getAndSaveZtormCatalogue($store=null){
        try{
            $itempurchaser  = new ItemPurchaser();
            $itempurchaser->setStoreDetails($store);
            $products = $itempurchaser->getZtormCatalogue();
            foreach($products as $product){
               if(!$product->saveWithAuditTrail('Created/Updated item ' . 'productcode ' . $product->name )){
                 Yii::error('Product not saved to Product Lookup ' . $product->product_id . ' ' . $product->name);
                    //also will require an email @TODO;
               }
            }
            //save the time the catelogue was downloaded to allow for delta lookup later on.
            PersistantDataLookup::saveZtormCatalogueLookupdate();
        }
        catch(CurlException $e){
           Yii::error(__CLASS__ . ' ZtormCatalogue update failed. Curl request failed');
           $this->exception = $e;
           return null;
       }
       catch(ZtormAPIException $e){
           Yii::error(__CLASS__ . ' ZtormCatalogue update failed. API request failed');
           $this->exception = $e;
           return null;
       }
    }


    /**
     * getZtormCatalogue()
     * Umbrella function for getting the ezorm product catalogue
     * We don't save it!
     */
    public function getZtormCatalogue($store){
        try{
            $itempurchaser  = new ItemPurchaser();
            $itempurchaser->setStoreDetails($store);
            $products = $itempurchaser->getZtormCatalogue();
            //\Yii::info(__METHOD__.': $products = '.\yii\helpers\VarDumper::dumpAsString($products));
            return $products;
        }
        catch(CurlException $e){
           Yii::error(__METHOD__ . ' ZtormCatalogue update failed. Curl request failed');
           $this->exception = $e;
           return null;
       }
       catch(ZtormAPIException $e){
           Yii::error(__METHOD__ . ' ZtormCatalogue update failed. API request failed');
           $this->exception = $e;
           return null;
       }
    } // getZtormCatalogue

    /**
     * purchaseProduct
     * Umbrella function for purchasing a product.
     * Looks up the eztorm productID. Gets the memberID or request one from eztorm
     * Creates a basket. Adds an item to the basket and then purchases the basket.
     * @param \common\models\StockItem $stockItem
     * @return string order_id if succesfull or null if not returns true if already owned
     */
    public function purchaseProduct(StockItem &$stockItem){
       //try{
        //will have lookup product_id in digital product.

            //if we find product set up basket and buy product.
            $product = DigitalProduct::getEztormProductFromPartcode($stockItem->productcode);
            
            $itempurchaser  = new ItemPurchaser();
            $itempurchaser->setStoreDetails($product->store);
            $itempurchaser->setMemberID($stockItem);
            //create the storeID
            $itempurchaser->setStoreMemberID($stockItem);   
            $itempurchaser->setCompanyName($stockItem);
            $storeorderid = str_pad((string)$stockItem->id, 10, "0", STR_PAD_LEFT);
            $itempurchaser->setStoreOrderID($storeorderid);
            $itempurchaser->setProductPrice($stockItem->getPrice());
            //check if we need to set up user
            if($itempurchaser->getMemberID() == '0')  { //no account - get an account
                $itempurchaser->getZtormMemberID($stockItem);
                $stockItem->setMemberID($itempurchaser->getMemberID());
            }
            
            if(isset($product)){
                $itempurchaser->getandsetBasket();
                $orderid = $itempurchaser->purchaseProduct($stockItem);
                //Finally set product id
                $stockItem->eztorm_product_id = $product->product_id;
                return $orderid;
            }else{//no product found - log and exit
                $msg = __CLASS__ . ' ' . $stockItem->productcode . ' ' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE);
                Yii::error($msg);
                throw new EDSRException(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE, $msg);
                //return null;
            }
    }

    public function getAndSetEztormMemberID(StockItem &$stockItem){
        $product = DigitalProduct::getEztormProductFromPartcode($stockItem->productcode);
        if(isset($product)){
            //if we find product set up basket and buy product.
            $itempurchaser  = new ItemPurchaser();
            $itempurchaser->setStoreDetails($product->store);
            $itempurchaser->setMemberID($stockItem);
            $itempurchaser->setStoreMemberID($stockItem);
            //create the storeID
            $storeorderid = $stockItem->getPo() . '-' . (string)$stockItem->id;
            $itempurchaser->setStoreOrderID($storeorderid);
            //check if we need to set up user
            //if($itempurchaser->getMemberID() == '0')  { //no account - get an account
                $itempurchaser->getZtormMemberID($stockItem);
                $stockItem->setMemberID($itempurchaser->getMemberID());
            //}
        }
        else{//no product found - log and exit
            $msg = __CLASS__ . ' ' . $stockItem->productcode . ' ' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE);
            Yii::error($msg);
            throw new EDSRException(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE, $msg);
        }
    }

    /**
     * getMemberFileURL
     * Umbrella function for geting a the download url for a particular stockitem
     * @param \common\models\StockItem $stockItem
     * @return type
     */
    public function getMemberFileURL(StockItem $stockItem){
        try{
            $product = DigitalProduct::getEztormProductFromPartcode($stockItem->productcode);
            if(isset($product)){
                $itempurchaser = new ItemPurchaser();
                $itempurchaser->setStoreDetails($product->store);
                $file = $itempurchaser->getDownloadURL($stockItem);
                return $file;
            }
            //no product found - log and exit
            $msg = __CLASS__ . ' ' . $stockItem->productcode . ' ' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE);
            Yii::error($msg);
            throw new EDSRException(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE, $msg);

        }
        catch(CurlException $e){
            Yii::error(__CLASS__ . 'download URL request failed on CURL request');
           $this->exception = $e;
           return null;
        }
       catch(ZtormAPIException $e){
           Yii::error(__CLASS__ . 'download URL request failed on API request');
           $this->exception = $e;
           return null;
       }
    } // getMemberFileURL



    /**
     * s_getMemberFileURL
     * Umbrella function for geting a the download url for a particular stockitem
     * @param \common\models\StockItem $stockItem
     * @return type
     */
    static function s_getMemberFileURL(StockItem $stockItem){
        try{
            $product = DigitalProduct::getEztormProductFromPartcode($stockItem->productcode);
            if(isset($product)){
                $itempurchaser = new ItemPurchaser();
                $itempurchaser->setStoreDetails($product->store);
                $file = $itempurchaser->getDownloadURL($stockItem);
                return $file;
            }
            //no product found - log and exit
            $msg = __CLASS__ . ' ' . $stockItem->productcode . ' ' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE);
            Yii::error($msg);
            throw new EDSRException(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE, $msg);

        }
        catch(CurlException $e){
            Yii::error(__CLASS__ . 'download URL request failed on CURL request');
            throw $e;
        }
       catch(ZtormAPIException $e){
           Yii::error(__CLASS__ . 'download URL request failed on API request');
           throw $e;
       }
    } // s_getMemberFileURL



    /**
     * getProductKey
     * Gets a product key for a given stockitem.
     * @param \common\models\StockItem $stockitem
     * @return string
     */
    static function getProductInstallKey(StockItem $stockItem){  //productkey

        if (array_key_exists('mockKeys', Yii::$app->params) && Yii::$app->params['mockKeys'] === true) {
            $key = 'mock-' . date('Ymd') . '-' . date('His') . '-' . $stockItem->id ;
            $stockItem->key_accessed = date('Y-m-d H:i:s');
            $stockItem->saveWithAuditTrail('Key accessed on ' . $stockItem->key_accessed . ' '. substr($key,-5));
            return $key;
        }


        try{
            // RCH 20160406
        //$product = DigitalProduct::getEztormProductFromPartcode($stockItem->productcode);
        $product = \common\models\ProductcodeLookup::find()->where(['productcode_lookup.product_id'=>$stockItem->eztorm_product_id])->one();
        if(isset($product)){ //product found
           $itempurchaser = new ItemPurchaser();
           $itempurchaser->setStoreDetails($product->store);
           $itempurchaser->getZtormMemberID($stockItem);
               
           $key = $itempurchaser->GetInstallKeys($stockItem);
           if($key === null){
               //something went very wrong.
               $msg = __METHOD__ . ' ' . __CLASS__ . 'Unable to get key for stockitem ' . print_r($stockItem->attributes,true);
               Yii::error($msg);
               throw new EDSRException(FileFeedErrorCodes::INSTALL_KEY_NOT_RX, $msg);
           }
           //All went well we have a valid key
           \Yii::info(__METHOD__.': key= ****'.substr($key,-5));
             $stockItem->key_accessed = date('Y-m-d H:i:s');
             $stockItem->saveWithAuditTrail('Key accessed ' . substr($key,-5));
             return $key;

         }
         //no product found - log and exit
        $msg = __CLASS__. ' '. __METHOD__ . ' ' . $stockItem->productcode . ' ' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE);
            Yii::error($msg);
            throw new EDSRException(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE, $msg);
       }
       catch(CurlException $e){
            Yii::error(__CLASS__ . 'get Install Key request failed on CURL request');
           throw $e;
           return null;
        }
       catch(ZtormAPIException $e){
            Yii::error(__CLASS__ . 'get Install Key request failed on API request');
           throw $e;
           return null;
       }
    }



    /**
     * s_getProductName
     * Umbrella function for geting a the Product Name for a particular stockitem
     * @param \common\models\StockItem $stockItem
     * @return String Name
     */
    static function s_getProductName(StockItem $stockItem){
        return self::getStoreProductFieldValue($stockItem, 'ProductName');
    } // s_getProductName

    /**
     * s_getProductName
     * Umbrella function for geting a the Product Name for a particular stockitem
     * @param \common\models\StockItem $stockItem
     * @return String Name
     */
    static function s_getBoxshot(StockItem $stockItem){
        return self::getStoreProductFieldValue($stockItem, 'Boxshot');
    } // s_getProductName

    /**
     * Helper method to get fields from StoreProduct
     * @param \common\models\StockItem $stockItem
     * @return type
     */
    static function getStoreProductFieldValue(StockItem $stockItem, $fieldName){
        \Yii::info(__METHOD__."(StockItem ".print_r($stockItem->attributes,true).", $fieldName)");
        try{
            $product = DigitalProduct::getEztormProductFromPartcode($stockItem->productcode);
            if(isset($product)){
                $itempurchaser = new ItemPurchaser();
                $itempurchaser->setStoreDetails($product->store);
                $method = 'get'.$fieldName;
                $value = $itempurchaser->$method($stockItem);
                return $value;
            }
            //no product found - log and exit
            $msg = __CLASS__ . ' ' . $stockItem->productcode . ' ' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE);
            Yii::error($msg);
            throw new EDSRException(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE, $msg);

        }
        catch(CurlException $e){
            Yii::error(__CLASS__ . ': '.$fieldName.' request failed on CURL request');
            throw $e;
        }
       catch(ZtormAPIException $e){
           Yii::error(__CLASS__ . ': '.$fieldName.' request failed on API request');
           return ''; // couldn't fetch details from Ztorrm. This can happen if the product id has been delisted in Ztorm Hub
           //throw $e; // RCH 20151103
       }
    } // s_getProductName

    // -----------------------------------------------------------------------
    // The following methods allow access to data items based on product code
    // instead of a stock item, so can be used outside of then stock rooms.
    // -----------------------------------------------------------------------
    /**
     * GET BOX SHOT
     * ============
     * Returns the boxshot for the passed product code
     *
     * @param $productCode
     *
     * @return mixed
     * @throws CurlException
     * @throws EDSRException
     * @throws ZtormAPIException
     * @throws \Exception
     */
    static function getBoxshot($productCode){
        return self::getStoreProductItemValue($productCode, 'Boxshot');
    } // s_getProductName

    /**
     * GET PRODUCT SCREEN SHOTS
     * ========================
     * These are stored as string separated by ^^, so this reads the string
     * and then expands it into an array before returning it.
     *
     * @param $productCode
     *
     * @return array
     * @throws CurlException
     * @throws EDSRException
     * @throws ZtormAPIException
     * @throws \Exception
     */
    static function getProductScreenshots($productCode) {
        $str = self::getStoreProductItemValue($productCode, 'Screenshots');

        $sshots = [] ;
        if ($str) {
            $sshots = explode('^^', $str) ;
        }
        return $sshots ;
    }

    /**
     * GET PRODUCT GENRES
     * ==================
     * These are stored as string separated by ^^, so this reads the string
     * and then expands it into an array before returning it.
     *
     * @param $productCode
     *
     * @return array
     * @throws CurlException
     * @throws EDSRException
     * @throws ZtormAPIException
     * @throws \Exception
     */
    static function getProductGenres($productCode) {
        $str = self::getStoreProductItemValue($productCode, 'Genres');

        $genres = [] ;
        if ($str) {
            $genres = explode('^^', $str) ;
        }
        return $genres ;
    }


    /**
     * GET PRODUCT ITEM
     * ================
     * Returns the named product field
     * @param $productCode
     * @param $itemName
     *
     * @return mixed
     * @throws CurlException
     * @throws EDSRException
     * @throws ZtormAPIException
     * @throws \Exception
     */
    static function getProductItem($productCode, $itemName) {
        return self::getStoreProductItemValue($productCode, $itemName);
    }


    /**
     * GET STORE PRODUCT ITEM VALUE
     * ============================
     * @param $productCode
     * @param $fieldName
     *
     * @return mixed
     * @throws CurlException
     * @throws EDSRException
     * @throws ZtormAPIException
     * @throws \Exception
     */
    static function getStoreProductItemValue($productCode, $fieldName){
        \Yii::info(__METHOD__."($productCode, $fieldName)");
        
        if ($fieldName=='RRP') {
            $x=1;
        }
    
        try{
            $product = DigitalProduct::getEztormProductFromPartcode($productCode);
            if(isset($product)){
                $itemPurchaser = new ItemPurchaser();
                $itemPurchaser->setStoreDetails($product->store);
                $method = 'retrieve'.$fieldName;
                if(method_exists($itemPurchaser, $method)) {
                    $value = $itemPurchaser->$method($productCode);
                } else {
                    $value = $itemPurchaser->getProductItem($productCode, $fieldName) ;
                }
                return $value;
            }
            //no product found - log and exit
            $msg = __CLASS__ . ' ' . $productCode . ' ' . FileFeedErrorCodes::toString(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE);
            Yii::error($msg);
            throw new EDSRException(FileFeedErrorCodes::ITEM_NOT_MAPPED_IN_CATALOGUE, $msg);

        }
        catch(CurlException $e){
            Yii::error(__CLASS__ . ': '.$fieldName.' request failed on CURL request');
            throw $e;
        }
        catch(ZtormAPIException $e){
            Yii::error(__CLASS__ . ': '.$fieldName.' request failed on API request');
            throw $e;
        }
    } // getStoreProductItemValue




}

