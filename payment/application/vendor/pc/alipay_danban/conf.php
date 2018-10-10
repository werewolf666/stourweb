<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 支付宝担保
 */
return array(
    'id'=>13,//编号
    'order' => 3,//排序
    'en' => 'AlipayDanbao',
    'name' => '支付宝担保交易(电脑端)',
    'img' => '/payment/public/images/st-payment01.gif',
    'ext' => '<i></i>担保交易：<br />买家先将交易资金存入支付宝并通知卖<br />家发货，买家确认收货后资金自动进入<br />卖家支付宝账户，完成交易。'
);