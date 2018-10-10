<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 会员俱乐部
 * Class Controller_Member_Club
 */
class Controller_Member_Club extends Stourweb_Controller
{
    private $_member = NULL;

    public function before()
    {
        parent::before();
        $this->_member = Common::session('member');
        if (empty($this->_member))
        {
            Common::message(array('message' => __('unlogin'), 'jumpUrl' => $this->cmsurl . 'member/login'));
        }
        $this->assign('member', $this->_member);
    }

    //首页
    public function action_index()
    {
        $member = Model_Member::get_member_byid($this->_member['mid']);
        $rank = Model_Member::member_rank($this->_member['mid'], array('return' => 'all'));
        //会员任务
        $label = array(
            'sys_member_register' => 8,
            'sys_member_login' => 9,
            'sys_member_upload_head' => 10,
            'sys_member_bind_phone' => 11,
            'sys_member_bind_email' => 12,
            'sys_member_bind_qq' => 13,
            'sys_member_bind_sina_weibo' => 14,
            'sys_member_bind_weixin' => 15
        );
        $strategy = DB::select()->from('jifen')->where(DB::expr('label in ("' . implode('","', array_keys($label)) . '")'))->and_where('isopen', '=', 1)->order_by('id', 'asc')->limit(3)->execute()->as_array();
        $this->assign('member', $member);
        $this->assign('rank', $rank);
        $this->assign('strategy', $strategy);
        $this->assign('label', $label);
        $this->display('member/club/index');
    }

    //会员等级
    public function action_member_rank()
    {
        //会员等级
        $member = Model_Member::get_member_byid($this->_member['mid']);
        $grade = Model_Member::member_rank($this->_member['mid'], array('return' => 'all'));
        foreach ($grade['grade'] as $k => &$v)
        {
            $v['rank'] = $k + 1;
        }
        if ($grade['current'] == $grade['total'])
        {
            $grade['nextGrade'] = null;
        }
        else
        {
            $nextArr = $grade['grade'][$grade['current']];
            $grade['nextGrade'] = array('poor' => $nextArr['begin'] - $grade['jifen'], 'name' => $nextArr['name']);
        }
        if ($grade['current'] - 2 < 0)
        {
            $progress = array_slice($grade['grade'], 0, 2);
            array_unshift($progress, null);
        }
        else
        {
            $progress = array_slice($grade['grade'], $grade['current'] - 2, 3);
        }
        $this->assign('grade', $grade);
        $this->assign('progress', $progress);
        $this->assign('member', $member);
        $this->display('member/club/member_rank');
    }

    //会员任务
    public function action_member_task()
    {
        $member = Model_Member::get_member_byid($this->_member['mid']);
        //会员任务
        $newer = array(
            'sys_member_register' => array('icon' => 8, 'complete' => true),
            'sys_member_upload_head' => array('icon' => 10, 'complete' => $member['litpic'] && $member['litpic'] != Model_Member::member_nopic() ? true : false, 'bind' => 'editData'),
            'sys_member_bind_phone' => array('icon' => 11, 'complete' => $member['mobile'] ? true : false, 'bind' => 'bindPhone'),
            'sys_member_bind_email' => array('icon' => 12, 'complete' => $member['email'] ? true : false, 'bind' => 'bindMailbox'),
          //  'sys_member_bind_qq' => array('icon' => 13),
           // 'sys_member_bind_sina_weibo' => array('icon' => 14),
           // 'sys_member_bind_weixin' => array('icon' => 15)
        );
        $daily = array('sys_member_login', 'sys_write_notes', 'sys_write_jieban', 'sys_write_wenda');
        $strategy = DB::select()->from('jifen')->where(DB::expr('label in ("' . implode('","', array_keys($newer)) . '")'))->and_where('isopen', '=', 1)->order_by('id', 'asc')->execute()->as_array();
        $daily_strategy = DB::select()->from('jifen')->where(DB::expr('label in("' . implode('","', $daily) . '")'))->and_where('isopen', '=', 1)->order_by('id', 'asc')->execute()->as_array();
        $temp = array();
        foreach ($daily_strategy as $v)
        {
            $temp[$v['label']] = $v;
        }
        $this->assign('member', $member);
        $this->assign('strategy', $strategy);
        $this->assign('daily_strategy', $temp);
        $this->assign('newer', $newer);
        $this->display('member/club/member_task');
    }


    //积分记录
    public function action_score()
    {
        $log = DB::select()->from('member_jifen_log')->where('memberid', '=', $this->_member['mid'])->order_by('addtime', 'desc')->limit(6)->execute()->as_array();
        $member = Model_Member::get_member_byid($this->_member['mid']);
        $this->assign('member', $member);
        $this->assign('log', $log);
        $this->display('member/club/score');
    }

    //积分详情
    public function action_score_detail()
    {
        $member = Model_Member::get_member_byid($this->_member['mid']);
        $this->assign('member', $member);
        $this->display('member/club/score_detail');
    }

    public function action_ajax_jifen_logs()
    {
        $page = intval($_GET['current']);
        $type = intval($_GET['type']);
        $obj = DB::select()->from('member_jifen_log')->where('memberid', '=', $this->_member['mid']);
        if ($type > 0)
        {
            $obj->where('type', '=', $type);
        }

        $list = $obj->limit(30)->offset(($page - 1) * 30)->order_by('addtime', 'desc')->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['addtime'] = date('Y-m-d H:i', $v['addtime']);
        }
        echo json_encode(array('status' => $list ? true : false, 'list' => $list, 'page' => $list ? ++$page : $page));
    }
}