<?php

/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/09/28 9:50
 * Desc: 在线支付退款类
 */
class Pay_Online_Refund
{




    /**
     * @function 退款开始
     * @param $ordersn 订单号
     * @param $model 模型
     * @param $offline 线下退款
     */
    public static function refund_start($ordersn,$model,$offline=false)
    {
        //判断订单是否已经退款
        if(self::_check_is_refund($ordersn))
        {
            return true;
        }
        $order = Model_Member_Order::order_info($ordersn);
        if(!$order['payprice'])
        {
            return true;
        }
        //判断是否为在线退款
        $online_transaction_no = json_decode($order['online_transaction_no'],true);
        if(empty($online_transaction_no)||$offline)
        {
            self::_refund_by_admin($order);
            return true;
        }
        $result = 2;
        switch ($online_transaction_no['source'])
        {
            case 'wxpay':
                $result = self::_wxpay_refund($order,$online_transaction_no['transaction_no']);
                break;
            case 'alipay':
                $result =  self::_ailpay_refund($order,$online_transaction_no['transaction_no']);
                break;
        }
        if($result == 1)
        {
            //退款成功,更改订单状态
            $rsn = DB::update('member_order')
                ->set(array('status'=>4))
                ->where('ordersn','=',$ordersn)
                ->execute();
            if($rsn)
            {
                $org_status = $order['status'];
                $order['status'] = 4;
                Model_Member_Order::back_order_status_changed($org_status,$order,$model);
            }
        }
        DB::update('member_order')
            ->set(array('online_refund_status'=>$result))
            ->where('ordersn','=',$ordersn)
            ->execute();
        return $result;
    }


    /**
     * @function 线下转账退款
     * @param $order
     */
    private function _refund_by_admin($order)
    {
        self::_update_refunf_log($order,1,'','管理员已同意退款,等待退款转出','bank');//更新退款信息

    }



    /**
     * @function 支付宝退款
     * @param $order 订单
     * @param $transaction_no 交易流水号
     */
    private  function _ailpay_refund($order,$transaction_no)
    {

        require_once TOOLS_PATH.'lib/alipay_refund/AlipayTradeService.php';
        require_once TOOLS_PATH.'lib/alipay_refund/AopClient.php';
        require_once TOOLS_PATH.'lib/alipay_refund/SignData.php';
        require_once TOOLS_PATH.'lib/alipay_refund/AlipayTradeRefundRequest.php';
        require_once TOOLS_PATH.'lib/alipay_refund/AlipayTradeRefundContentBuilder.php';
        $alipay_config =  self::_alipay_config();
        $batch_no = self::_get_refund_num($order,'alipay');//退款批次号
        $RequestBuilder=new AlipayTradeRefundContentBuilder();
        $RequestBuilder->setOutTradeNo($order['ordersn']);//订单号
        $RequestBuilder->setTradeNo($transaction_no);//支付宝流水号
        $RequestBuilder->setRefundAmount($order['payprice']);//退款金额
        $RequestBuilder->setOutRequestNo($batch_no);//退款标识
        $RequestBuilder->setRefundReason('协商退款');
        $aop = new AlipayTradeService($alipay_config);
        $response = $aop->Refund($RequestBuilder);
        $status = 2;
        if(($response->code=='10000') && ($response->msg=='Success'))
        {
            $reason = '已退款到支付宝支付账号'.$response->buyer_logon_id;
            $status = 1;
        }
        else
        {
            $reason = $response->msg;
        }
        self::_update_refunf_log($order,$status,$batch_no,$reason,'alipay');//更新退款信息
        return $status;


    }




    /**
     * @function 微信退款
     * @param $order 订单
     * @param $transaction_no 交易流水号
     */
    private  function _wxpay_refund($order,$transaction_no)
    {
        if(empty($transaction_no))
        {
            return false;
        }
        self::_set_wxpay_config();//配置微信支付账号等
        $price = $order['payprice']*100;
        $out_refund_no = self::_get_refund_num($order,'wxpay');
        $input = new WxPayRefund();
        //$input->SetOut_trade_no($order['ordersn']);         //自己的订单号
        $input->SetTransaction_id($transaction_no);     //微信官方生成的订单流水号，在支付成功中有返回
        $input->SetOut_refund_no($out_refund_no);         //退款单号
        $input->SetTotal_fee($price);         //订单标价金额，单位为分
        $input->SetRefund_fee($price);         //退款总金额，订单总金额，单位为分，只能为整数
        $input->SetOp_user_id(MCHID);
        $result = WxPayApi::refund($input); //退款操作
        $status = 2;
        $reason = '微信退款未知错误,请联系管理员';
        if(($result['return_code']=='SUCCESS') && ($result['result_code']=='SUCCESS'||$result['err_code_des']=='订单已全额退款'))
        {
            $reason = '已退款到微信支付原账号';
            $status = 1;
        }
        else if
        (($result['return_code']=='FAIL') || ($result['result_code']=='FAIL'))
        {
            $reason = (empty($result['err_code_des'])?$result['return_msg']:$result['err_code_des']);
        }
        self::_update_refunf_log($order,$status,$out_refund_no,$reason,'wxpay');//更新退款信息
        return $status;
    }


    /**
     * @function  判断订单是否已经退款
     * @param $ordersn
     */
    private static function _check_is_refund($ordersn)
    {
        return DB::select('id')
            ->from('member_order_refund')
            ->where('ordersn','=',$ordersn)
            ->where('status','=',1)
            ->execute()->get('id');
    }


    /**
     * @function 获取退款单号
     * @param $order
     * @param $type
     */
    private static function _get_refund_num($order,$type)
    {
        $refund_no = DB::select('refund_no')
            ->from('member_order_refund')
            ->where('ordersn','=',$order['ordersn'])
            ->execute()->get('refund_no');
        if($refund_no)
        {
            return $refund_no;
        }
        $func = '_get_'.$type.'_refund_num';
        return call_user_func(array('Pay_Online_Refund',$func),$order['ordersn']);

    }


    /**
     * @function 微信生成退款单号
     * @param $ordersn
     * @return string
     */
    private static function _get_wxpay_refund_num($ordersn)
    {
        $order_length = strlen($ordersn);
        $out_length = 64-$order_length;
        $chars = md5($ordersn).md5(strrev($ordersn));
        for ( $i = 0; $i < $out_length; $i++ )
        {
            $ordersn .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $ordersn;

    }


    /**
     * @function 支付宝生成退款单号
     * @param $ordersn
     * @return mixed
     */
    private static function _get_alipay_refund_num($ordersn)
    {
        $batch_no = date('Ymd');
        return $batch_no.$ordersn;
    }


    /**
     * @function 微信退款初始化
     */
    private static function _set_wxpay_config()
    {

        //绑定支付的APPID
        define('APPID', self::_get_config_val('cfg_wxpay_appid'));
        //商户号
        define('MCHID',self::_get_config_val('cfg_wxpay_mchid'));
        //商户支付密钥
        define('KEY', self::_get_config_val('cfg_wxpay_key'));
        //公众帐号secert
        define('APPSECRET', self::_get_config_val('cfg_wxpay_appsecret'));


        $ext_dir = self::_get_ext_dir('wxpay');//微信文件所在路径

        define('SSLCERT_PATH', $ext_dir . '/vendor/pc/wxpay/cert/apiclient_cert.pem');
        define('SSLKEY_PATH', $ext_dir . '/vendor/pc/wxpay/cert/apiclient_key.pem');
        $file_dir = $ext_dir."/vendor/pc/wxpay/lib/WxPay.Api.php";
        require_once $file_dir;
    }


    /**
     * @function 获取支付应用的路径
     * @param $source
     */
    private static function _get_ext_dir($source)
    {
        //证书路径,注意应该填写绝对路径
        $dir = rtrim(BASEPATH,DIRECTORY_SEPARATOR);
        $issystem = DB::select('issystem')
            ->from('payset')
            ->where('pinyin','=',$source)
            ->execute()->get('issystem');
        if($issystem!=1)
        {
            $dir .= '/plugins/'.$source;
        }
        else
        {
            $dir .= '/payment/application';
        }
        return $dir;
    }





    /**
     * @function 更新退款记录
     * @param $order 订单
     * @param $status 退款状态
     * @param $refund_no 退款流水号
     * @param $description 说明
     * @param $platform 退款平台
     */
    private static function _update_refunf_log($order,$status,$refund_no,$description,$platform)
    {
        $old_log = DB::select()
            ->from('member_order_refund')
            ->where('ordersn','=',$order['ordersn'])
            ->execute()->current();
        $log = array(
            'status'=>$status,
            'refund_no'=>$refund_no,
            'description'=>$description,
            'modtime'=>time(),
        );
        if($old_log)
        {
            $log['addtime'] = $old_log['addtime'];
            $log['ordersn'] = $old_log['ordersn'];
            $log['platform'] = $old_log['platform'];
            $log['refund_fee'] = $old_log['refund_fee'];
            $log['memberid'] = $old_log['memberid'];
            DB::delete('member_order_refund')->where('id','=',$old_log['id'])->execute();
        }
        else
        {
            $log['addtime'] = time();
            $log['ordersn'] = $order['ordersn'];
            $log['platform'] = $platform;
            $log['refund_fee'] = $order['payprice'];
            $log['memberid'] = $order['memberid'];
        }
        self::add_refund_log($log);

    }


    /**
     * @function 添加退款日志
     * @param $log
     */
    public static function  add_refund_log($log)
    {
        return DB::insert('member_order_refund',array_keys($log))
            ->values(array_values($log))
            ->execute();
    }


    /**
     * @function 整合支付宝配置
     * @return mixed
     */
    private static function _alipay_config()
    {
        $ext_dir = self::_get_ext_dir('alipay');
        $merchant_private_key = $ext_dir .'/vendor/pc/alipay_cash/rsa_private_key.pem';
        $alipay_public_key_path = $ext_dir .'/vendor/pc/alipay_cash/rsa_public_key.pem';

        //合作身份者id
        $alipay_config['app_id'] = self::_get_config_val('cfg_alipay_appid');
        //商户私钥
        $alipay_config['merchant_private_key'] = $merchant_private_key;
        //字符编码格式
        $alipay_config['charset'] = strtolower('utf-8');
        //签名方式
        $alipay_config['sign_type'] = strtoupper('RSA2');
        //签名
        $alipay_config['sign'] = strtoupper('MD5');
        //支付宝网关
        $alipay_config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';
        //支付宝公钥
        $alipay_config['alipay_public_key'] = $alipay_public_key_path;

        return $alipay_config;
    }



    /**
     * @function 获取参数的值
     */
    private static function _get_config_val($field)
    {

        return DB::select('value')
            ->from('sysconfig')
            ->where('varname','=',$field)
            ->execute()->get('value');
    }


    /**
     * @function 前台用户申请退款
     * @param $data
     * @param $memberid
     */
    public static function apply_order_refund($data,$memberid,$model)
    {
        $refund_reason = trim(Common::remove_xss($data['refund_reason']));
        $platform =  trim(Common::remove_xss($data['platform']));
        $ordersn =  trim(Common::remove_xss($data['ordersn']));
        $alipay_account ='';
        $cardholder ='';
        $cardnum ='';
        $bank ='';
        if(empty($ordersn))
        {
            return  array('status'=>0,'message'=>'订单号错误');
        }
        $info = Model_Member_Order::order_info($ordersn,$memberid);
        if(empty($info)||$info['status']!=2)
        {
            return  array('status'=>0,'message'=>'订单号错误');
        }
        if(strlen($refund_reason)<5)
        {
            return  array('status'=>0,'message'=>'退款原因不能少于5个字');
        }
        $online_transaction_no = json_decode($info['online_transaction_no'],true);
        //退款到指定账户
        if(empty($online_transaction_no))
        {
            if($platform == 'alipay')
            {
                $alipay_account =  trim(Common::remove_xss($data['alipay_account']));
                $platform = 'other_alipay';
                if(!$alipay_account)
                {
                    return  array('status'=>0,'message'=>'请输入正确的支付宝账号');
                }
            }
            elseif ($platform == 'bank')
            {
                $cardholder =  trim(Common::remove_xss($data['cardholder']));
                $cardnum =  trim(Common::remove_xss($data['cardnum']));
                $bank =  trim(Common::remove_xss($data['bank']));
                if(empty($cardholder)||empty($cardnum)||empty($bank))
                {
                    return  array('status'=>0,'message'=>'请输入正确的银行卡信息');
                }
            }
        }
        //原路退还
        else
        {
            $platform = $online_transaction_no['source'];
        }
        $log = array(
            'ordersn'=>$ordersn,
            'platform'=>$platform,
            'refund_fee'=>$info['payprice'],
            'status'=>0,
            'addtime'=>time(),
            'memberid'=>$memberid,
            'alipay_account'=>$alipay_account,
            'cardholder'=>$cardholder,
            'cardnum'=>$cardnum,
            'bank'=>$bank,
        );
        DB::delete('member_order_refund')->where('ordersn','=',$ordersn)->execute();
        if(self::add_refund_log($log))
        {
            $update = array(
                'status'=>6,
                'refund_reason'=>$refund_reason,
            );
            DB::update('member_order')->set($update)->where('ordersn','=',$ordersn)->execute();
            $order = Model_Member_Order::order_info($ordersn);
            Model_Member_Order::back_order_status_changed($info['status'],$order,$model);
            return  array('status'=>1,'message'=>'退款请求提交成功，等待商家确认!');
        }
        else
        {
            return  array('status'=>0,'message'=>'未知错误');
        }

    }


    /**
     * @function 撤销订单退款
     * @param $ordersn
     * @param $memberid
     */
    public static function order_refund_back($ordersn,$memberid,$model)
    {


        $order = DB::select()->from('member_order')->where('memberid', '=', $memberid)->and_where('ordersn', '=', $ordersn)->execute()->current();

        if (!$order)
        {
            exit;
        }
        $result = array('bool' => true, 'message' => __('不同意您的申请，详情请联系商家'));
        if ($order['status'] == 6)
        {
            $row = DB::update('member_order')->set(array('status' => 2))->where('id', '=', $order['id'])->execute();
            if ($row)
            {
                $order['status'] = 2;
                self::_refund_back_log($ordersn);
                Model_Member_Order::back_order_status_changed(6,$order,$model);

                $result = array('bool' => true, 'message' => __('商家同意您的申请'));
            }
        }
        else if ($order['status'] == 4)
        {
            $result = array('bool' => false, 'message' => __('商家已退款，您的申请未通过'));
        }
        return $result;

    }


    /**
     * @function 撤销退款
     * @param $ordersn
     */
    private static function _refund_back_log($ordersn)
    {
        $update = array(
            'status'=>2,
            'description'=>'用户撤销退款申请',
        );
        DB::update('member_order_refund')->set($update)->where('ordersn','=',$ordersn)->execute();

    }


    /**
     * @function 增加 订单退款信息
     * @param $info
     */
    public static function get_refund_info($info)
    {
        if($info['status']==4||$info['status']==6)
        {
            $info['refund'] = DB::select()
                ->from('member_order_refund')
                ->where('ordersn','=',$info['ordersn'])
                ->order_by('addtime','desc')
                ->limit(1)
                ->execute()->current();
            switch ($info['refund']['platform'])
            {
                case 'alipay':
                    $info['refund']['platform'] = '支付宝';
                    break;
                case 'other_alipay':
                    $info['refund']['platform'] = '支付宝';
                    break;
                case 'wxpay':
                    $info['refund']['platform'] = '微信';
                    break;
                case 'bank':
                    $info['refund']['platform'] = '银行卡';
                    break;
            }

            $info['refund']['refund_reason'] = $info['refund_reason'];
            unset($info['refund_reason']);
        }
        return $info;

    }


    /**
     * @function 拒绝退款
     * @param $ordersn 订单号
     * @param $model 模型
     * @param $description 拒绝理由
     */
    public static function admin_refund_back($ordersn,$model,$description)
    {
        $order = Model_Member_Order::order_info($ordersn);
        $rsn = DB::update('member_order')
            ->set(array('status'=>2))
            ->where('ordersn','=',$ordersn)
            ->execute();
        if($rsn)
        {
            $org_status = $order['status'];
            $order['status'] = 2;
            Model_Member_Order::back_order_status_changed($org_status,$order,$model);
            self::_update_last_log($org_status,$order,$description);
        }
        return true;
    }


    /**
     * @function 更新最后一个日志记录
     * @param $org_status
     * @param $order
     * @param $description
     */
    private static function _update_last_log($org_status,$order,$description)
    {
        $description  = '商家拒绝了您的退款申请，原因:'.$description;
        $lastid = DB::select('id')
            ->from('member_order_log')
            ->where('orderid','=',$order['id'])
            ->and_where('prev_status','=',$org_status)
            ->and_where('current_status','=',$order['status'])
            ->order_by('addtime','desc')
            ->limit(1)
            ->execute()
            ->get('id');
        DB::update('member_order_log')
            ->set(array('description'=>$description))
            ->where('id','=',$lastid)
            ->execute();
    }



}