<?php defined('SYSPATH') or die('No direct script access.');


/**
 * 支付全局函数
 * Class product
 */
class St_Payment
{
    /**
     * 支付金额与订单金额是否相等
     * @param $ordersn
     * @param $payMoney
     * @param string $exception
     * @return bool
     */
   public static function total_fee_confirm($ordersn, $payMoney, $exception = '')
    {
        $bool = false;
        $info = Model_Member_Order::order_info($ordersn);
        if ($info['payprice'] == $payMoney)
        {
            $bool = true;
        }
        return $bool;
    }

    /**
     * 支付成功后，修改订单状态
     * @param $ordersn
     * @param string $payMethod
     * @param bool|false $is_offline 是否是线下支付
     */
    public static function pay_success($ordersn, $payMethod, $is_offline = false)
    {
        $bool = true;
        //线上支付
        if (!$is_offline)
        {
            //未支付，更新支付状态并赠送积分

            if (!self::is_order_payed($ordersn))
            {
                $info['sign'] = '11';
                self::change_order($ordersn, $payMethod);
                $detectresult = Model_Member_Order_listener::detect($ordersn);
                if ($detectresult !== true)
                {
                    $bool = false;
                }

            }
            else
            {
                $info['sign'] = '24';
            }
        }
        else
        {
            //线下支付
            $info['sign'] = '12';
            self::chang_order_by_offline($ordersn, $payMethod);
            $info['ordersn'] = $ordersn;
			self::pay_status($info);

        }
    }

    /**
     * 订单是否支付
     * @param $ordersn
     * @return bool
     */
    public static function is_order_payed($ordersn)
    {

        $rs = DB::select('id')->from('member_order')
            ->where('ordersn','=',$ordersn)
            ->and_where('status','=',2)
            ->execute()
            ->as_array();
        return empty($rs) ? false : true;
    }

    /**
     * 线上支付修改订单状态
     * @param $ordersn
     * @param string $payMethod
     */
    public static function change_order($ordersn, $payMethod)
    {
        $status = false;
        //更改处理中订单状态 status=1
        $rows = DB::update('member_order')
            ->where('ordersn', '=', "{$ordersn}")
            ->and_where('status', '=', 1)
            ->set(array('status' => 2, 'paysource' => $payMethod, 'paytime' => time()))
            ->execute();
        if ($rows == 1)
        {
            $order_info = DB::select()->from('member_order')->where('ordersn', '=', $ordersn)->execute()->current();
            Model_Member_Order::back_order_status_changed(1,$order_info,'');
          /*  St_Product::add_eticketno($ordersn);
            $order_info = DB::select()->from('member_order')->where('ordersn', '=', $ordersn)->execute()->current();
            Model_Member_Order_Log::add_log($order_info,1);
            // 添加电子票



            //短信通知
            St_SMSService::send_product_order_msg(NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG, $order_info);
            //邮件通知
            St_EmailService::send_product_order_email(NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG, $order_info);
            //送积分
            if (isset($order_info['jifenbook']) && $order_info['jifenbook'] > 0)
            {
                $rows = DB::update('member')
                    ->where('mid', '=', $order_info['memberid'])
                    ->set(array('jifen' => DB::expr("jifen + {$order_info['jifenbook']}")))
                    ->execute();
                //积分写入日志
                if ($rows == 1)
                {
                    DB::insert('member_jifen_log', array('memberid', 'content', 'jifen', 'type', 'addtime'))->values(array($order_info['memberid'], "预订{$order_info['productname']}获得积分{$order_info['jifenbook']}", $order_info['jifenbook'], 2, time()))->execute();
                }
            }*/
            $status = true;

        }
        return $status;

    }


      /**
         * 线下支付修改订单状态
         * @param $ordersn
         * @param $payMethod
         */
    public static function chang_order_by_offline($ordersn, $payMethod)
    {
        $prev_status =DB::select('status')->from('member_order')->where('ordersn', '=', $ordersn)->execute()->get('status');
        //更改订单状态
        $rows = DB::update('member_order')->where('ordersn', '=', "{$ordersn}")->set(array('paysource' => $payMethod))->execute();
        if ($rows != 1)
        {
            new Pay_Exception("订单({$ordersn})线下支付状态更新失败");
        }
        $order_info = DB::select()->from('member_order')->where('ordersn', '=', $ordersn)->execute()->current();
        Model_Member_Order_Log::add_log($order_info,$prev_status);
    }


    /**
     * 支付状态
     * @param $data
     */
    static function pay_status($data)
    {
        $data['sign'] = md5($data['sign']);
        $url = self::get_main_host() . '/payment/status';
        $html = "<form action='{$url}' style='display:none;' method='post' id='payment'>";
        foreach ($data as $name => $v)
        {
            $html .= "<input type='text' name='{$name}' value='{$v}'>";
        }
        $html .= '</form>';
        $html .= "<script>document.forms['payment'].submit();</script>";
        exit($html);
    }

    /**
     * 主站域名
     * @return string
     */
    static function get_main_host()
    {
        $host = '';
        $sql = "select weburl from sline_weblist where webid=0";
        $arr = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($arr))
        {
            $host = $arr['weburl'];
        }
        return $host;
    }

    /**
     * @function 写日志
     * @param $file
     * @param $msg
     */
    static function write_log($file,$msg)
    {
        if (!file_exists($file))
        {
            fopen($file, "w");
        }
        $time = date('Y-m-d H:i:s');
        $logFormat = <<<LOG
            #time:$time
            #message:$msg
LOG;
        file_put_contents($file, PHP_EOL.$logFormat.PHP_EOL, FILE_APPEND);

    }

    /**
     * 零元支付
     * @param $ordersn
     * @param string $payMethod
     */
    static function zero_pay($ordersn,$payMethod='积分抵现')
    {
        St_Payment::change_order($ordersn,$payMethod);
        $info['sign'] = '13';
        $info['ordersn'] = $ordersn;
        St_Payment::pay_status($info);
    }


}