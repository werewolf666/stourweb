<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Login extends Stourweb_Controller{


    //后台登录页面
    public function action_index()
    {
        //$config = Common::getConfig('menu.产品');
        //$test = $this->authcode('netman','','mytest');
        //echo $test;

       //echo 'decode:'.$this->_authcode($test,'DECODE','mytest');
       // exit;


        $session = Session::instance();
        $session_username = $session->get('username');
        $cookie_username = Cookie::get('username');

        if(isset($session_username) || isset($cookie_username))
        {

            echo "<script>window.location.href='/{$GLOBALS['cfg_backdir']}/';</script>";
        }

        $form_code = md5(mt_rand(1000,99999));
        $session->set('form_code',$form_code);

        $userip = $this->getIp();
        $this->assign('ip',$userip);
        $this->assign('form_code',$form_code);
        $this->assign('majorVersion',Model_SystemParts::getCoreMajorVersion());
        $this->display('stourtravel/login/login');

    }
    //验证是否已经登录
    public static function checklogin()
    {
		$username = Common::remove_xss(Session::instance()->get('username'));
		if($username)
		   return ORM::factory('admin')->where("username='$username'")->as_array(); 
		
		$username = Common::remove_xss(Cookie::get('username'));
	    if($username)
		{
		   $userinfo=ORM::factory('admin')->where("username='$username'")->as_array(); 
		   $session = Session::instance();
		   $session->set('username',$userinfo['username']);
		   $session->set('userid',$userinfo['id']);
		   $session->set('roleid',$userinfo['roleid']);

		   $rolemodule=ORM::factory('role_module')->where("roleid='{$userinfo['roleid']}'")->as_array();
		   $session->set('rolemodule',$rolemodule);
		   return $userinfo;
		}
		return false; 
    }
	//登录
	public function action_ajax_login()
	{
        $session = Session::instance();
        $org_form_code = $session->get('form_code');
        $form_code = $_POST['form_code'];

		$checkcode= Common::remove_xss(Arr::get($_POST, 'checkcode'));
		$username= Common::remove_xss(Arr::get($_POST, 'username'));
		$password= Common::remove_xss(Arr::get($_POST,'password'));
        $out = array();

        if(empty($form_code) || $org_form_code!=$form_code)
        {
            echo json_encode(array('status'=>false,'msg'=>'请求错误，请刷新登录页重试'));
            return;
        }
		
		//验证码 
		$checkcode=strtolower($checkcode);
        if(!Captcha::valid($checkcode))
        {
           // $out['status'] = 'checkcode_err';
            echo json_encode(array('status'=>false,'msg'=>'验证码错误'));
            return;
        }

		//用户名密码验证
		$password=md5($password);

		$userinfo=ORM::factory('admin')->where('username','=',$username)->and_where('password','=',$password)->find();

		if(!$userinfo->loaded())
		{
            echo json_encode(array('status'=>false,'msg'=>'用户名或密码错误'));
            return;
		}

        $session->set('form_code','');
        $logintime = time();
        $ip = $this->getIp();
        $userinfo->logintime = $logintime;
        $userinfo->loginip = $ip;
        $userinfo->update();

        $userinfo = $userinfo->as_array();

		
		//将用户信息保存到session
		$session = Session::instance();

        $serectkey = Common::authcode($userinfo['username'].'||'.$userinfo['password'],'');

		$session->set('username',$serectkey);
        Cookie::set('username',$serectkey);
		$session->set('userid',$userinfo['id']);
		$session->set('roleid',$userinfo['roleid']);

		$rolemodule=ORM::factory('role_module')->where("roleid='{$userinfo['roleid']}'")->as_array();
		$session->set('rolemodule',$rolemodule);
		$out['status']='ok';
        echo json_encode($out);
        //后台初始化
        Common::runtime();
	}

    //退出登陆
    public function action_loginout()
    {
        $session = Session::instance();
        $session->delete('username');
        $session->delete('userid');
        $session->delete('roleid');
        Cookie::delete('username');
        $this->request->redirect('login/index');
    }

    //获取IP
    private function getIp()
    {

        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";

        return $ip;

    }

	
	
	
	
}