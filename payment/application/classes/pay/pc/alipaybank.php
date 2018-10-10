<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PC 端支付宝网银支付
 * Class Pay_Pc_AlipayBank
 */
class Pay_Pc_AlipayBank
{
    //支付宝即时交易目录
    private $_alipayBankDir;
    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Pc_AlipayBank-notify_url/';
    //同步通知
    const ERTURN_URL = '/callback/index/Pay_Pc_AlipayBank-return_url/';

    public function __construct()
    {
        $this->_alipayBankDir = Common::C('interface_path') . 'pc/alipay_bank/';
    }

    public function submit($data)
    {
        require_once($this->_alipayBankDir . "lib/alipay_submit.class.php");
        //支付宝配置
        $alipay_config = $this->_alipay_config();
        //格式化提交数据
        $parameter = $this->_data_format($data, $alipay_config);
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        //防钓鱼时间戳
        $parameter['anti_phishing_key'] = $alipaySubmit->query_timestamp();
        //提交数据
        $html_text = '<style>form{display: none}</style>';
        $html_text .= $alipaySubmit->buildRequestForm($parameter, "post", '');
        echo $html_text;
    }

    /**
     * 整合支付宝配置
     * @return mixed
     */
    public function _alipay_config()
    {
        //合作身份者id
        $alipay_config['partner'] = trim(Common::C('cfg_alipay_pid'));
        //收款支付宝账号
        $alipay_config['seller_email'] = trim(Common::C('cfg_alipay_account'));
        //安全检验码
        $alipay_config['key'] = trim(Common::C('cfg_alipay_key'));
        //签名方式
        $alipay_config['sign_type'] = strtoupper('MD5');
        //字符编码格式
        $alipay_config['input_charset'] = strtolower('utf-8');
        //ca证书路径地址，用于curl中ssl校验
        $alipay_config['cacert'] = $this->_alipayBankDir . 'cacert.pem';
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';
        return $alipay_config;
    }

    /**
     * 数据格式化
     * @param $data  订单详情
     * @param $conf  alipay_config 配置
     * @return array
     */
    private function _data_format($data, $conf)
    {
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => $conf['partner'], //合作身份者id
            "seller_email" => $conf['seller_email'],//收款支付宝账号
            "payment_type" => '1', //支付类型
            "notify_url" => Common::C('base_url') . self::NOTIFY_URL, //订单异步通知
            "return_url" => Common::C('base_url') . self::ERTURN_URL, //订单同步通知
            "out_trade_no" => $data['ordersn'],  //订单编号
            "subject" => $data['ordersn'], //订单标题
            'total_fee' => $data['total'],//订单总金额
            "body" => $data['remark'], //订单备注
            "paymethod" => 'bankPay', //默认支付方式
            "defaultbank" => 'ICBCB2C', //默认网银 中国工商银行
            "show_url" => Common::show_url($data['typeid'], $data['productaid']), //产品详情页
            "anti_phishing_key" => '', //防钓鱼时间戳
            "exter_invoke_ip" => Common::get_ip(), //客户端的IP地址
            "_input_charset" => trim(strtolower('utf-8')) //字符编码格式
        );
        return $parameter;
    }

    /**
     * 服务器异步通知页面路径
     */
    public function notify_url()
    {
        $bool = 'fail';
        include($this->_alipayBankDir . 'lib/alipay_notify.class.php');
        $alipay_config = $this->_alipay_config();
        $alipayNotify = new AlipayNotify($alipay_config);
        $result = $alipayNotify->verifyNotify();
        if ($result)
        {
            //判断该笔订单是否在商户网站中已经做过处理
            if ($_POST['trade_status'] == 'TRADE_SUCCESS')
            {
                $tip = '信息:支付宝网银(异)交易,订单金额与实际支付不一致';
                if (Common::total_fee_confirm($_POST['out_trade_no'], $_POST['total_fee'], $tip))
                {
                    $method = Common::C('pc');
                    Common::pay_success($_POST['out_trade_no'], $method['method']['14']['name']);
                }
                $bool = 'success';
            }
            else
            {
                new Pay_Exception("状态:{$_POST['trade_status']}");
            }
        }
        else
        {
            new Pay_Exception("信息:合法性验证失败");
        }
        return $bool;
    }

    /**
     * 页面跳转同步通知页面路径
     */
    public function return_url()
    {
        include($this->_alipayBankDir . 'lib/alipay_notify.class.php');
        $alipay_config = $this->_alipay_config();
        $alipayNotify = new AlipayNotify($alipay_config);
        $result = $alipayNotify->verifyReturn();
        if ($result)
        {
            //判断该笔订单是否在商户网站中已经做过处理
            if ($_GET['trade_status'] == 'TRADE_SUCCESS')
            {
                $tip = '信息:支付宝网银(同)交易,订单金额与实际支付不一致';
                $info['sign'] = Common::total_fee_confirm($_GET['out_trade_no'], $_GET['total_fee'], $tip) ? '11' : '23';
            }
            else
            {
                $info['sign'] = '00';
                new Pay_Exception("状态:{$_GET['trade_status']}");
            }
        }
        else
        {
            $info['sign'] = '22';
            new Pay_Exception("状态:支付宝网银(同)数据有效性验证失败");
        }
        $info['ordersn'] = $_GET['out_trade_no'];
        Common::pay_status($info);
    }
}