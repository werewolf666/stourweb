<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Mynotes
 * 我的游记
 */
class Controller_Member_Mynotes extends Stourweb_Controller
{
    /**
     * 前置操作
     */
    public function before()
    {
        parent::before();
        $this->member = Common::session('member');
        if (empty($this->member))
        {
            Common::message(array('message' => __('unlogin'), 'jumpUrl' => $this->cmsurl . 'member/login'));
        }
    }

    public function action_index()
    {
        $pagesize=3;
        $sorttype=1;
        $list=Model_Notes::search(0,'',$sorttype,false,0,$pagesize,$this->member['mid']);
        $this->assign('list',$list);
        $this->assign('sorttype',$sorttype);
        $this->assign('pagesize',$pagesize);
        $this->display('member/mynotes');
    }
    public function action_ajax_get_more()
    {
        $pagesize = intval($_POST['pagesize']);
        $page = intval($_POST['page']);
        $page = empty($page)?1:$page;
        $sortype = $_POST['sorttype'];
        $offset = $pagesize*($page-1);
        $list=Model_Notes::search(null,null,$sortype,false,$offset,$pagesize,$this->member['mid']);
        foreach($list as &$row)
        {
            $memberInfo = Model_Member::get_member_byid($row['memberid']);
            $row['modtime'] = date('Y/m/d',$row['modtime']);
            $row['memberinfo'] = $memberInfo;
        }
        echo Product::list_search_format($list,$page,$pagesize);
    }
}