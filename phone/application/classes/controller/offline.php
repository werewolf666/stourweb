<?php
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/09/25 16:31
 * Desc: 新版线下支付处理
 */

class  Controller_Offline extends Stourweb_Controller
{


    private $_mid = 0;

    function before()
    {
        parent::before();
        $member = Common::session('member');
        if($member)
        {
            $this->_mid = $member['mid'];
        }
    }

    public function action_mobile()
    {
        $ordersn = Common::remove_xss($_GET['ordersn']);
        $method = Common::remove_xss($_GET['method']);
        $info['ordersn'] = $ordersn;
        if($ordersn&&$method==6&&$this->_mid)
        {
             $status = DB::select('status')->from('member_order')
                 ->where('ordersn','=',$ordersn)
                 ->and_where('memberid','=',$this->_mid)
                 ->execute()->get('status');
             if($status==1)
             {
                 DB::update('member_order')->set(array('paytype'=>4))->where('ordersn','=',$ordersn)->execute();
                 $info['sign'] = 12;
             }
             else
             {
                 $info['sign'] = 01;

             }
        }
        else
        {
            $info['sign'] = 01;

        }
        St_Payment::pay_status($info);
    }

}