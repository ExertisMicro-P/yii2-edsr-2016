<?php

/**
 * Description of ProductCSVProcessor
 * Sets up which configurator and seperator to use for reading the OrderFile
 * Reads the Product file feed
 * @author helenk
 */
namespace console\components\ProductFeedFile;
use console\components\CSVProcessor;
use console\components\ProductFeedFile\ProductFileParser;
class ProductCSVProcessor extends CSVProcessor {

    function __construct() {
        parent::__construct(new ProductFileParser(), ',');
    }
}

?>
