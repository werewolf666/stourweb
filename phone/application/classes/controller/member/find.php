<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Find
 * 会员注册
 */
class Controller_Member_Find extends Stourweb_Controller
{
    //
    private $_member = null;

    /**
     * 通过referer 确定返回链接地址
     */
    public function before()
    {
        parent::before();
        $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;
        $this->assign('url', $this->referer);
    }

    /**
     * 找回密码
     * get 显示视图
     */
    public function action_index()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        switch ($method)
        {
            case 'get':
                //是否开启短信
                $sms = DB::select()->from('sms_msg')->where('msgtype="reg_findpwd"')->execute()->current();
                $this->assign('isopen', $sms['isopen']);
                $this->assign('url',$this->cmsurl . "member/login");
                $this->display('member/find');
                break;
            case 'post':
                //检查验证码
                $_POST = Common::remove_xss($_POST);
                $userType = strpos($_POST['user'], '@') ? 'email' : 'phone';
                $validataion = Validation::factory($this->request->post());
                $validataion->rule('code', 'not_empty');
                $validataion->rule('user', 'not_empty');
                $validataion->rule('user', $userType);
                if ($validataion->check())
                {
                    $user = Arr::get($_POST, 'user');
                    $code = Arr::get($_POST, 'code');
                    if (Captcha::valid($code))
                    {
                        Session::instance()->delete('captcha_response');
                        //检查用户是否存在
                        $member = Model_Member::member_find($user);
                        if ($member)
                        {
                            //验证信息写入缓存
                            $this->member = $member;
                            if (strpos($user, '@'))
                            {
                                $data = array(
                                    'user' => $member['email'],
                                    'mid' => $member['mid'],
                                    'token' => $member['pwd'],
                                    'expired' => strtotime('+1 day')
                                );
                                $type = 'email';
                            }
                            else
                            {
                                $data = array(
                                    'user' => $member['mobile'],
                                    'mid' => $member['mid'],
                                    'token' => $member['pwd'],
                                    'expired' => strtotime('+20 minutes')
                                );
                                $type = 'mobile';
                            }
                            $this->put_cache($data);
                            $message = array('url' => $this->cmsurl . "member/find/{$type}?token=" . md5($data['user']), 'status' => 1);
                            exit(json_encode($message));
                        }
                        else
                        {
                            $message = array('msg' => __("error_user_noexists"), 'status' => 0);
                            exit(json_encode($message));
                        }
                    }
                    else
                    {
                        $message = array('msg' => __("error_code"), 'status' => 0);
                        exit(json_encode($message));
                    }
                }
                else
                {
                    Common::session('captcha_response', null);
                    $error = $validataion->errors();
                    $keys = array_keys($validataion->errors());
                    $message = array('msg' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), 'status' => 0);
                    exit(json_encode($message));
                }
                break;
        }
    }

    /**
     * 重置密码
     */
    public function  action_reset()
    {
        $param = $this->request->param();
        $_POST = Common::remove_xss($_POST);
        if (!empty($param))
        {
            $param = $param['query'];
        }
        $cache = APPPATH . 'cache/find/' . $param . '.php';
        //如果包含验证则检查验证码
        if (isset($_POST['code']))
        {
            if (empty($_POST['code']) || Common::session('msg_code') != $_POST['code'])
            {
                Common::session('msg_code', null);
                $message = array('msg' => __("error_code"), 'status' => 0);
                exit(json_encode($message));
            }else{
                $message = array('url' => $this->cmsurl."member/find/reset/{$param}", 'status' => 1);
                exit(json_encode($message));
            }
        }
        if (!empty($param) || file_exists($cache))
        {
            $data = include($cache);
            //已过有效验证时间
            if (time() < $data['expired'])
            {
                //根据mid 选择修改密码或显示重置密码视图
                $mid = Arr::get($_POST, 'mid');
                if ($mid)
                {
                    //修改密码
                    $pwd = Arr::get($_POST, 'pwd');
                    $rows = DB::update('member')->set(array('pwd' => md5($pwd)))->where("mid={$_POST['mid']} and pwd='{$_POST['token']}'")->execute();
                    $this->assign('issuccess', $rows > 0 ? true : false);
                    //删除验证缓存
                    unlink($cache);
                    $this->display('member/find_success');
                }
                else
                {
                    $this->assign('data', $data);
                    $this->display('member/find_reset');
                }
            }
            else
            {
                if (strtolower($_SERVER['REQUEST_METHOD']) == 'get')
                {
                    $this->request->redirect("member/find/expired");
                }
                else
                {
                    $message = array('msg' => __("error_find_pwd"), 'status' => 0);
                    exit(json_encode($message));
                }
            }
        }
        else
        {
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'get')
            {
                $this->request->redirect("member/find/expired");
            }
            else
            {
                $message = array('msg' => __("error_find_pwd"), 'status' => 0);
                exit(json_encode($message));
            }
        }
    }

    /**
     * ajax 发送验证码
     */
    public function action_ajax_send_message()
    {
        $cache = APPPATH . 'cache/find/' . Arr::get($_POST, 'cache') . '.php';
        $status = false;
        if (file_exists($cache))
        {
            $data = include($cache);
            $code = rand(1000, 9999);

            Common::session('msg_code',null);
            $status = json_decode(St_SMSService::send_member_msg($data['user'],NoticeCommon::MEMBER_FINDPWD_CODE_MSGTAG,"","",$code));
            if($status->Success){
                Common::session('msg_code', $code);
            }
            $status = $status->Success;
        }
        echo intval($status);
    }

    /**
     * 邮件找回密码
     */
    public function action_email()
    {
        $member = $this->get_cache($_GET['token']);
        if (empty($member))
        {
            $this->request->redirect("member/find/expired");
        }
        //发送邮件
        $md5 = md5($member['user']);
        $title = "{$GLOBALS['cfg_webname']}用户找回密码--{$GLOBALS['cfg_webname']}";
        $header = "<html><body>";
        $content = "<p>尊敬的会员：</p>
        <p>您好！欢迎使用邮箱验证找回密码!</p>
        <p>请点击下面的链接找回你的登陆密码,如果验证邮箱链接无法正常打开，请直接将以下地址复制到地址栏:</p>
        <p><a href='{$GLOBALS['cfg_basehost']}{$this->cmsurl}member/find/reset/{$md5}'>{$GLOBALS['cfg_basehost']}{$this->cmsurl}member/find/reset/{$md5}</a></p>";
        $content .= "</body></html>";
        $status = Common::send_email($member['user'], $title, $header . $content);
        $this->assign('md5', $md5);
        $this->display('member/find_email');
    }

    /**
     * 手机找回密码
     */
    public function action_mobile()
    {
        $member = $this->get_cache($_GET['token']);
        if (empty($member))
        {
            $this->request->redirect("member/find/expired");
        }
        $this->assign('md5', md5($member['user']));
        $this->assign('mobile', $member['user']);
        //发送邮件
        $this->display('member/find_mobile');
    }

    /**
     * 找回密码已过有效期
     */
    public function action_expired(){
        $this->display('member/find_expired');
    }
    /**
     * 验证信息写入缓存文件
     * @param $data
     */
    private function put_cache($data)
    {
        $path = APPPATH . 'cache/find/';
        if (!file_exists($path))
        {
            mkdir($path, 0777);
        }
        file_put_contents($path . md5($data['user']) . '.php', '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');' . PHP_EOL . 'return ' . var_export($data, true) . ';');
    }

    /**
     * 获取验证缓存
     * @param $file
     * @return mixed|null
     */
    private function get_cache($file)
    {
        $data = null;
        $file = APPPATH . "cache/find/{$file}.php";
        if (file_exists($file))
        {
            $data = include($file);
        }
        return $data;
    }

}