<?php



/**
 * Description of ProcessCustomerFile
 * Holding classes incase any preprossing or overides are required to ProcessFilesCommand
 *
 * @author helenk
 */
namespace console\Shell;
use console\Shell\ProcessFilesCommand;
use common\models\Customer;

class ProcessCustomerFile extends ProcessFilesCommand {

    /**
     * beforeProcessingFiles
     * Files found do any activities before file processing
     */
    protected function beforeProcessingFiles(){
        Customer::truncateTable();
    }
}

?>
