<?php

/**
 * ItemPurchaser
 * Handles CurlRequests
 * @author helenk
 *
 * @see https://secure.ztorm.net/vhosts/manager/files/dl/Ztorm_API_Documentation.pdf
 */
/* need  to extend from class */

namespace console\components;
use console\components\ZtormAPI\BaseCurlValueObject;
use console\components\ZtormAPI\ExportProductsMsgHandler;
use console\components\ZtormAPI\SetUserInfoMsgHandler;
use console\components\ZtormAPI\CreateBasketMsgHandler;
use console\components\ZtormAPI\AddProductToBasketMsgHandler;
use console\components\ZtormAPI\PurchaseBasketMsgHandler;
use console\components\ZtormAPI\GetInstallKeysMsgHandler;
use console\components\ZtormAPI\GetDownloadUrlMsgHandler;
use console\components\ZtormAPI\GetProductByIdMsgHandler;
use common\models\ProductcodeLookup;
use common\models\DigitalProduct;
use console\components\ZtormAPI\CurlException;
use console\components\ZtormAPI\ZtormAPIException;
use common\models\StockItem;


class ItemPurchaser extends BaseCurlValueObject{

    private $_useLiveeZtorm = FALSE; // actually set by params-local.php! See _construct.

    public $xmlRequest = '';
   // public $url;
    public $memberID;
    public $basketID;
    public $productID = '14162';
    public $price = '0.00';
    public $ip = '213.122.164.130'; // Exertis Public IP address
    public $storeOrderID = '';
    public $storeMemberID = '';
    public $orderID = ''; //532797';
    public $installKey='';
    public $stockitem = null;
   // public $storeid;
   // public $storep;
    public $file=null;
    public $companyName;
    public $store;
 //previous <StoreID>139</StoreID>    <Password>yq6fK49fdhUHvD</Password>



    function __construct() {
 //       $this->_useLiveeZtorm = \Yii::$app->params['useLiveeZtorm'];
/*
        if ($this->_useLiveeZtorm) {
            // Live eZtorm API
            $this->storeid = 183;
            //$this->storep = 'S_sdIt1WasKQR';
            $this->storep = 'OQoc136iA7b9o82Wi';
            $this->url = 'http://secure.ztorm.net/local/api/1.0/';
        } else {
           
            $this->storeid = 183;
            $this->storep = '273kkYrjdk99Uy';
            $this->url = 'http://ztormlab.ztorm.net/api/1.0/ ';
        }*/
    }
    
    public function setStoreDetails($store){
        $this->store = $store;
    }
    

    public function setMemberID($stockitem){
        $this->memberID = 0; //$stockitem->getMemberID();
    }


    public function setStoreMemberID(StockItem $stockItem){
        $this->storeMemberID = $stockItem->getStoreMemberID();

    }

    public function getStoreMemberId(){
        return $this->storeMemberID;
    }

    public function setCompanyName(StockItem $stockitem){
        $this->companyName = $stockitem->getCompanyName();
    }


    public function getCompanyName(){
        return $this->storeMemberID;
       // return htmlspecialchars($this->companyName);
    }

    public function setBasketID($id){
        $this->basketID = $id;
    }
    public function setProductID($id){
        $this->productID = $id;
    }
    public function setProductPrice($price){
        $this->price = $price;
    }
    public function setStoreOrderID($id){
        //generate a unique number
        $this->storeOrderID = $id;//time(); //$stockitem->getStoreOrderID();
    }

    public function setFile($file){
        $this->file = $file;
    }


    public function setOrderID($id){
        $this->orderID = $id;
    }

    public function getMemberID(){
        return $this->memberID;
    }
    public function getBasketID(){
        return $this->basketID;
    }
    public function getProductID(){
        return $this->productID;
    }
    public function getProductPrice(){
        return $this->price;
    }

    public function getOrderID(){
        return $this->orderID;
    }

    public function getstoreOrderID(){
        return $this->storeOrderID;
    }

     public function getFile(){
        return $this->file;
    }
    
    public function getStore(){
        return $this->store;
    }
    /**
     *
     * A series of requests to the API Ztorm to get the user id, basket id and
     * and finally the order id inorder to get the install key.
     * @return type $key
     */

    public function getZtormCatalogue(){
         $allproducts = array();
         $exportproducts = new ExportProductsMsgHandler($this);
         $exportproducts->page = 0;
        try{
        do{
            $productsResponse = $exportproducts->sendRequest();
            $exportproducts->page++;
            //keep getting products
            foreach($productsResponse->StoreProduct as $product){
		\Yii::info(__METHOD__.': product = '.\yii\helpers\VarDumper::dumpAsString($product)); // RCH 20150408

                $item = ProductcodeLookup::findAndReturnModel($product->RealProductId);
                $item->product_id = (int)$product->RealProductId;
                $item->name = (string)$product->Name;
                $allproducts[] = $item;
            }
             //add products to DB
         } while(count($productsResponse->StoreProduct)== 200);
         return $allproducts;
        }
        catch(CurlException $e) {
            throw($e);
        }
        catch(ZtormAPIException $e) {
            throw($e);
        }
    }

    public function getZtormRawCatalogue(){
         $allproducts = array();
         $exportproducts = new ExportProductsMsgHandler($this);
         $exportproducts->page = 0;
        try{
        do{
            $productsResponse = $exportproducts->sendRequest();
            $exportproducts->page++;
            //keep getting products
            //die(\yii\helpers\VarDumper::dump($productsResponse, 99, true));
            foreach($productsResponse->StoreProduct as $product){
		//\Yii::info(__METHOD__.': product = '.\yii\helpers\VarDumper::dumpAsString($product)); // RCH 20150408

                //$item = ProductcodeLookup::findAndReturnModel($product->RealProductId);
                //$item->product_id = (int)$product->RealProductId;
                //$item->name = (string)$product->Name;
                $allproducts[] = $product;                   
            }
            //var_dump($allproducts[0]);
            //die();
             //add products to DB
         } while(count($productsResponse->StoreProduct)== 200);
         return $allproducts;
        }
        catch(CurlException $e) {
            throw($e);
        }
        catch(ZtormAPIException $e) {
            throw($e);
        }
    }

    /**
     * 
     * @param type $stockitem
     * @return int eZtorm Store Member ID for given Stockitem
     * @throws type
     */
    public function getZtormMemberID($stockitem){
        $this->stockitem  = $stockitem;
        $this->storeMemberID = $this->stockitem->getStoreMemberID();
        $this->memberID = 0; //$this->stockitem->getMemberID();
        $setinforuserdata = new SetUserInfoMsgHandler($this);
        try{
            $setinforuserdata->sendRequest();
            $this->memberID = $setinforuserdata->getMemberID();
            return $this->memberID;
        }
        catch(CurlException $e) {
            throw($e);
        }
        catch(ZtormAPIException $e) {
           throw($e);
        }
    }

    public function getandsetBasket(){
        $createbasket = new CreateBasketMsgHandler($this);
        try{
            $createbasket->sendRequest();
            $this->basketID = $createbasket->getBasketID();
            return $this->basketID;
        }
        catch(CurlException $e) {
            throw($e);
        }
        catch(ZtormAPIException $e) {
            throw($e);
        }
    }


    public function purchaseProduct($stockitem){
        //$this->productID = $productId;
        //throw new \Exception(__METHOD__.' Live Product Purchase from eZtorm DISABLED');

        try{
            $this->stockitem  = $stockitem;
            $this->productID = DigitalProduct::getEztormProductFromPartcode($this->stockitem->productcode)->product_id;
            $addproducttobasket = new AddProductToBasketMsgHandler($this);
            $addproducttobasket->sendRequest();
            $purchasebasket = new PurchaseBasketMsgHandler($this);
            $purchasebasket->sendRequest();
            $this->orderID = $purchasebasket->getOrderID();
            return $this->orderID;
        }
        catch(CurlException $e) {
            throw($e);
        }
        catch(ZtormAPIException $e) {
            throw($e);
        }
    }


    /**
     * getDownloadURL
     * Gets the download URL from eZtorm for a given stockitem
     * URL is only valid for a set time
     * @param type $stockitem
     * @return String URL
     */
    public function getDownloadURL($stockitem){
        try{
            $this->stockitem  = $stockitem;
            $this->productID = DigitalProduct::getEztormProductFromPartcode($this->stockitem->productcode)->product_id;
            $this->memberID = $this->stockitem->getMemberID();
            $getdownloadurl = new GetDownloadUrlMsgHandler($this); //@todo RCH I think this is unique to the member, so not really the generic download URL we want - See definition of MemberFile.DownloadURL
            $getdownloadurl->sendRequest();
            $url = $getdownloadurl->getDownloadDataURL();
            return $url;
        }
        catch(CurlException $e) {
           throw($e);
        }
        catch(ZtormAPIException $e) {
            throw($e);
        }
    }

    public function getDownloadData($stockitem){
        try{
            $this->stockitem  = $stockitem;
            $this->productID = DigitalProduct::getEztormProductFromPartcode($this->stockitem->productcode)->product_id;
            $this->memberID = $this->stockitem->getMemberID();
            $getdownloadurl = new GetDownloadUrlMsgHandler($this);
            $getdownloadurl->sendRequest();
            $data = $getdownloadurl->getDownloadData();
            return $data;
        }
        catch(CurlException $e) {
           throw($e);
        }
        catch(ZtormAPIException $e) {
            throw($e);
        }
    }


    /**
     * GetInstallKeys
     * Gets the install key from eZtorm for a given stockitem
     * @param type $stockitem
     * @return string key.
     */
    public function GetInstallKeys(StockItem $stockitem){
        try{
            $this->stockitem  = $stockitem;
            // RCH 20160406
            // Use the product ID it was bought on... product can change ztorm id
            //$this->productID = DigitalProduct::getEztormProductFromPartcode($this->stockitem->productcode)->product_id;
            $this->productID = $stockitem->eztorm_product_id;
            $this->memberID = $this->stockitem->getMemberID();
            
            // RCH 20160405
            // Workaround for a stockitem whichwas ordered using the wrong account!
            if ($this->stockitem->id == 3883) {
                $this->memberID = 3189376;
            }
            
            $this->orderID =  $this->stockitem->getOrderID();
            $getinstallkeys = new GetInstallKeysMsgHandler($this);
            $getinstallkeys->sendRequest();
            $getinstallkeys->setInstallKeys();
            $key = $getinstallkeys->getInstallKeyValue();
            return $key;
        }
        catch(CurlException $e) {
            \Yii::error(__METHOD__.': CurlException'.$e->getMessage());
            throw($e);
        }
        catch(ZtormAPIException $e) {
            \Yii::error(__METHOD__.': ZtormAPIException'.$e->getMessage());
           throw($e);
        }
    }



    public function getProductBoxshot($stockitem){

       try{
            $this->stockitem  = $stockitem;
            $this->productID = DigitalProduct::getEztormProductFromPartcode($this->stockitem->productcode)->product_id;
            
            $eztormCatalogue = \common\models\ZtormCatalogueCache::find()->where(['RealProductId'=>$this->productID])->one();

            return $eztormCatalogue->Boxshot;
        }
        catch(CurlException $e) {
           throw($e);
        }
        catch(ZtormAPIException $e) {
            throw($e);
        }

    } // getProductBoxshot



    public function getProductName($stockitem){
      return $this->getProductField($stockitem, 'Name');
    } // getProductName


    public function getBoxshot($stockitem){
      return $this->getProductField($stockitem, 'Boxshot');
    } // getBoxshot


    /**
     * GET PRODUCT FIELD
     * =================
     * This is used when the request demands detaisl from the stock item in
     * addition to the product code
     *
     *
     * @param $stockitem
     * @param $fieldname
     *
     * @return bool
     * @throws CurlException
     * @throws ZtormAPIException
     * @throws \Exception
     */
    private function getProductField($stockitem, $fieldname) {
        $this->stockitem  = $stockitem;

        return $this->getProductItem($stockitem->productcode, $fieldname) ;
    } // getProductField


    // -----------------------------------------------------------------------
    // The following methods allow access to data items based on product code
    // instead of a stock item, so can be used outside of then stock rooms.
    // -----------------------------------------------------------------------

    public function retrieveBoxshot($productCode){
        return $this->getProductItem($productCode, 'Boxshot');
    } // retrieveBoxshot



    /**
     * GET PRODUCT ITEM
     * ================
     * @param $productCode
     * @param $fieldname
     *
     * @return mixed
     * @throws CurlException
     * @throws ZtormAPIException
     * @throws \Exception
     */
    public function getProductItemOld($productCode, $fieldname) {
        \Yii::info(__METHOD__."($productCode, $fieldname)");

        try{
            $this->productID = DigitalProduct::getEztormProductFromPartcode($productCode)->product_id;

            $getproduct = new GetProductByIdMsgHandler($this);

            $data = $getproduct->getProduct();
            \Yii::info(__METHOD__.': $data->attributes='.print_r($data->attributes,  true));
            return $data[$fieldname];
        }
        catch(CurlException $e) {
            throw($e);
        }
        catch(ZtormAPIException $e) {
            //throw($e);
            // If we're getting product not found we need to flag the product as disabled
            $error = $e->getMessage();
            
            if (strpos($error, '[ErrorCode] => 1001')!==FALSE) {
                $dp = DigitalProduct::find()->where(['partcode'=>$productCode])->one();
                if (!empty($dp)) {
                    $dp->enabled = false;
                    $dp->saveWithAuditTrail('Ztorm API tells us "Product not found". Disabling product');
                    \Yii::error(__METHOD__.': Disabled Product '.$productCode);
                }
            }
            \Yii::error(__METHOD__.': ZtormAPIException attempting to get '.$fieldname.' for '.$productCode.' : '.$error);
            return null;
        }

    }


    /**
     * GET PRODUCT ITEM FROM CATALOGUE
     * ================
     * @param $productCode
     * @param $fieldname
     *
     * @return mixed
     */
    public function getProductItem($productCode, $fieldname) {
        \Yii::info(__METHOD__."($productCode, $fieldname)");

        $this->productID = DigitalProduct::getEztormProductFromPartcode($productCode)->product_id;
        
        $ztormCatalogue = \common\models\ZtormCatalogueCache::find()->where(['RealProductId'=>$this->productID])->limit(1)->one();
        
        if($ztormCatalogue){
            return $ztormCatalogue->$fieldname;
        } else {
            //DigitalProduct::saveWithAuditTrail('Ztorm API tells us "Product not found". Disabling product');
            \Yii::error(__METHOD__.': Disabled Product '.$productCode.' / '.$this->productID);
            \Yii::error(__METHOD__.': ZtormAPIException attempting to get '.$fieldname.' for '.$productCode);
            return null;
        }

    }


}
