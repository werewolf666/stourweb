<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 发票管理
 * Class Bill
 */
class Model_Member_Order_Bill extends ORM
{
    /**
     * @function 添加发票信息
     * @param $orderId
     * @param $billInfo
     */
    public static function add_bill_info($orderId, $billInfo)
    {
        $m = ORM::factory('member_order_bill');
        $m->orderid = $orderId;
        foreach ($billInfo as $k => $v)
        {
            $m->$k = $v;
        }
        $m->save();
    }


}