<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PC 线下支付
 * Class Pay_Pc_Xianxia
 */
class Pay_Pc_Xianxia
{
    public function submit($data)
    {
        $method = Common::C('pc');
        Common::pay_success($data['ordersn'], $method['method']['6']['name'],true);
    }

}