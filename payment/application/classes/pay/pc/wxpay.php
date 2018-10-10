<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PC 端微信扫码支付
 * Class Pay_Pc_WxPay
 */
class Pay_Pc_WxPay
{
    //微信支付目录
    private $_wxPayDir;
    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Pc_WxPay-notify_url/';

    /**
     * 微信支付初始化
     */
    public function __construct()
    {
        $this->_wxPayDir = Common::C('interface_path') . 'pc/wxpay/';
        //绑定支付的APPID
        define('APPID', Common::C('cfg_wxpay_appid'));
        //商户号
        define('MCHID', Common::C('cfg_wxpay_mchid'));
        //商户支付密钥
        define('KEY', Common::C('cfg_wxpay_key'));
        //公众帐号secert
        define('APPSECRET', Common::C('cfg_wxpay_appsecret'));
        //证书路径,注意应该填写绝对路径
        define('SSLCERT_PATH', Common::C('interface_path') . 'mobile/wxpay/cert/apiclient_cert.pem');

        define('SSLKEY_PATH', Common::C('interface_path') . 'mobile/wxpay/cert/apiclient_key.pem');
    }

    /**
     * 支付数据提交
     * @param $data
     */
    public function submit($data)
    {
        require $this->_wxPayDir . 'native/index.php';
        $html = false;
        $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $ordersn = $this->generate_ordersn($data['ordersn']);
        $input->SetBody($ordersn); //商品描述
        $input->SetAttach($data['remark']); //备注
        $input->SetOut_trade_no($ordersn); //商户订单号
        $input->SetTotal_fee($data['total'] * 100);//总金额,以分为单位
        $input->SetTime_start(date("YmdHis"));//交易起始时间
        $input->SetTime_expire(date("YmdHis", time() + 600));//交易结束时间
        $input->SetGoods_tag("");//商品标记
        $input->SetNotify_url(Common::C('base_url') . self::NOTIFY_URL); //异步通知
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($data['ordersn']);
        try
        {
            $result = $notify->GetPayUrl($input);
            if ($result['return_code'] == 'FAIL')
            {
                new Pay_Exception($result['return_msg']);
            }
            $src = Common::C('base_url') . '/public/qrcode/make.php?param=' . urlencode($result["code_url"]);
            //获取扫码页模板内容
            $content = file_get_contents(APPPATH . 'views/' . Common::C('template_dir') . 'pc/wx_native.php');
            //返回替换后的HTML
            $html = str_replace('{src}', $src, $content);
            $html = str_replace('{ordersn}', $data['ordersn'], $html);
            $html = str_replace('{sign}', md5('11'), $html);
        } catch (WxPayException $e)
        {
            new Pay_Exception($e->errorMessage());
        }
        return $html;
    }

    /**
     * 微信支付异步通知回调地址
     */
    public function notify_url()
    {
        require $this->_wxPayDir . 'native/notify.php';
        $notify = new notify();
        $notify->Handle(true);
    }

    /**
     * @function 生成微信支付订单号(规则:原订单号+当前时间time()+6位随机数)
     * @param $ordersn
     * @return 返回32位订单号.
     */
    private function generate_ordersn($ordersn)
    {
        $rand_num = St_Math::get_random_number(6);
        return $ordersn.time().$rand_num;

    }
}