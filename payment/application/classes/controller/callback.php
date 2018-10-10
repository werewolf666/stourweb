<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 支付回调类
 * Class Controller_Callback
 */
class Controller_Callback extends Controller
{
    public function before()
    {
        parent::before();

    }

    /**
     * 根据URL参数实列对象
     * URL:/payment/callback/index/类名-方法名/
     */
    public function action_index()
    {
        $param = $this->request->param('param');
        $uri = explode('-', $param);
        //参数个数错误
        if (count($uri) != 2)
        {
            return;
        }
        list($class, $method) = $uri;
        //写入日志
        $this->_write_log($class, $method);
        //检测类与方式是否存在
        if (method_exists($class, $method))
        {
            $obj = new $class();
            echo $obj->$method();
        }
    }

    /**
     * 将回调参数写入日志文件
     * @param $payMethod
     * @param $method
     */
    private function _write_log($payMethod, $method)
    {
        $payLogDir = APPPATH . 'logs/pay/';
        if (!file_exists($payLogDir))
        {
            mkdir($payLogDir, 0777, true);
        }
        //日志文件
        $file = $payLogDir . str_replace('pay_','',strtolower($payMethod)) . '_' . date('ymd') . '.txt';
        $data = "\r\n[{$method}:".date('YmdHis');
        foreach($_REQUEST as $k=>$v){
            $data .=" {$k}={$v}";
        }
        $data.=']';
        file_put_contents($file, $data, FILE_APPEND);
    }
}