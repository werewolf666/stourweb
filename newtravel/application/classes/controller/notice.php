<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Notice extends Stourweb_Controller{

    public function before()
    {
        parent::before();
        $this->assign('cmsurl', URL::site());

    }


    public function action_ajax_checkorder()
    {
        $order_log_file = BASEPATH.'/data/order.php';
        $org_id = '';
        if(file_exists($order_log_file))
        {
            $org_id = file_get_contents($order_log_file);
        }
        $new_order = ORM::factory('member_order')->order_by('addtime','DESC')->limit(1)->find()->as_array();
        if($new_order['id'] != $org_id)
        {
            file_put_contents($order_log_file,$new_order['id']);
            echo json_encode(array('status'=>1,'order_info'=>$new_order));
        }
        else
        {
           echo json_encode(array('status'=>0));
        }






    }


}