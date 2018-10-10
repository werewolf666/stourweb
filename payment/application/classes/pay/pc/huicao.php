<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PC 端汇潮支付
 * Class Pay_Pc_HuiCao
 */
class Pay_Pc_HuiCao
{

    //异步通知
    const NOTIFY_URL = '/callback/index/Pay_Pc_HuiCao-notify_url/';
    //同步通知
    const ERTURN_URL = '/callback/index/Pay_Pc_HuiCao-return_url/';

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
        $md5 = array(Common::C('cfg_huicao_account'), $data['ordersn'], $data['total'], $returnUrl, Common::C('cfg_huicao_key'));//(商户号、订单号、订单金额、同步通知、MD5私钥)
        $parameter = array(
            "MerNo" => Common::C('cfg_huicao_account'), // 商户号
            "BillNo" => $data['ordersn'], //订单编号
            "Amount" => $data['total'],//订单金额0.01
            "ReturnURL" => $returnUrl, //订单同步通知
            "AdviceURL" => Common::C('base_url') . self::NOTIFY_URL, //订单异步通知
            "SignInfo" => strtoupper(md5(http_build_query($md5))),//校验源字符串
            'orderTime' => date('YmdHis'),//交易时间YYYYMMDDHHMMSS
            "defaultBankNumber" => '', //银行编码
            "Remark" => $data['remark'],  //订单备注
            "products" => $data['productname'] //订单标题
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
        $html = '<form action="https://pay.ecpss.com/sslpayment"  method="post" name="E_FORM" accept-charset="utf-8">';
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
        $bool = 'failure';
        $checkArr = array($_POST["BillNo"], $_POST["Amount"], $_POST["Succeed"], Common::C('cfg_huicao_key'));
        $md5 = strtoupper(md5(http_build_query($checkArr)));
        if ($md5 == $_POST["SignMD5info"])
        {
            if ($_POST["Succeed"] == '88')
            {
                $tip = '信息:汇潮(异)交易,订单金额与实际支付不一致';
                if (Common::total_fee_confirm($_POST['BillNo'], $_POST['BillNo'], $tip))
                {
                    $method = Common::C('pc');
                    Common::pay_success($_POST['BillNo'], $method['method']['3']['name']);
                }
                $bool = 'ok';
            }
            else
            {
                new Pay_Exception("状态:{$_POST['Succeed']}");
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
        echo 123;
        $checkArr = array($_POST["BillNo"], $_POST["Amount"], $_POST["Succeed"], Common::C('cfg_huicao_key'));
        $md5 = strtoupper(md5(http_build_query($checkArr)));
        if ($md5 == $_POST["SignMD5info"])
        {
            if ($_POST["Succeed"] == '88')
            {
                $tip = '信息:汇潮(同)交易,订单金额与实际支付不一致';
                $info['sign'] = Common::total_fee_confirm($_POST['BillNo'], $_POST['Amount'], $tip) ? '11' : '23';
            }
            else
            {
                $info['sign'] = '00';
                new Pay_Exception("状态:{$_POST['Succeed']}");
            }
        }
        else
        {
            $info['sign'] = '22';
            new Pay_Exception("状态:汇潮交易数据有效性验证失败");
        }
        $info['ordersn'] = $_POST['BillNo'];
        Common::pay_status($info);
    }
}