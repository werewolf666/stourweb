<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 * 我的积分
 */
class Controller_Member_Jifen extends Stourweb_Controller
{
    private  $_member = NULL;
    public function before()
    {
        parent::before();
        $this->_member = Common::session('member');

        $this->assign('member',$this->_member);
    }

    /**
     * 首页
     */
    public function action_index()
    {
        $member = Common::session('member');
        $this->assign('member',$member);
        $this->display('member/jifen/index');
    }

    public function action_ajax_log_more()
    {
        $page = intval($_GET['page']);
        $page = $page < 1 ? 1 : $page;
        $type = intval(Arr::get($_GET,'type'));
        $pagesize = 10;
        $log_arr = Model_Member_Jifen_Log::log_list($this->_member['mid'],$page,$pagesize,$type);
        $arr = $log_arr['list'];
        foreach($arr as &$ar)
        {
            $ar['addtime'] = date('Y-m-d',$ar['addtime']);
            $ar['point'] = $ar['type'] == 1 ? '-'.$ar['jifen'] : '+'.$ar['jifen'];
        }
        echo(Product::list_search_format($arr, $page));


    }




    /**
     * 检测是否登陆
     */
    public function check_login()
    {
        $this->member = Common::session('member');
        if (empty($this->member))
        {
            $this->request->redirect('member/login');
        }
    }
}

