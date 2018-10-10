<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Order
 * 订单管理
 */
class Controller_Member_Message extends Stourweb_Controller
{
    /**
     * 订单管理前置操作
     */
    public function before()
    {

        parent::before();
        $this->member = Common::session('member');
        $order_query_token = Common::session('order_query_token');
        Common::session('order_query_token','');//重置查询
        if (empty($this->member) && empty($order_query_token))
        {
            Common::message(array('message' => __('unlogin'), 'jumpUrl' => $this->cmsurl . 'member/login'));
        }

    }
    /*
     * 订单查询页面
     * */
    public function action_index()
    {
        $this->display('member/message/index');
    }

    /*
     * 获取更多
     */
    public function action_ajax_more()
    {
        $page = intval($_POST['page']);
        $page = $page<1?1:$page;
        $pagesize = 10;
        $offset = $pagesize*($page-1);

        $w = 'where type<300 and memberid='.$this->member['mid'];
        $sql = "select * from sline_message {$w} order by addtime desc limit {$offset},{$pagesize}";
        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        foreach($list as &$row)
        {
            $row['url'] = $this->get_url($row);
            $row['addtime'] = date('Y-m-d H:i',$row['addtime']);
            if($row['type']>=200 && $row['type']<=299)
            {
                DB::update('message')->set(array('status'=>1))->where('id','=',$row['id'])->execute();
            }

        }
        echo json_encode(array('status'=>true,'list'=>$list));
    }

    public function action_ajax_readed()
    {
        $id = intval($_POST['id']);
        DB::update('message')->set(array('status'=>1))->where('id','=',$id)->and_where('memberid','=',$this->member['mid'])->execute();
        echo json_encode(array('status'=>true));
    }

    //获取PC端地址
    private function get_url($msg)
    {
        if($msg['type']<100)
        {
            return URL::site().'member/order/show?id='.$msg['orderid'];
        }
        else if($msg['type']<200)
        {
            $info = DB::select()->from('notes')->where('id','=',$msg['productid'])->execute()->current();
            if(empty($info) || empty($info['id']))
            {
                return '';
            }
            return Common::get_web_url(0) . "/notes/show_{$msg['productid']}.html";
        }
        else if($msg['type']<300)
        {
            $info = DB::select()->from('news')->where('id','=',$msg['productid'])->execute()->current();
            return '';//资讯暂时无手机端
            if(empty($info) || empty($info['id']))
            {
                return '';
            }
            return Common::get_web_url($info['webid']) . "/news/{$info['aid']}.html";
        }
        return '';
    }
}