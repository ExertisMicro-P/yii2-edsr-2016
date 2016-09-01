<?php

/**
 * MPFileLogRoute extends CFileLogRoute with setting correct group permissions.
 *
 * @see http://www.yiiframework.com/forum/index.php/topic/8639-permissions-of-application-log/
 */
class MPFileLogRoute extends CFileLogRoute {

    /**
     * After processing the logs, chmod the resulting file so that it is
     *   group-writeable.
     */
    protected function processLogs($logs)
    {
        Yii::log("Processing custom logs start", CLogger::LEVEL_TRACE);
        parent::processLogs($logs);
        $logFile=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
        @chmod($logFile, 0664);
        Yii::log("Processing custom logs end", CLogger::LEVEL_TRACE);
    }
}