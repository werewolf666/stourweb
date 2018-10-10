<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 订单管理
 * Class Order
 */
Class Model_Member_Withdraw extends ORM
{
    public static $status_names=array('0'=>'申请中','1'=>'完成',2=>'未通过');

    public static function get_status_name($status)
    {
        return self::$status_names[$status];
    }
}