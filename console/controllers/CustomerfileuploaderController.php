<?php

namespace console\controllers;
use yii\console\Controller;
use console\Shell\ProcessCustomerFile;
use console\components\CustomerFeedFile\CustomerFileParser;
use console\components\CustomerFeedFile\CustomerCSVProcessor;

/**
 * Handles action to do with uploading  customer feed file
 * Author H Kappes
 * Methods: actionUploadfeed.
 * 
 */
class CustomerfileuploaderController extends Controller
{
   /**
    * actionUploadfeed()
    * Uploads any files with from the uploads in folder with a filename pattern as defined in action
    * To run this in netbeans 
    * Run As Script
    * Index File : Yii
    * Arguments: customerfileuploader/uploadfeed
    * 
    */ 
    public function actionUploadfeed()
    {
        
        //@TODO DROP TABLE
        //configurator for processing the data from file
        $configurator = new CustomerFileParser();
        $filenamepattern = '/^ECO_CUSTOMER[^\.]+.dat/';
        $seperator = '|';
        //processor for handling the file
        $processor = new CustomerCSVProcessor($configurator,$seperator );
        //collection of the correct files from right place.
        $uploader = new ProcessCustomerFile($processor,$filenamepattern);
        $uploader->run();
    }
    
  

}
