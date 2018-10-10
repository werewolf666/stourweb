<?php defined('SYSPATH') or die('No direct script access.');

/**
 * mobile 支付
 * Class Pay_Mobile_Alipay
 */
class Pay_Mobile_Alipay
{
    //支付宝即时交易目录
    private $_AlipayDir;
    //异步通知
    private $_notify_url;
    //同步通知
    private $_return_url;
    //操作中断返回地址
    private $_error_url;
    //Mobile配置
    private $_mobile_config;

    public function __construct()
    {
        $this->_AlipayDir = Common::C('interface_path') . 'mobile/alipay/';
        $this->_notify_url = Common::C('base_url') . '/callback/index/Pay_Mobile_Alipay-notify_url/';
        $this->_return_url = Common::C('base_url') . '/callback/index/Pay_Mobile_Alipay-return_url/';
        $this->_error_url = Common::C('base_url') . '/callback/index/Pay_Mobile_Alipay-error_url/';
        $this->_mobile_config = Common::C('mobile');
    }

    public function submit($data)
    {
        require_once($this->_AlipayDir . "lib/alipay_submit.class.php");
        //支付宝配置
        $alipay_config = $this->_alipay_config();
        //格式化提交数据
        $parameter = $this->_data_code($data, $alipay_config);
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        //获取token
        $html_text = $alipaySubmit->buildRequestHttp($parameter);
        $html_text = urldecode($html_text);
        //获取request_token
        $para_html_text = $alipaySubmit->parseResponse($html_text);
        $request_token = $para_html_text['request_token'];
        //根据request_token重新封装数据
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        $parameter["req_data"] = $req_data;
        $parameter["service"] = "alipay.wap.auth.authAndExecute";
        //提交数据
        $html_text = '<style>form{display: none}</style>';
        $html_text .= $alipaySubmit->buildRequestForm($parameter, "get", '确认');
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
        //$alipay_config['seller_id'] = $alipay_config['partner'];
        //签名方式
        $alipay_config['sign_type'] = strtoupper('MD5');
        //字符编码格式
        $alipay_config['input_charset'] = strtolower('utf-8');
        //ca证书路径地址，用于curl中ssl校验
        $alipay_config['cacert'] = $this->_AlipayDir . 'cacert.pem';
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';
        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = trim(Common::C('cfg_alipay_key'));
        //返回参数
        return $alipay_config;
    }

    /**
     * 数据格式化
     * @param $data  订单详情
     * @param $conf  alipay_config 配置
     * @return array
     */
    private function _data_code($data, $conf)
    {
        $req_data = '<direct_trade_create_req><notify_url>' . $this->_notify_url . '</notify_url><call_back_url>' . $this->_return_url . '</call_back_url><seller_account_name>' . Common::C('cfg_alipay_account') . "</seller_account_name><out_trade_no>{$data['ordersn']}</out_trade_no><subject>{$data['ordersn']}</subject><total_fee>{$data['total']}</total_fee><merchant_url>" . $this->_error_url . "</merchant_url></direct_trade_create_req>";
        $parameter = array(
            "service" => "alipay.wap.trade.create.direct",
            "partner" => $conf['partner'],
            "sec_id" => trim($conf['sign_type']),
            "format" => 'xml',
            "v" => '2.0',
            "req_id" => date('Ymdhis'),
            "req_data" => $req_data,
            "_input_charset" => strtolower('utf-8')
        );
        return $parameter;
    }

    /**
     * 服务器异步通知页面路径
     */
    public function notify_url()
    {
        $bool = 'fail';
        include($this->_AlipayDir . 'lib/alipay_notify.class.php');
        $alipay_config = $this->_alipay_config();
        $alipayNotify = new AlipayNotify($alipay_config);
        $result = $alipayNotify->verifyNotify();
        if ($result)
        {
            $doc = new DOMDocument();
            if ($alipay_config['sign_type'] == 'MD5')
            {
                $doc->loadXML($_POST['notify_data']);
            }
            if ($alipay_config['sign_type'] == '0001')
            {
                $doc->loadXML($alipayNotify->decrypt($_POST['notify_data']));
            }
            if (!empty($doc->getElementsByTagName("notify")->item(0)->nodeValue))
            {
                //商户订单号
                $out_trade_no = $doc->getElementsByTagName("out_trade_no")->item(0)->nodeValue;
                //交易状态
                $trade_status = $doc->getElementsByTagName("trade_status")->item(0)->nodeValue;
                //总金额
                $total_fee = $doc->getElementsByTagName("total_fee")->item(0)->nodeValue;
                //流水号
                $trade_no = $doc->getElementsByTagName("trade_no")->item(0)->nodeValue;
                if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS')
                {
                    if (Common::total_fee_confirm($out_trade_no, $total_fee, '信息:支付宝手机支付(异),订单金额与实际支付不一致'))
                    {
                        $method = Common::C('mobile');
                        Common::pay_success($out_trade_no, $method['method']['1']['name']);
                        //写入支付宝流水号
                        $online_transaction_no = array('source'=>'alipay','transaction_no'=>$trade_no);
                        DB::update('member_order')->set(array('online_transaction_no'=>json_encode($online_transaction_no)))
                            ->where('ordersn','=',$out_trade_no)
                            ->execute();

                    }
                    $bool = 'success';
                }
                else
                {
                    new Pay_Exception("状态:{$trade_status}");
                }
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
        include($this->_AlipayDir . 'lib/alipay_notify.class.php');
        $alipay_config = $this->_alipay_config();
        $alipayNotify = new AlipayNotify($alipay_config);
        $result = $alipayNotify->verifyReturn();
        if ($result)
        {
            $info['sign'] =$_GET['result'] == 'success'?'11':'00';
        }
        else
        {
            $info['sign'] = '22';
            new Pay_Exception("信息:支付宝手机支付(同)合法性验证失败");

        }
        $info['ordersn'] = $_GET['out_trade_no'];
        Common::pay_status($info);
    }

    /**
     * 支付终端操作
     */
    public function error_url()
    {
        $info['sign'] = '01';
        $info['ordersn'] = $_GET['out_trade_no'];
        Common::pay_status($info);
    }
}