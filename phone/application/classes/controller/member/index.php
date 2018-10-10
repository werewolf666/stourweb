<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 * 会员中心首页
 */
class Controller_Member_Index extends Stourweb_Controller
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
        $islogin = 0;
        if($member)
        {
            $has_msg = Model_Message::has_msg($member['mid']);
            $this->assign('has_msg',$has_msg);

            $member = Model_Member::get_member_byid($member['mid']);
            $fx = Model_Fenxiao::is_fenxiao();
            $fx_url =  URL::site()."fenxiao/";
            //$member['rank']=Common::member_rank($member['mid'],array('return'=>'current'));
            $member['number'] = $this->caculate_num($member['mid']);
            $temp=DB::select('jifen')->from('member')->where('mid','=',$member['mid'])->execute()->current();
            $member['jifen']=$temp['jifen'];
            $islogin = 1;
            $this->assign('fx',$fx);
            $this->assign('fx_url',$fx_url);
        }

        $this->assign('member',$member);
        $this->assign('islogin',$islogin);
        $this->display('member/index');
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

    /**
     * @function 统计数量
     * @param $mid
     */
    private function caculate_num($mid)
    {
        $out = array();
        //待付款
        $out['needpay'] =   count(Model_Member_Order::unpay($mid));
        //待消费
        $out['needconsume'] = DB::select('id')->from('member_order')->where('status','=',2)->and_where('memberid','=',$mid)->execute()->count();

        //待点评
        $out['needcomment'] = DB::select('id')->from('member_order')->where('status','=',5)->and_where('memberid','=',$mid)->and_where('ispinlun','=',0)->execute()->count();

        //优惠券数量
        if(St_Functions::is_normal_app_install('coupon'))
        {

            $out['coupon'] = DB::select('id')->from('member_coupon')->where('mid','=',$mid)->execute()->count();
        }

        //我的游记数量
        if(St_Functions::is_system_app_install(101))
        {
            $out['notes'] = DB::select('id')->from('notes')->where('memberid','=',$mid)->execute()->count();
        }

        //我的结伴数量
        if(St_Functions::is_system_app_install(11))
        {
            $out['jieban'] = DB::select('id')->from('jieban')->where('memberid','=',$mid)->execute()->count();
        }

        //我的咨询

        $out['question'] = DB::select('id')->from('question')->where('memberid','=',$mid)->execute()->count();

        return $out;


    }
}

