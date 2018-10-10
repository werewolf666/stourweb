<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Register
 * 会员注册
 */
class Controller_Member_Register extends Stourweb_Controller
{

    //注册首页
    public function action_index()
    {
        if(St_Functions::is_normal_app_install('mobiledistribution'))
        {
            Model_Fenxiao::save_fxcode();
        }
        $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;
        if (!stripos($this->referer, '/message/') && !stripos($this->referer, '/member/'))
        {
            Cookie::set('referer', $this->referer);
        }
        $this->assign('referer', Cookie::get('referer', $this->cmsurl));
        $sms = DB::select()->from('sms_msg')->where('msgtype="reg_msgcode"')->execute()->current();
        $this->assign('isopen', $sms['isopen']);

        $emailcode_sms = DB::select()->from('email_msg')->where('msgtype="reg_msgcode"')->execute()->current();
        $this->assign('is_emailcode_open',$emailcode_sms['isopen']);

        $token = md5(time());
        Common::session('crsf_code', $token);
        $this->assign('frmcode', $token);
        $this->assign('url', $this->cmsurl . 'member/');
        $this->display('member/register','member_reg');
    }

    //注册执行
    public function action_ajax_reg()
    {
        $user = Common::remove_xss(Arr::get($_POST, 'user'));
        $pwd = Common::remove_xss(Arr::get($_POST, 'pwd'));
        $frmcode = Common::remove_xss(Arr::get($_POST, 'frmcode'));
        $code = Common::remove_xss(Arr::get($_POST, 'code'));
        $msgcode = Common::remove_xss(Arr::get($_POST, 'msg'));

        if ($frmcode != Common::session('crsf_code'))
        {
            $message = array('msg' => __("error_frmcode"), 'status' => 0);
            exit(json_encode($message));
        }


        if (!Captcha::valid($code))
        {
            $message = array('msg' => __("error_code"), 'status' => 0);
            exit(json_encode($message));
        }

        if (!empty($msgcode))
        {
            if (Common::session('msg_code') != $msgcode)
            {
                $message = array('msg' => __("error_msgcode"), 'status' => 0);
                exit(json_encode($message));
            }
        }


        $userType = isset($_POST['is_email']) ? 'email' : 'phone';
        $validataion = Validation::factory($this->request->post());
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
        //用户不存在则添加
        $member = Model_Member::member_find($user);
        if (!empty($member))
        {
            $message = array('msg' => __("error_user_exists"), 'status' => 0);
            exit(json_encode($message));
        }
        $isPhone = false;
        $regtype = 0;
        if ($userType == 'phone')
        {
            $isPhone = true;
            $userType = 'mobile';
        } else
        {
            $regtype = 1;
        }

        $addtime = time();
        list($insertId, $rows) = DB::insert('member', array('nickname', $userType, 'jointime', 'logintime', 'pwd', 'regtype'))->values(array($user, $user, $addtime, $addtime, md5($pwd), $regtype))->execute();
        if ($rows > 0)
        {
            Common::session('crsf_code', null);
            Common::session('captcha_response', null);
            Common::session('msg_code', null);

            //发送注册成功信息
            if ($isPhone)
            {
                St_SMSService::send_member_msg($user,NoticeCommon::MEMBER_REG_MSGTAG,$user,$pwd,"");
            } else
            {
                St_EmailService::send_member_email($user,NoticeCommon::MEMBER_REG_MSGTAG,$pwd,"");
            }


            //注册送积分
            $jifen = Model_Jifen::reward_jifen('sys_member_register',$insertId);
            if(!empty($jifen))
            {
                St_Product::add_jifen_log($insertId,"注册赠送积分{$jifen}",$jifen,2);
            }

            //登录状态
            Model_Member::write_session(Model_Member::get_member_byid($insertId));



            $message = array('url' => Cookie::get('referer', $this->cmsurl), 'status' => 1);
            $ucsynlogin = '';
            if (defined('UC_API') && @include_once BASEPATH . '/uc_client/client.php')
            {
                $uid = uc_user_register($user, $pwd, $user);
                if ($uid > 0)
                {
                    $ucsynlogin = uc_user_synlogin($uid);
                }
            }
            $message['js'] = $ucsynlogin;
            Plugin_Core_Factory::factory()->add_listener('on_member_register', ORM::factory('member', $insertId)->as_array())->execute();
            exit(json_encode($message));
        } else
        {
            $message = array('msg' => __("error_member_insert"), 'status' => 0);
            exit(json_encode($message));
        }
    }

    /**
     * ajax 发送验证码
     */
    public function action_ajax_send_message()
    {
        $frmcode = Common::remove_xss(Arr::get($_POST, 'frmcode'));
        $code = Common::remove_xss(Arr::get($_POST, 'code'));

        if ($frmcode != Common::session('crsf_code'))
        {
            exit(__('error_frmcode'));
        }

        if (!Captcha::valid($code))
        {
            exit(__('error_code'));
        }

        $validataion = Validation::factory($this->request->post());
        $validataion->rule('phone', 'not_empty');
        $validataion->rule('phone', 'phone');
        if (!$validataion->check())
        {
            exit(__('error_user_phone'));
        }
        //检测用户是否存在
        $phone = Common::remove_xss(Arr::get($_POST, 'phone'));
        $member = Model_Member::member_find($phone);
        if (!empty($member))
        {
            exit(__('error_user_exists'));
        }
        $code = rand(1000, 9999);

        Common::session('msg_code',null);
        $status = json_decode(St_SMSService::send_member_msg($phone,NoticeCommon::MEMBER_REG_CODE_MSGTAG,"","",$code));
        if($status->Success){
            Common::session('msg_code', $code);
        }
        echo intval($status->Success);
    }
    /**
     * ajax 发送验证码
     */
    public function action_ajax_send_email_message()
    {
        $frmcode =$_POST['frmcode'];
        $code = $_POST['code'];

        if ($frmcode != Common::session('crsf_code'))
        {
            exit(__('error_frmcode'));
        }
        if (!Captcha::valid($code))
        {
            exit(__('error_code'));
        }

        $validataion = Validation::factory($this->request->post());
        $validataion->rule('email', 'not_empty');
        $validataion->rule('email', 'email');
        if (!$validataion->check())
        {
            exit(__('error_user_email'));
        }
        //检测用户是否存在
        $email =$_POST['email'];
        $member = Model_Member::member_find($email);
        if (!empty($member))
        {
            exit(__('error_user_exists'));
        }
        $code = rand(1000, 9999);
        Common::session('msg_code', null);
        $result = St_EmailService::send_member_email($email,NoticeCommon::MEMBER_REG_CODE_MSGTAG,'',$code);
        if($result)
        {
            Common::session('msg_code', $code);
        }
        echo intval($result);
    }

}