<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderCSVProcessor
 * Sets up which configurator and seperator to use for reading the OrderFile
 * Reads the Order file feed
 * @author helenk
 */
namespace console\components\OrderFeedFile;

use console\components\CSVProcessor;
use console\components\OrderFeedFile\OrderFileParser;
class OrderCSVProcessor extends CSVProcessor {

function __construct() {
        parent::__construct(new OrderFileParser(), ',');
    }
}