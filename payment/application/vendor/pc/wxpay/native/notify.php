<?php defined('SYSPATH') or die('No direct script access.');
require_once dirname(dirname(__FILE__)) . "/lib/WxPay.Api.php";
require_once dirname(dirname(__FILE__)) . '/lib/WxPay.Notify.php';

/**
 * 微信支付回调类
 * Class notify
 */
class notify extends WxPayNotify
{
    /**
     * 重写父类异步验证
     * @param array $data
     * @param string $msg
     * @return bool
     */
    public function NotifyProcess($data, &$msg)
    {
        $bool = false;
        $this->_write_log('Pay_Pc_WxPay', 'notify_url', $data);
        //返回状态码、业务结果
        if (array_key_exists("return_code", $data) && array_key_exists("result_code", $data) && $data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS')
        {
            //查询订单
            if (isset($data["out_trade_no"]) && $data["out_trade_no"] != "")
            {
                $input = new WxPayOrderQuery();
                $input->SetOut_trade_no($data["out_trade_no"]);//商户订单号
                $result = WxPayApi::orderQuery($input);
                $tip = '信息:微信扫码交易,订单金额与实际支付不一致';
                //这里针对微信订单号作特殊处理,去掉后面的16位字符
                $ordersn = substr($data['out_trade_no'],0,strlen($data['out_trade_no'])-16);
                if (isset($result['total_fee']) && Common::total_fee_confirm($ordersn, $result['total_fee'] / 100, $tip))
                {
                    $bool = true;
                    $method = Common::C('pc');
                    Common::pay_success($ordersn, $method['method']['8']['name']);
                    $online_transaction_no = array('source'=>'wxpay','transaction_no'=>$data['transaction_id']);
                    //写入微信订单号
                    DB::update('member_order')->set(array('online_transaction_no'=>json_encode($online_transaction_no)))
                        ->where('ordersn','=',$ordersn)
                        ->execute();

                }
            }
            else
            {
                new Pay_Exception("信息:微信扫码下单,未会返回商品订单号");
            }
        }
        else
        {
            new Pay_Exception("信息:微信扫码交易错误({$data['return_msg']})");
        }
        return $bool;
    }

    /**
     * 将回调参数写入日志文件
     * @param $payMethod
     * @param $method
     */
    private function _write_log($payMethod, $method, $params)
    {
        $payLogDir = APPPATH . 'logs/pay/';
        if (!file_exists($payLogDir))
        {
            mkdir($payLogDir, 0777, true);
        }
        //日志文件
        $file = $payLogDir . str_replace('pay_', '', strtolower($payMethod)) . '_' . date('ymd') . '.txt';
        $data = "\r\n[{$method}:" . date('YmdHis');
        foreach ($params as $k => $v)
        {
            $data .= " {$k}={$v}";
        }
        $data .= ']';
        file_put_contents($file, $data, FILE_APPEND);
    }
}