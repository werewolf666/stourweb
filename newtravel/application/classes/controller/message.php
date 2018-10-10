<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Message extends Stourweb_Controller{

    /*
     * 评论总控制器
     * @author:netman
     * @data:2014-07-22
     * */
    public function before()
    {
        parent::before();
    }
    //订单消息配置
    public function action_order()
    {
        $typeid = $this->params['typeid'];

        DB::select()->from('message_config')->where('typeid','=',$typeid);
        $status_arr = array(
            array('name'=>'订单未处理','status'=>'0','content'=>'','isopen'=>0),
            array('name'=>'订单处理中','status'=>'1','content'=>'','isopen'=>0),
            array('name'=>'订单付款成功','status'=>'2','content'=>'','isopen'=>0),
            array('name'=>'订单取消','status'=>'3','content'=>'','isopen'=>0)
        );
        foreach($status_arr as &$row)
        {
            $msg_cfg = DB::select()->from('message_config')->where('typeid','=',$typeid)->and_where('type','=',$row['status'])->execute()->current();
            if(!empty($msg_cfg))
            {
                $row['isopen'] = $msg_cfg['isopen'];
                $row['content'] =  $msg_cfg['content'];
            }
        }

        $this->assign('typeid',$typeid);
        $this->assign('status_arr',$status_arr);
        $this->display('stourtravel/message/order');
    }
    //保存
    public function action_ajax_save_order()
    {
        $content = $_POST['content'];
        $isopen = $_POST['isopen'];
        $typeid = $_POST['typeid'];

        foreach($content as $k=>$v)
        {
            $msg_model = ORM::factory('message_config')->where('type','=',$k)->and_where('typeid','=',$typeid)->find();
            $msg_model->typeid = $typeid;
            $msg_model->type = $k;
            $msg_model->isopen = $isopen[$k];
            $msg_model->content = $v;
            $msg_model->save();
        }
        echo json_encode(array('status'=>true));
    }
}