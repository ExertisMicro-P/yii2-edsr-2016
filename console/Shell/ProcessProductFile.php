<?php

/**
 * Description of ProcessOrderFile
 * Holding classes incase any preprossing or overides are required to ProcessFilesCommand
 * @author helenk
 */
namespace console\Shell;
use console\Shell\ProcessFilesCommand;

use common\components\DigitalPurchaser;
use common\models\DigitalProduct;
use common\models\PersistantDataLookup;
use \common\models\ZtormAccess;

class ProcessProductFile extends ProcessFilesCommand {
    /**
     * beforeProcessingFiles
     * Files found do any activities before file processing
     */
    protected function beforeProcessingFiles(){
        $digitalpurchaser = new DigitalPurchaser();
       $pd = PersistantDataLookup::getZtormTimeStamp();
       if($pd->value == '0'){  //require new total product list.
           DigitalProduct::truncateTable();
           //PersistantDataLookup::saveZtormCatalogueLookupdate();
           $digitalpurchaser = new DigitalPurchaser();
           //---------------------------------------------//
           //MUST FIND BETTER WAY TO DO THIS HK 
           //choose a store to find a product\
           
           $digitalpurchaser->getAndSaveZtormCatalogue();
       }

       //$digitalproduct = new DigitalProduct();
       //$digitalproduct->truncateTable();
    }
}

?>
