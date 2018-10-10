<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 会员俱乐部
 * Class Controller_Member_Club
 */
class Controller_Member_Club extends Stourweb_Controller
{
    //默认值
    private $_defaults = array();

    public function before()
    {
        parent::before();
        $user = Model_Member::check_login();
        if (!empty($user['mid']))
        {
            $this->mid = $user['mid'];
            $this->member = Model_Member::get_member_byid($this->mid);
            $this->assign('member', $this->member);
        }
        else
        {
            $referUrl = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $GLOBALS['cfg_cmsurl'];
            $this->assign('backurl', $referUrl);
            $this->request->redirect('member/login');
        }
    }

    //俱乐部头部
    public function action_header()
    {
        $integralActivity=array();
        if(St_Functions::is_normal_app_install('integral_award')){
              array_push($integralActivity,array('url'=>'award','title'=>'积分抽奖'));
        }
        $this->assign('integralActivity',$integralActivity);
        $this->display('member/club/header');
    }

    //俱乐部首页
    public function action_index()
    {
        $grade = Common::member_rank($this->mid, array('return' => 'all'));
        if ($grade['current'] == $grade['total'])
        {
            $nextGrade = null;
        }
        else
        {
            $nextArr = $grade['grade'][$grade['current']];
            $nextGrade = array('poor' => $nextArr['begin'] - $grade['jifen'], 'name' => $nextArr['name']);
        }
        $this->_defaults['selected'] = 'index';
        $this->assign('default', $this->_defaults);
        $this->assign('nextGrade', $nextGrade);
        $this->display('member/club/index');
    }

    //会员积分
    public function action_score()
    {
        $page = isset($_GET['p'])&& intval($_GET['p'])>0 ? intval($_GET['p']) : 1;
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $obj = DB::select()->from('member_jifen_log')->where('memberid','=',$this->mid);
        if ($type)
        {
            $obj->where('type', '=', $type);
        }
        $total = count($obj->execute()->as_array());
        $result = $obj->offset(10 * ($page-1))->limit(10)->order_by('addtime', 'desc')->execute()->as_array();
        foreach ($result as &$v)
        {
            $v['addtime'] = date('Y-m-d H:i', $v['addtime']);
            $v['typeMsg'] = $v['type'] == 1 ? '使用' : '获取';
        }
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action()
        );
        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/show',
                'total_items' => $total,
                'items_per_page' => 10,
                'first_page_in_url' => false,
            )
        );
        $pager->route_params($route_array);
        $this->_defaults['selected'] = 'score';
        $this->_defaults['type'] = $type;
        $this->_defaults['member'] = DB::select('jifen')->from('member')->where('mid', '=', $this->mid)->execute()->current();
        $this->assign('default', $this->_defaults);
        $this->assign('result', $result);
        $this->assign('page', $pager);
        $this->display('member/club/score');
    }

    //赚取积分
    public function action_makescore()
    {
        $newerTask = array(
            'sys_member_register' => 'mid',
            'sys_member_upload_head' => 'litpic',
            'sys_member_bind_phone' => 'mobile',
            'sys_member_bind_email' => 'email',
            'sys_member_bind_qq' => 'from_qq',
            'sys_member_bind_sina_weibo' => 'from_weibo',
            'sys_member_bind_weixin' => 'from_weixin'
        );
        $member = DB::select()->from('member')->where('mid', '=', $this->mid)->execute()->current();
        $member['from_qq'] = $member['from_weibo'] = $member['from_weixin'] = '';
        $member_third = DB::select()->from('member_third')->where('mid', '=', $this->mid)->execute()->as_array();
        foreach ($member_third as $v)
        {
            $member['from_' . $v['from']] = $v['openid'];
        }
        $result = DB::select()->from('jifen')->where('label', 'in ', DB::expr('("' . implode('","', array_keys($newerTask)) . '")'))->and_where('isopen', '=', 1)->execute()->as_array();
        foreach ($result as &$v)
        {
            $v['noFirst'] = $member[$newerTask[$v['label']]] ? 1 : 0;
        }
        $this->_defaults['selected'] = 'score';
        $this->_defaults['newerTask'] = $result;
        $this->assign('default', $this->_defaults);
        $this->display('member/club/makescore');
    }

    //会员等级
    public function action_rank()
    {
        //会员等级
        $grade = Common::member_rank($this->mid, array('return' => 'all'));
        $grade['process'] = 5;
        foreach ($grade['grade'] as $k => &$v)
        {
            if ($k < $grade['current'] - 1)
            {
                $grade['process'] += 127;
                $v['per'] = '100';
            }
            else
            {
                $v['per'] = '0';
            }
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
        $this->_defaults['selected'] = 'rank';
        $this->assign('default', $this->_defaults);
        $this->assign('grade', $grade);
        $this->display('member/club/rank');
    }
}