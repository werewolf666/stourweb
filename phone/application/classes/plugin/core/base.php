<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:01
 */
class Plugin_Core_Base{
    //订单状态改变通知,params为订单信息，即订单表的一行
    public function on_orderstatus_changed($params){}

    //会员注册通知，params为会员信息
    public function on_member_register($params){}


}