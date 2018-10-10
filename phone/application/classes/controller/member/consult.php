<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_consult
 * 我的咨询
 */
class Controller_Member_Consult extends Stourweb_Controller
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
        $this->display('member/consult');
    }
    public function action_ajax_more()
    {
        $questype = 0;
        $page = Common::remove_xss(intval($_GET['page']));
        $page = $page < 1 ? 1 : $page;
        $pagesize = 5;
        $out = Model_Question::question_list($this->member['mid'], $questype, $page, $pagesize);
        $list = $out['list'];
        foreach($list as &$row)
        {
            $row['addtime'] = date('Y-m-d H:i',$row['addtime']);
            $row['title'] = $row['content'];
            $row['product'] = $row['productname'];
            $row['answer'] = !empty($row['replycontent']) ? strip_tags($row['replycontent']) : '暂未回复';
            $row['replytime'] = !empty($row['replytime']) ? date('Y-m-d H:i',$row['replytime']) : '';
        }
        echo Product::list_search_format($list, $page,$pagesize);


    }
}