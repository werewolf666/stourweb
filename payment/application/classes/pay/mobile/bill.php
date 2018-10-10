<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 移动端快钱支付
 * Class Pay_Mobile_Bill
 */
class Pay_Mobile_Bill
{

    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Mobile_Bill-notify_url/';
    //同步通知
    const ERTURN_URL = '/callback/index/Pay_Mobile_Bill-return_url/';

    public function __construct()
    {
        //RSA 签名计算
        define('RSA_FILE', Common::C('interface_path') . 'pc/bill/cert/99bill-rsa.pem');

        define('KEY_FILE', Common::C('interface_path') . 'pc/bill/cert/public-rsa.cer');
    }

    /**
     * 支付数据提交
     * @param $data
     */
    public function submit($data)
    {
        $parameter = $this->_data_format($data);
        //过滤出非空值
        $parameter = array_filter($parameter);
        //封装签名
        $string = '';
        foreach ($parameter as $k => $v)
        {
            $string .= $this->_kq_ck_null($k, $v);
        }
        $string = rtrim($string, '&');
        //RSA签名
        $priv_key = file_get_contents(RSA_FILE);
        $pkeyid = openssl_get_privatekey($priv_key);
        openssl_sign($string, $signMsg, $pkeyid, OPENSSL_ALGO_SHA1);
        openssl_free_key($pkeyid);
        $parameter['signMsg'] = base64_encode($signMsg);
        //提交数据
        echo $this->_buildRequestForm($parameter);
    }

    /**
     * 数据格式化
     * @param $data 订单详情
     * @return array
     */
    private function _data_format($data)
    {
        $parameter = array(
            "inputCharset" => '1',//编码方式 1代表 UTF-8; 2 代表 GBK; 3代表 GB2312
            "pageUrl" => Common::C('base_url') . self::ERTURN_URL,//接收支付结果的页面地址
            "bgUrl" => Common::C('base_url') . self::NOTIFY_URL,//服务器接收支付结果的后台地址
            "version" => 'mobile1.0',//网关版本，固定值：v2.0,
            "language" => '1',//语言种类，1代表中文显示，2代表英文显示
            "signType" => '4',//签名类型
            "merchantAcctId" => Common::C('cfg_bill_account') . '01',//人民币网关账号，该账号为11位人民币网关商户编号+01。
            "payerName" => '',//支付人姓名，可选
            "payerContactType" => '',//支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式，可选
            "payerContact" => '',//支付人联系方式，可选
            "payerIdType" => '',//指定付款人
            "payerId" => '',//付款人标识
            "orderId" => $data['ordersn'],//商户订单号
            "orderAmount" => $data['total'] * 100,//订单金额，金额以“分”为单位
            "orderTime" => date("YmdHis"),//订单提交时间
            "productName" => $data['ordersn'],//商品名称，可选
            "productNum" => '1',//商品数量，可选
            "productId" => '',//商品代码，可选
            "productDesc" => '',//商品描述，可选
            "ext1" => '',//扩展字段1，商户可以传递自己需要的参数，支付完快钱会原值返回，可选
            "ext2" => '',//扩展自段2，可选
            "payType" => '21',//支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10。
            "bankId" => '',//银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表，可选
            "redoFlag" => '',//同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交，可选
            "pid" => ''//快钱合作伙伴的帐户号，即商户编号，可选
        );
        return $parameter;
    }

    /**
     * 返回生成的表单
     * @param $paras
     * @return string
     */
    private function _buildRequestForm($paras)
    {
        $html = '<form name="kqPay" method="GET" action="https://www.99bill.com/mobilegateway/recvMerchantInfoAction.htm" accept-charset="utf-8">';
        foreach ($paras as $k => $v)
        {
            $html .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
        }
        $html .= '</form>';
        $html .= "<script>document.forms['kqPay'].submit();</script>";
        return $html;
    }

    /**
     * 服务器异步通知页面路径
     */
    public function notify_url()
    {
        if ($this->_verify())
        {
            if ($_REQUEST['payResult'] == '10')
            {
                if (Common::total_fee_confirm($_REQUEST['orderId'], $_REQUEST['payAmount'] / 100, '信息:快钱(异)交易,订单金额与实际支付不一致'))
                {
                    $rtnOK = 1;
                    $sign = 11;
                    $method = Common::C('mobile');
                    Common::pay_success($_REQUEST['orderId'], $method['method']['2']['name']);
                }
                else
                {
                    $rtnOK = 0;
                    $sign = 23;
                }
            }
            else
            {
                $rtnOK = 0;
                $sign = 00;
                new Pay_Exception("状态:{$_REQUEST['payResult']}");
            }
        }
        else
        {
            $rtnOK = 0;
            $sign = 22;
            new Pay_Exception("信息:合法性验证失败");
        }
        $rtnUrl = Common::get_main_host() . "/payment/status/?ordersn ={$_REQUEST['orderId']}&sign=" . md5($sign);
        echo "<result>{$rtnOK}</result><redirecturl>{$rtnUrl}</redirecturl>";
    }

    /**
     * 页面跳转同步通知页面路径
     */
    public function return_url()
    {
        if ($this->_verify())
        {
            if ($_REQUEST['payResult'] == '10')
            {
                $tip = '信息:快钱(同)交易,订单金额与实际支付不一致';
                $info['sign'] = Common::total_fee_confirm($_REQUEST['orderId'], $_REQUEST['payAmount'] / 100, $tip) ? '11' : '23';
            }
            else
            {
                $info['sign'] = '00';
                new Pay_Exception("状态:{$_REQUEST['payResult']}");
            }
        }
        else
        {
            $info['sign'] = '22';
        }
        $info['ordersn'] = $_REQUEST['orderId'];
        Common::pay_status($info);
    }

    /**
     * 数字签名验证
     * @return bool
     */
    private function _verify()
    {
        $bool = false;
        $kq_check_all_para = $this->_kq_ck_null('merchantAcctId', $_REQUEST['merchantAcctId']);
        $kq_check_all_para .= $this->_kq_ck_null('version', $_REQUEST['version']);
        $kq_check_all_para .= $this->_kq_ck_null('language', $_REQUEST['language']);
        $kq_check_all_para .= $this->_kq_ck_null('signType', $_REQUEST['signType']);
        $kq_check_all_para .= $this->_kq_ck_null('payType', $_REQUEST['payType']);
        $kq_check_all_para .= $this->_kq_ck_null('bankId', $_REQUEST['bankId']);
        $kq_check_all_para .= $this->_kq_ck_null('orderId', $_REQUEST['orderId']);
        $kq_check_all_para .= $this->_kq_ck_null('orderTime', $_REQUEST['orderTime']);
        $kq_check_all_para .= $this->_kq_ck_null('orderAmount', $_REQUEST['orderAmount']);
		$kq_check_all_para .= $this->_kq_ck_null('bindCard', $_REQUEST['bindCard']);
        $kq_check_all_para .= $this->_kq_ck_null('bindMobile', $_REQUEST['bindMobile']);
        $kq_check_all_para .= $this->_kq_ck_null('dealId', $_REQUEST['dealId']);
        $kq_check_all_para .= $this->_kq_ck_null('bankDealId', $_REQUEST['bankDealId']);
        $kq_check_all_para .= $this->_kq_ck_null('dealTime', $_REQUEST['dealTime']);
        $kq_check_all_para .= $this->_kq_ck_null('payAmount', $_REQUEST['payAmount']);
        $kq_check_all_para .= $this->_kq_ck_null('fee', $_REQUEST['fee']);//费用，快钱收取商户的手续费，单位为分。
        $kq_check_all_para .= $this->_kq_ck_null('ext1', $_REQUEST['ext1']);
        $kq_check_all_para .= $this->_kq_ck_null('ext2', $_REQUEST['ext2']);
        $kq_check_all_para .= $this->_kq_ck_null('payResult', $_REQUEST['payResult']);//处理结果
        $kq_check_all_para .= $this->_kq_ck_null('errCode', $_REQUEST['errCode']);//错误代码
        $trans_body = rtrim($kq_check_all_para, '&');
        //验签
        $mac = base64_decode($_REQUEST['signMsg']);
        $cert = file_get_contents(KEY_FILE);
        $pubkeyid = openssl_get_publickey($cert);
        $ok = openssl_verify($trans_body, $mac, $pubkeyid);
        if ($ok == 1)
        {//签名验证
            $bool = true;
        }
        return $bool;
    }

    /**
     * 块钱去除值为空的参数
     * @param $kq_va
     * @param $kq_na
     * @return string
     */
    private function  _kq_ck_null($kq_na, $kq_va)
    {
        if ($kq_va == "")
        {
            return $kq_va = "";
        }
        else
        {
            return $kq_va = ($kq_na . '=' . $kq_va . '&');
        }
    }
}