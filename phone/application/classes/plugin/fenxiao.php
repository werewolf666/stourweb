<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 13:59
 */
class Plugin_Fenxiao extends Plugin_Core_Base{
    public function on_member_register($params){
        try {
            $fxcode = Common::session('fxcode');
            if (empty($fxcode))
                return;
            $fenxiao = ORM::factory('fenxiao')->where('memberid', '=', $params['mid'])->find();
            if ($fenxiao->loaded())
                return;
            Model_Fenxiao::invite($params['mid'], $fxcode);
            Common::session('fxcode', null);
        }catch (Exception $excep)
        {

        }
    }
}