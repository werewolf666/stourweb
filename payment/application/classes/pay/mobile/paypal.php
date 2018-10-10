<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 移动端贝宝支付
 * Class Pay_Mobile_Bill
 */
class Pay_Mobile_Paypal
{

    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Mobile_Paypal-notify_url/';
    //同步通知
    const RETURN_URL = '/callback/index/Pay_Mobile_Paypal-return_url/';

    public function __construct()
    {

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
            'cmd' => '_xclick',
            'business'=>Common::C('cfg_paypal_key'),
            'currency_code'=>Common::C('cfg_paypal_currency'),
            'item_name'=>$data['productname'],
            'item_number'=>$data['ordersn'],
            'no_shipping' => '1',
            'no_note' => '0',
            'charset' => 'UTF-8',
            'amount'=>number_format($data['total'],2),
            'return'=> Common::C('base_url').self::RETURN_URL,
            'notify_url'=> Common::C('base_url').self::NOTIFY_URL,
            'rm'=>2
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
        $html = '<!doctype html>';
        $html.='<html><head><meta charset="utf-8"><title>支付</title></head><body>';
        $html.='<form name="paypalPay" method="POST" action="https://www.paypal.com/cgi-bin/webscr">';
        foreach ($paras as $k => $v)
        {
            $html .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
        }
        $html .= '</form></body></html>';
        $html .= "<script>document.forms['paypalPay'].submit();</script>";
        return $html;
    }

    /**
     * 服务器异步通知页面路径
     */
    public function notify_url()
    {

        if($this->_verify())
        {
            $item_number = $_POST['item_number'];
            $payment_status = $_POST['payment_status'];
            $payment_amount = $_POST['mc_gross'];
            //$payment_currency = $_POST['mc_currency'];
            //$txn_id = $_POST['txn_id'];
            //$receiver_email = $_POST['receiver_email'];
            //$payer_email = $_POST['payer_email'];

            if ($payment_status == 'Completed' && Common::total_fee_confirm($item_number, $payment_amount, '信息:贝宝交易,订单金额与实际支付不一致')) {
                $method = Common::C('mobile');
                Common::pay_success($item_number, $method['method']['7']['name']);
            }
        }


    }

    /**
     * 页面跳转同步通知页面路径
     */
    public function return_url()
    {
        header("Location:".rtrim(Common::get_main_host(),'/').'/member/');
        exit;
    }

    /**
     * 数字签名验证
     * @return bool
     */
    private function _verify()
    {

        define("DEBUG", 1);
        define("USE_SANDBOX", 0);
        define("LOG_FILE", "./ipn.log");


        // Read POST data
        // reading posted data directly from $_POST causes serialization
        // issues with array data in POST. Reading raw POST data from input stream instead.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        if(function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        // Post IPN data back to PayPal to validate the IPN data is genuine
        // Without this step anyone can fake IPN data

        if(USE_SANDBOX == true) {
            $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
        }

        $ch = curl_init($paypal_url);
        if ($ch == FALSE) {
            return FALSE;
        }

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

        if(DEBUG == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        }

        // CONFIG: Optional proxy configuration
        //curl_setopt($ch, CURLOPT_PROXY, $proxy);
        //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);

        // Set TCP timeout to 30 seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

        // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
        // of the certificate as shown below. Ensure the file is readable by the webserver.
        // This is mandatory for some environments.

        //$cert = __DIR__ . "./cacert.pem";
        //curl_setopt($ch, CURLOPT_CAINFO, $cert);

        $res = curl_exec($ch);
        if (curl_errno($ch) != 0) // cURL error
        {
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
            exit;

        } else {
            // Log the entire HTTP response if debug is switched on.
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
                error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
        }

        // Inspect IPN validation result and act accordingly

        // Split response headers and payload, a better way for strcmp
        $tokens = explode("\r\n\r\n", trim($res));
        $res = trim(end($tokens));

        if (strcmp ($res, "VERIFIED") == 0) {
            // check whether the payment_status is Completed
            // check that txn_id has not been previously processed
            // check that receiver_email is your PayPal email
            // check that payment_amount/payment_currency are correct
            // process payment and mark item as paid.

            // assign posted variables to local variables
            //$item_name = $_POST['item_name'];
           // $item_number = $_POST['item_number'];
          //  $payment_status = $_POST['payment_status'];
           // $payment_amount = $_POST['mc_gross'];
            //$payment_currency = $_POST['mc_currency'];
            //$txn_id = $_POST['txn_id'];
            //$receiver_email = $_POST['receiver_email'];
            //$payer_email = $_POST['payer_email'];

           // if ($payment_status=='Completed'&&Common::total_fee_confirm($item_number, $payment_amount, '信息:贝宝交易,订单金额与实际支付不一致') )
           // {
          //      $method = Common::C('mobile');
           //     Common::pay_success($item_number, $method['method']['7']['name']);
           // }

            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
            }
            return true;
        } else if (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            // Add business logic here which deals with invalid IPN messages
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
            }
            return false;
        }
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