<?php

/**
 * Description of ProcessCustomerFile
 * Holding classes incase any preprossing or overides are required to ProcessFilesCommand
 * @author helenk
 */
namespace console\Shell;
use console\Shell\ProcessFilesCommand;
use common\models\StockItem;
use console\components\OrderFeedFile\StockItemEmailer;
use common\models\AccountSopLookup;

class ProcessOrderFile extends ProcessFilesCommand {
   
    protected function beforeProcessingFiles(){
        AccountSopLookup::truncateTable();
        
    }
    
    
    protected function afterProcessingFiles(){
        $stockitems = StockItem::getStockitemstoemail();
        $stockitememailer = new StockItemEmailer();
        $stockitememailer->notifyCustomerofNewStockItems($stockitems);
        
    }
}

?>
