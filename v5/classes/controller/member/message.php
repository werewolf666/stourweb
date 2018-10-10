<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Order
 * 订单总控制器
 */
class Controller_Member_Message extends Stourweb_Controller
{


    private $mid = null;
    private $refer_url = null;

    public function before()
    {
        parent::before();
        $this->refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $GLOBALS['cfg_cmsurl'];
        $this->assign('backurl', $this->refer_url);
        $user = Model_Member::check_login();
        if (!empty($user['mid']))
        {
            $this->mid = $user['mid'];
        }
        else
        {
            $this->request->redirect('member/login');
        }
        $this->assign('mid', $this->mid);
    }

    //全部订单
    public function action_index()
    {

        $page = intval($_GET['p']);
        $page = $page<1?1:$page;
        $pagesize = 20;
        $offset = $pagesize*($page-1);
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
        );

        $w = 'where type<300 and memberid='.$this->mid;
        $sql = "select * from sline_message {$w} order by addtime desc limit {$offset},{$pagesize}";
        $sql_num = "select count(*) as num from sline_message {$w} ";

        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');

        foreach($list as &$v)
        {
            $v['url'] = $this->get_url($v);
        }
        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $num,
                'items_per_page' => $pagesize,
                'first_page_in_url' => false,
            )
        );
        foreach($list as &$v)
        {
            $v['is_commentable'] = Model_Model::is_commentable($v['typeid']);
            $v['is_standard_product'] =  Model_Model::is_standard_product($v['typeid']);
            if($v['typeid']==107){
                $v['producturl'] = St_Functions::get_web_url($v['webid']) . "/integral/show_{$v['productautoid']}.html";
            }
        }
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $list);
        $this->display('member/message/index');
    }

    //删除
    public function action_ajax_delete()
    {
        $id = $_POST['id'];
        $model = ORM::factory('message')->where('id','=',$id)->and_where('memberid','=',$this->mid)->find();
        if($model->loaded())
        {
            $model->delete();
        }
        echo json_encode(array('status'=>true));
    }

    //设置已读
    public function action_ajax_readed()
    {
        $id = intval($_POST['id']);
        DB::update('message')->set(array('status'=>1))->where('id','=',$id)->and_where('memberid','=',$this->mid)->execute();
        echo json_encode(array('status'=>true));
    }

    //获取PC端地址
    private function get_url($msg)
    {
        if($msg['type']<100)
        {
            $ordersn = DB::select('ordersn')->from('member_order')->where('id','=',$msg['orderid'])->execute()->get('ordersn');
            if(empty($ordersn))
            {
                return '';
            }
            return URL::site().'member/order/view?ordersn='.$ordersn;
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
            if(empty($info) || empty($info['id']))
            {
                return '';
            }
            return Common::get_web_url($info['webid']) . "/news/{$info['aid']}.html";
        }
        return '';
    }
}
