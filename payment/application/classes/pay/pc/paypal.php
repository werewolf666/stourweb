<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PC 贝宝支付
 * Class Pay_Pc_HuiCao
 */
class Pay_Pc_PayPal
{

    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Pc_PayPal-notify_url/';
    //同步通知
    const ERTURN_URL = '/callback/index/Pay_Pc_PayPal-return_url/';

    /**
     * 支付数据提交
     * @param $data
     */
    public function submit($data)
    {

        $parameter = $this->_data_format($data);
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
        $returnUrl = Common::C('base_url') . self::ERTURN_URL;
        $parameter = array(
            'cmd' => '_xclick',// 网站拥有自己的购物车系统
            'business' => Common::C('cfg_paypal_key'),//商家的贝宝账号
            'item_name' => $data['ordersn'],//订单号
            'amount' => $data['total'],//商品总价
            'currency_code' => Common::C('cfg_paypal_currency'),//使用哪种货币 USD-美元
            'return' => $returnUrl,
            'invoice' => $data['ordersn'],
            'item_number'=>$data['ordersn'],
            'charset' => 'UTF-8',
            'no_shipping' => '1',
            'no_note' => '0',
            'image_url' => 'https://www.paypal.com/en_US/i/logo/paypal_logo.gif',
            'cancel_return' => $returnUrl,
            'notify_url' => Common::C('base_url') . self::NOTIFY_URL,
            'rm' => '2',
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
        $html = '<form action="https://www.paypal.com/cgi-bin/webscr"  method="post" name="E_FORM" accept-charset="utf-8">';
        foreach ($paras as $k => $v)
        {
            $html .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
        }
        $html .= '</form>';
        $html .= "<script>document.forms['E_FORM'].submit();</script>";
        return $html;
    }

    /**
     * 服务器异步通知页面路径
     */
    public function notify_url()
    {
        $bool = 'fail';
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $k => $v)
        {
            $v = urlencode(stripslashes($v));
            $req .= "&{$k}={$v}";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        $res = curl_exec($ch);
        curl_close($ch);
        if ($res)
        {
            if (strcmp($res, 'VERIFIED') == 0)
            {
                if ($_POST['payment_status'] != 'Completed' && $_POST['payment_status'] != 'Pending')
                {
                    exit;
                }
                $tip = '信息:贝宝(异)交易,订单金额与实际支付不一致';
                if (Common::total_fee_confirm($_POST['item_number'], $_POST['mc_gross'], $tip))
                {
                    $method = Common::C('pc');
                    Common::pay_success($_POST['item_number'], $method['method']['7']['name']);
                }
                $bool = 'success';
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
        header("Location:".rtrim(Common::get_main_host(),'/').'/member/');
        exit;
    }

}