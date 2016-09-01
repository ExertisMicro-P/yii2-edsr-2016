<?php

/**
 * Description of CustomerCSVProcessor
 * Sets up which configurator and seperator to use for readng the CustomerFile
 * @author helenk
 */
namespace console\components\CustomerFeedFile;
use console\components\CSVProcessor;
class CustomerCSVProcessor extends CSVProcessor {

    function __construct() {
        parent::__construct(new CustomerFileParser(), '|');
    }
}

?>
