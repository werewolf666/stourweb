<?php defined('SYSPATH') or die('No direct script access.');

/**
 *  mobile 微信扫码支付
 * Class Pay_Mobile_WxPay
 */
class Pay_Mobile_WxPay
{
    //微信支付目录
    private $_wxPayDir;
    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Mobile_WxPay-notify_url/';

    /**
     * 微信支付初始化
     */
    public function __construct()
    {
        $this->_wxPayDir = Common::C('interface_path') . 'mobile/wxpay/';
        //绑定支付的APPID
        define('APPID', Common::C('cfg_wxpay_appid'));
        //商户号
        define('MCHID', Common::C('cfg_wxpay_mchid'));
        //商户支付密钥
        define('KEY', Common::C('cfg_wxpay_key'));
        //公众帐号secert
        define('APPSECRET', Common::C('cfg_wxpay_appsecret'));
    }

    /**
     * 支付数据格式化
     * @param $data
     * @return array
     */
    public function submit($data)
    {
        require $this->_wxPayDir . 'jsapi/index.php';
        $jsApiPay = new JsApiPay();
        $openId = $jsApiPay->GetOpenid();
        if (!isset($_GET['code']))
        {
            exit;
        }
        $ordersn = $this->generate_ordersn($data['ordersn']);
        $input = new WxPayUnifiedOrder();
        $input->SetBody($ordersn); //商品描述
        $input->SetAttach($data['remark']); //备注
        $input->SetOut_trade_no($ordersn); //商户订单号
        $input->SetTotal_fee($data['total'] * 100);//总金额,以分为单位
        $input->SetTime_start(date("YmdHis"));//交易起始时间
        $input->SetTime_expire(date("YmdHis", time() + 6000));//交易结束时间
        $input->SetGoods_tag("tag");//商品标记
        $input->SetNotify_url(Common::C('base_url') . self::NOTIFY_URL); //异步通知
        $input->SetTrade_type("JSAPI");
        $input->SetProduct_id($data['ordersn']);
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $jsApiPay->GetJsApiParameters($order);
        $arr = array(
            'parameter' => $jsApiParameters,
            'productname' => $data['productname'],
            'total_fee' => $data['total'],
            'ordersn' => $data['ordersn'],
            'template' => Common::C('template_dir') . 'mobile/wx_jsapi'
        );
        return $arr;
    }

    /**
     * 微信支付异步通知回调地址
     */
    public function notify_url()
    {
        require $this->_wxPayDir . 'jsapi/notify.php';
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