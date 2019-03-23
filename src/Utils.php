<?php
require_once 'Settings.php';

abstract class Utils
{
    public static $debugLogger = null;
    public static function addDebugLog($error)
    {
        if (!Utils::$debugLogger) {
            Utils::$debugLogger = new Logger('test',Settings::DEBUG_LOG_FILE);
        }
        Utils::$debugLogger->addLine($error);
    }
}
?>
