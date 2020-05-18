<?php
class Logger
{
    public $filePointer = null;

    public function __construct($fileName = '',$logFilePath = '')
    {
        if ($fileName && $logFilePath) {
            $logFileName  = $fileName.'_'.date('YmdHis').'.log';
            $logFilePath .= $logFileName;
        } else {
            $logFilePath = Settings::DEBUG_LOG_FILE;
        }
        $this->filePointer = fopen($logFilePath, 'a');
        chmod($logFilePath, 0666);
        if (!$this->filePointer) {
            echo "Error: Could not create Log file \n";
            exit;
        }
    }

    public function addLine($line = '')
    {
        $line = '['.date('Y-m-d H:i:s').'] '.$line;
        fwrite($this->filePointer, $line."\n");
    }

    public function __destruct()
    {
        if ($this->filePointer) {
            fclose($this->filePointer);
        }
    }
}
?>
