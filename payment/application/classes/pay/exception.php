<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 支付异常类 写入支付日志
 * Class Pay_Exception
 */
class Pay_Exception extends Exception
{
    //日志目录
    private $_message;
    private $_code;

    //异常构造函数
    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
        $this->_message = $message;
        $this->_code = $code;
        $this->write_log();
    }

    //写入日志
    function write_log()
    {
        $logPayDir= APPPATH.Common::C('log_path');
        if(!file_exists($logPayDir)){
            mkdir($logPayDir,0666);
        }
        $file =$logPayDir. date('Ymd') . '.php';
        $time = date('Y-m-d H:i:s');
        $errorFile=$this->getFile();
        $line=$this->getLine();
        $message=$this->getMessage();
        $logFormat = <<<LOG
#time:$time
#message:$message
#file:$errorFile [$line]
LOG;
        file_put_contents($file, PHP_EOL.$logFormat.PHP_EOL, FILE_APPEND);
    }

}