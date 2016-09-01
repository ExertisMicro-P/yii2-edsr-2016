<?php

/**
 * Description of IDataLineProcessor
 * Interface used for processing a line of a file feed
 * @author helenk
 */
namespace console\components;

interface IDataLineProcessor {
    function processLine($data);
}

?>
