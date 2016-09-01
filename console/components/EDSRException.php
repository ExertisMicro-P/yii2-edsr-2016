<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParserException
 *
 * @author helenk
 */
namespace console\components;
use yii\db\Exception;
use Yii;
//@TODO RETHINK THIS
class EDSRException extends Exception {
    
     function __construct($code, $message) {
        parent::__construct($message, $code, $code);
        Yii::info($message,__METHOD__);
    }
    
}

?>
