<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Mobile 线下支付
 * Class Pay_Mobile_Xianxia
 */
class Pay_Mobile_Xianxia
{
    public function submit($data)
    {
        $method = Common::C('mobile');
        Common::pay_success($data['ordersn'], $method['method']['6']['name'], true);
    }
}