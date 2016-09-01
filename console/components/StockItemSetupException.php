<?php

/**
 * Description of CampaignException
 * Custome exception used throughout the engine to highlight when specific failure cases occurr
 * @author helenk
 */
namespace console\components;
use console\components\EDSRException;
use Yii;

class StockItemSetupException extends EDSRException {
    
    
    function __construct($code, $message) {
        parent::__construct($message, $code);
        Yii::info($message,__METHOD__);
    }
} 


?>
