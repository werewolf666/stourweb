<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 移动端银联支付
 * Class Pay_Pc_ChinaBank
 */
class Pay_Mobile_ChinaBank
{
    //银联支付接口文件目录
    private $_cbPayDir;
    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Mobile_ChinaBank-notify_url/';
    //同步通知
    const ERTURN_URL = '/callback/index/Pay_Mobile_ChinaBank-return_url/';

    /**
     * 银联支付初始化
     */
    public function __construct()
    {
        $this->_cbPayDir = Common::C('interface_path') . 'mobile/chinabank/';
        // 签名证书路径
        define('SDK_SIGN_CERT_PATH', Common::C('interface_path') . 'pc/chinabank/certs/zhengshu.pfx');
        // 签名证书密码
        define('SDK_SIGN_CERT_PWD', Common::C('cfg_yinlian_new_securitykey'));
        // 密码加密证书
        define('SDK_ENCRYPT_CERT_PATH', Common::C('interface_path') . 'pc/chinabank/certs/acp_prod_enc.cer');
        // 验签证书路径
        define('SDK_VERIFY_CERT_DIR', Common::C('interface_path') . 'pc/chinabank/certs');
        //文件下载目录
        define('SDK_FILE_DOWN_PATH', $this->_cbPayDir . 'file/');
        //日志 目录
        define('SDK_LOG_FILE_PATH', $this->_cbPayDir . 'logs/');
        //前台通知地址
        define('SDK_FRONT_NOTIFY_URL', Common::C('base_url') . self::ERTURN_URL);
        //后台通知地址
        define('SDK_BACK_NOTIFY_URL', Common::C('base_url') . self::NOTIFY_URL);
    }

    /**
     * 提交支付参数
     */
    public function submit($data)
    {
        // 初始化日志
        require $this->_cbPayDir . '/sdk/acp_service.php';
        //格式化参数
        $params = array(
            'version' => '5.0.0',
            'encoding' => 'utf-8',
            'certId' => getSignCertId(SDK_SIGN_CERT_PATH, SDK_SIGN_CERT_PWD),
            'txnType' => '01',
            'txnSubType' => '01',
            'bizType' => '000201',
            'frontUrl' => SDK_FRONT_NOTIFY_URL,
            'backUrl' => SDK_BACK_NOTIFY_URL,
            'signMethod' => '01',
            'channelType' => '08',
            'accessType' => '0',
            'currencyCode' => '156',
            'merId' => Common::C('cfg_yinlian_new_name'),
            'orderId' => $data['ordersn'],
            'txnTime' => date('YmdHis'),
            'txnAmt' => $data['total'] * 100,
        );
        AcpService::sign($params);
        $uri = SDK_FRONT_TRANS_URL;
        $html_form = AcpService::createAutoFormHtml($params, $uri);
        echo $html_form;
    }

    /**
     * 服务器异步通知页面路径
     */
    public function notify_url()
    {
        $bool = 'failure';
        include $this->_cbPayDir . '/sdk/acp_service.php';
        if (AcpService::validate($_POST))
        {
            if ($_POST ['respCode'] == '00' || $_POST ['respCode'] == 'A6')
            {
                $tip = '信息:银联移动版支付(异)交易,订单金额与实际支付不一致';
                if (Common::total_fee_confirm($_POST['orderId'], $_POST['txnAmt'] / 100, $tip))
                {
                    $method = Common::C('pc');
                    Common::pay_success($_POST['orderId'], $method['method']['4']['name']);
                }
                $bool = 'success';
            }
            else
            {
                new Pay_Exception("状态:{$_POST ['respMsg']}");
            }
        }
        else
        {
            new Pay_Exception("状态:银联移动版支付(异)数据有效性验证失败");
        }
        return $bool;
    }

    /**
     * 页面跳转同步通知页面路径
     */
    public function return_url()
    {
        include $this->_cbPayDir . '/sdk/acp_service.php';
        if (AcpService::validate($_POST))
        {
            if ($_POST ['respCode'] == '00' || $_POST ['respCode'] == 'A6')
            {
                $tip = '信息:银联移动版支付(同)交易,订单金额与实际支付不一致';
                $info['sign'] = Common::total_fee_confirm($_POST['orderId'], $_POST['txnAmt'] / 100, $tip) ? '11' : '23';
            }
            else
            {
                new Pay_Exception("状态:{$_POST ['respMsg']}");
            }
        }
        else
        {
            $info['sign'] = '22';
            new Pay_Exception("状态:银联移动版支付(同)数据有效性验证失败");

        }
        $info['ordersn'] = $_POST['orderId'];
        Common::pay_status($info);
    }
}