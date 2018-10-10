<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Login
 * 会员登陆
 */
class Controller_Member_Login extends Stourweb_Controller
{
    private $referer;

    public function before()
    {
        parent::before();
        $this->loginUrl = $this->cmsurl . 'member/login/index/' . md5(time() . rand(1000, 9999));
    }

    //登录首页
    public function action_index()
    {
        if (isset($_GET['redirecturl']) && !empty($_GET['redirecturl']))
        {
            $this->referer = $_GET['redirecturl'];
        }
        else
        {
            $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;
        }
        if (!stripos($this->referer, '/message/') && !stripos($this->referer, '/member/'))
        {
            Cookie::set('referer', $this->referer);
        }
        $bool = Common::session('login_num') ? false : true;
        Cookie::set('_version', 'mobile_5.0');
        $this->assign('one', $bool);
        $this->assign('url', Cookie::get('referer', $this->cmsurl));
        $this->display('member/login','member_login');
    }

    //登录检测
    public function action_ajax_check()
    {
        $user = Arr::get($_POST, 'user');
        $pwd = Arr::get($_POST, 'pwd');
        $code = Arr::get($_POST, 'code');
        //验证码检测
        if (Common::session('login_num') && !Captcha::valid($code))
        {
            Common::session('login_num', 1);
            $message = array('msg' => __("error_code"), 'status' => 0);
            exit(json_encode($message));
        }
        Common::session('captcha_response', null);
        //数据验证
        $userType = strpos($_POST['user'], '@') ? 'email' : 'phone';
        $validataion = Validation::factory($_POST);
        $validataion->rule('pwd', 'not_empty');
        $validataion->rule('pwd', 'min_length', array(':value', '6'));
        $validataion->rule('user', 'not_empty');
        $validataion->rule('user', $userType);
        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($validataion->errors());
            $message = array('msg' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), 'status' => 0);
            exit(json_encode($message));
        }
        $member = Model_Member::member_find($user, $pwd);
        if (empty($member))
        {
            Common::session('login_num', 1);
            $message = array('msg' => __("error_login"), 'status' => 0);
            exit(json_encode($message));
        }
        else
        {
            Model_Member::write_session($member, $user);
            //清空登录次数
            Common::session('login_num', null);
            //删除Cookie
            $message = array('url' => Cookie::get('referer', $this->cmsurl), 'status' => 1);
            #api{{
            $ucsynlogin = '';
            if (defined('UC_API') && include_once BASEPATH . '/uc_client/client.php')
            {
                //检查帐号
                list($uid, $loginname, $password, $email) = uc_user_login($user, $pwd);

                if ($uid > 0)
                {

                    //同步登录的代码
                    $ucsynlogin = uc_user_synlogin($uid);
                }
                else if ($uid == -1)
                {
                    $uid = uc_user_register($loginname, md5($password), '');
                    if ($uid > 0)
                    {
                        $ucsynlogin = uc_user_synlogin($uid);
                    }
                }
            }
            #/aip}}
            $message['js'] = $ucsynlogin;
            exit(json_encode($message));
        }
    }

    /**
     * ajax判断是否登陆
     */
    public function action_ajax_is_login()
    {
        //检测用户是否存在
        $userinfo = Common::session('member');
        if (!isset($userinfo['mid']))
        {
            exit(json_encode(array('status' => 0)));
        }
        else
        {
            exit(json_encode(array('status' => 1)));
        }
    }
    //第三方登录
    public function action_third()
    {
        $forwardurl = Arr::get($_GET, 'forwardurl');
        //QQ
        $cfg_qq_appid = $GLOBALS['cfg_qq_appid'];
        $cfg_qq_appkey = $GLOBALS['cfg_qq_appkey'];
        //sina
        $cfg_sina_appkey = $GLOBALS['cfg_sina_appsecret'];
        $cfg_sina_appsecret = $GLOBALS['cfg_sina_appsecret'];
        //weixin
        $cfg_weixi_appkey = $GLOBALS['cfg_weixi_appkey'];
        $cfg_weixi_appsecret = $GLOBALS['cfg_weixi_appsecret'];
        //
        $qqlogin = $cfg_qq_appid && $cfg_qq_appkey ? 1 : 0;
        $sinalogin = $cfg_sina_appkey && $cfg_sina_appsecret ? 1 : 0;
        $weixinlogin = $cfg_weixi_appkey && $cfg_weixi_appsecret ? 1 : 0;
        if (!empty($GLOBALS['cfg_qq_appid']) && !empty($GLOBALS['cfg_qq_appkey']))
        {
            $this->assign('forwardurl', $forwardurl);
        }
        $this->assign('qqlogin', $qqlogin);
        $this->assign('sinalogin', $sinalogin);
        $this->assign('weixinlogin', $weixinlogin);
        $this->display('member/third');
    }
    //注销登录
    public function action_ajax_out()
    {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;
        Common::session('member', NULL);
        Cookie::set('st_userid',null,-1000);
        Cookie::set('st_username',null,-1000);
        $this->request->redirect($referer);
        //header("Location:{$referer}");
    }

    //ajax获取登陆信息
    public function action_ajax_islogin()
    {
        $bool = 0;
        $member = Common::session('member');
        if (!empty($member))
        {
            $bool = 1;
            $member['orderNum'] = count(Model_Member_Order::unpay($member['mid']));
            $level=Common::member_rank($member['mid'],array('return'=>'current'));
            $member['fx_member']=0;
            $fx=Model_Fenxiao::is_fenxiao();
            $fx_only=Model_Fenxiao::is_fenxiao(true);
            $member['head']=<<<HEAD
            <img src="{$member['litpic']}" />
            <p><a>{$member['nickname']}</a><i class="level">{$level}</i></p>
HEAD;
            if(Model_Fenxiao::is_installed())
            {
                $fx_name=!$fx_only?'成为分销商':'分销商中心';
                $member['head'] .= <<<FENXIAO
                <a style="position: absolute;top:10px;right:0px;font-size: 1rem;color:white;background: #FF6600;padding: 2px;padding-left:4px;padding-right:4px;border-top-left-radius: 8px;border-bottom-left-radius: 8px" href="http://{$GLOBALS['main_host']}/plugins/fx_phone/">{$fx_name}</a>
FENXIAO;
            }
            if($fx && Model_Fenxiao::is_installed()){
                $refer=parse_url($_SERVER['HTTP_REFERER']);
                $fxurl=St_Functions::get_http_prefix().$refer['host'].$refer['path'].'?fxcode='.$fx['fxcode'];
                $fxurl=urlencode($fxurl);

                $member['fx_member']=1;
                $member['fx_btn']=<<<FXBTN
<a style="background:#FFCCCC;color:#fff" id="share_btn" href="javascript:;">我要分销</a>
<script>
  (function()
  {
   var is_new = typeof(Mobilebone)=='object'?1:0;
   $.ajax({
            url:SITEURL+'fenxiao/jieshao/share?isnew='+is_new,
            type:'POST',
            data:{url:"{$fxurl}"},
            dataType:'html',
            success:function(data,textStatus,jqXHR){
                 $("body").append(data);

            }
            });
   })();

</script>
FXBTN;
            }

            //检测模块是否安装
            if(St_Functions::is_system_app_install(101))
            {
                $notes_link = '<a href="/phone/notes/member/"><i class="ico_04"></i>我的游记</a>';
            }
            else if(St_Functions::is_model_exist(101))
            {
                $notes_link = '<a href="/phone/member/mynotes/"><i class="ico_04"></i>我的游记</a>';
            }
            else
            {
                $notes_link = '';
            }
            if(St_Functions::is_normal_app_install('coupon'))
            {
                $conpon_link = '<a href="/phone/member/coupon/"><i class="ico_07"></i>我的优惠券</a>';
            }
            else
            {
                $conpon_link = '';
            }
            $member['nav']=<<<NAV
            <a href="/phone/"><i class="ico_01"></i>首页</a>
                        <a href="/phone/member/order/list"><i class="ico_02"></i>我的订单<em>{$member['orderNum']}</em></a>
                        <a href="/phone/order/index"><i class="ico_05"></i>订单查询</a>
                        <a href="/phone/member/linkman"><i class="ico_03"></i>常用联系人</a>
                         {$conpon_link}
                        {$notes_link}
                        <a class="cursor" id="logout"><i class="ico_06"></i>退出</a>
NAV;
        }
        echo 'is_login(' . json_encode(array('islogin' => $bool, 'info' => $member)) . ');';
    }
}