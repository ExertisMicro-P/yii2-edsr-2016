<?php

namespace console\controllers;
use yii\console\Controller;
use console\Shell\ProcessProductFile;
use console\components\ProductFeedFile\ProductFileParser;
use console\components\ProductFeedFile\ProductCSVProcessor;
use \common\models\ZtormAccess;
use common\components\DigitalPurchaser;

/**
 * Handles action to do with uploading product feed file
 * Author H Kappes
 * Methods: actionUploadfeed.
 *
 */
class ProductfileuploaderController extends Controller
{
    /**
    * actionUploadfeed()
    * Uploads any files with from the uploads in folder with a filename pattern as defined in action
    * To run this in netbeans
    * Run As Script
    * Index File : Yii
    * Arguments: productfileuploader/uploadfeed
    *
    */
    public function actionUploadfeed()
    {
        //Do we truncate table ?
      //  $tablectrl = new testcustomertable();
      //  $tablectrl->down();
        //configurator for processing the data from file
        $configurator = new ProductFileParser();
        $filenamepattern = '/^PACMAN_PRODUCTS_/';
        $seperator = ',';
        //processor for handling the file
        $processor = new ProductCSVProcessor($configurator,$seperator );
        //collection of the correct files from right place.
        $uploader = new ProcessProductFile($processor,$filenamepattern);
        $uploader->run();
    }

   public function actionGetdeltacatalogue(){
           $digitalpurchaser = new DigitalPurchaser();
           $digitalpurchaser->getAndSaveZtormCatalogue();
    } // actionGetdeltacatalogue


   /**
    * RCH 20150408
    * Added to allow us to see the catalogue for debug purposes
    * 
    * Call this as a console command
    */
   public function actionGetcatalogue(){
           $digitalpurchaser = new DigitalPurchaser();
           //get the store
           $store = ZtormAccess::findOne(['type'=>'TEST', 'storealias'=>'EXERTIS']);
           $products = $digitalpurchaser->getZtormCatalogue($store);
           $dump = array();
           $msg = __METHOD__. 'PRODUCTS for STORE '. $store->getType() . ' ' . $store->getstorealias();
           $dump[] = $msg;
           \Yii::info(__METHOD__. 'PRODUCTS for STORE '. $store->getType() . ' ' . $store->getstorealias());
           foreach($products as $product){
               $msg = __METHOD__ . 'PRODUCT' . print_r($product->attributes,true);
               $dump[] = $msg;
               \Yii::info($msg);
            }
            print_r($dump);
           } // actionGetcatalogue
}
