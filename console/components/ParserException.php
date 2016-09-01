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
use Yii;

class ParserException extends \yii\db\Exception {
    
     function __construct($code, $message) {
        parent::__construct($message, $code);
        Yii::info($message,__METHOD__);
    }
    
}

?>
