<?php

namespace console\controllers;
use yii\console\Controller;
use console\Shell\ProcessOrderFile;
use console\components\OrderFeedFile\OrderFileParser;
use console\components\OrderFeedFile\OrderCSVProcessor;
use \common\components\DigitalPurchaser;
use \common\models\StockItem;

/**
 * Handles action to do with uploading  customer feed file
 * Author H Kappes
 * Methods: actionUploadfeed.
 * 
 */
class OrderfileuploaderController extends Controller
{
    const filenamepattern = '/^ECO_NEW_ORDER/';
    const seperator = ',';
    /**
    * actionUploadfeed()
    * Uploads any files with from the uploads in folder with a filename pattern as defined in action
    * To run this in netbeans 
    * Run As Script
    * Index File : Yii
    * Arguments: orderfileuploader/uploadfeed
    * 
    */ 
    public function actionUploadfeed()
    {
        //Do we truncate table ?
      //  $tablectrl = new customertable();
      //  $tablectrl->down();
        //configurator for processing the data from file
        echo 'MODE : ' . \Yii::$app->params['storeType'];
        $configurator = new OrderFileParser();
        //processor for handling the file
        $processor = new OrderCSVProcessor($configurator,self::seperator );
        //collection of the correct files from right place.
        $uploader = new ProcessOrderFile($processor,self::filenamepattern);
        $uploader->run();
    }
    
    public function actionTestgetkey(){
        // test stockitem
        $stockitem = StockItem::findone(['id'=>'2234']);
        $key = DigitalPurchaser::getProductInstallKey($stockitem);   
        echo $key;
    }
}
