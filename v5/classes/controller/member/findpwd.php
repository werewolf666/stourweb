<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Findpwd
 * 找回密码
 */
class Controller_Member_Findpwd extends Stourweb_Controller
{

    //找回密码首页(第一步)
    public function action_index()
    {

        $step = 1;
        $forwardurl = Arr::get($_GET,'forwardurl');
        if(!empty($forwardurl))
        {
            $this->assign('backurl',$forwardurl);
        }
        //token
        $token = md5(time());
        //$token = 'testnetman';
        Common::session('crsf_code',$token);
        $this->assign('frmcode',$token);
        $this->assign('step',$step);
        $this->display('member/findpwd/index');
    }

    //找回密码第二步
    public function action_step2()
    {
        $frmCode = Common::remove_xss(Arr::get($_POST,'frmcode'));
        $loginName = Common::remove_xss(Arr::get($_POST,'loginname'));
        $account = preg_replace("/(d{3})d{5}/","$1*****",$loginName);
        $checkCode = Common::remove_xss(Arr::get($_POST,'checkcode'));
        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if($orgCode!=$frmCode)
        {
            exit('error token');
        }
        else
        {
            Common::session('crsf_code',null);
        }
        //验证码验证
        if(!Captcha::valid($checkCode) || empty($checkCode))
        {
            exit('error checkcode');
        }
        else
        {
            //清空验证码
            Common::session('captcha_response',null);
        }

        //是邮箱找回,还是密码找回
        $isPhone=strpos($loginName,'@')===false?true:false;
        $token = md5(time());
        //$token = 'testnetman';
        Common::session('crsf_code',$token);
        $this->assign('frmcode',$token);
        $this->assign('step',2);
        $this->assign('account',$account);
        $this->assign('loginname',$loginName);
        $this->assign('isphone',$isPhone);
        $this->display('member/findpwd/step2');
    }
    //找回密码第三步
    public function action_step3()
    {

        $frmCode = Common::remove_xss(Arr::get($_POST,'frmcode'));
        $loginName = Common::remove_xss(Arr::get($_POST,'loginname'));
        $msgCode = Common::remove_xss(Arr::get($_POST,'msgcode'));
        $password1 = Common::remove_xss(Arr::get($_POST,'password1'));
        $password2 = Common::remove_xss(Arr::get($_POST,'password2'));
        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if($orgCode!=$frmCode)
        {
            exit('error token');
        }
        else
        {
            Common::session('crsf_code',null);
        }
        //密码验证
        if($password1!=$password2)
        {
            exit('error pwd not match');
        }

        $isPhone=strpos($loginName,'@')===false?true:false;
        if($isPhone)
        {
            $flag = $this->check_mobile_code($msgCode,$loginName);
        }
        else
        {
            $flag = $this->check_email_code($msgCode,$loginName);
        }
        //如果通过验证执行修改密码功能.
        $saved = false;
        if($flag=='true')
        {
            $m = ORM::factory('member')
                ->where("mobile='$loginName' or email='$loginName'")
                ->find();
            $m->pwd = md5($password1);
            $m->save();
            if($m->saved())
            {
                $saved = true;
            }

        }
        //如果保存成功
        if($saved)
        {
            $this->display('member/findpwd/step3');
        }
        else
        {
            Common::message(array(
                'status'=>0,
                'msg'=>'找回密码失败',
                'jumpUrl'=>$_SERVER['HTTP_REFERER']
            ));
        }

    }



    //检测登陆名是否存在
    public function action_ajax_check_loginname()
    {

        $input = Arr::get($_POST,'loginname');
        $flag = Model_Member::check_member_exist($input);
        if(!$flag)
        {
            $flag = 'false';
        }
        else
        {
            $flag = 'true';
        }
        echo $flag;
    }


    //检测动态验证码
    public function action_ajax_check_code()
    {
        $loginName = Arr::get($_POST,'loginname');
        $msgCode = Arr::get($_POST,'msgcode');
        $isPhone=strpos($loginName,'@')===false?true:false;
        if($isPhone)
        {
            $flag = $this->check_mobile_code($msgCode,$loginName);
        }
        else
        {
            $flag = $this->check_email_code($msgCode,$loginName);
        }
        echo $flag;

    }

    /**
     * 发送验证码
     */



    public function action_ajax_send_code()
    {

        $loginName = Arr::get($_POST,'loginname');//
        $token = Arr::get($_POST,'token');//

        $isPhone=strpos($loginName,'@')===false?true:false;


        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if($orgCode!=$token)
        {
            echo json_encode(array('status'=>false,'msg'=>'检验码错误'));
            exit;
        }


        Common::session('captcha_response','');

        if($isPhone)
        {
            $out = $this->send_msg_code($loginName);
        }
        else
        {
            $out = $this->send_email_code($loginName);
        }
        echo json_encode($out);





    }



    /*
     * 检测手机短信验证码是否正确
     * */
    private function check_mobile_code($msgcode,$mobile)
    {

        $flag = 'false';
        if(Common::session('mobilecode_'.$mobile) == $msgcode)
        {
            $flag = 'true';
           // Common::session('mobilecode_'.$mobile,null);
        }
        return $flag;
    }

    /**
     * 检测email验证码是否正确.
     */
    private  function check_email_code($msgcode,$email)
    {

        $flag = 'false';
        if(Common::session('emailcode_'.md5($email)) == $msgcode)
        {
            $flag = 'true';
            //Common::session('emailcode_'.md5($email),null);

        }
        return $flag;
    }









    private static function send_msg_code($mobile)
    {
        $curtime=time();
        //手机号验证
        if(empty($mobile))
        {
            echo json_encode(array('status'=>false,'msg'=>'手机号不能为空'));
            exit;
        }
        else
        {
            $sentNum = Common::session('sendnum_'.$mobile); //已发验证码次数
            $lastSentTime = Common::session('senttime_'.$mobile);//上次发送时间
            $sentNum = empty($sentNum) ? 0 : $sentNum;
            $lastSentTime=empty($lastSentTime)?0:$lastSentTime;

            if($sentNum<3&&$sentNum>0&&$lastSentTime>($curtime-60))
            {
                return array('status'=>false,'msg'=>'验证码发送过于频繁，请稍后再试');

            }

            if($sentNum>=3&&$lastSentTime>($curtime-60*15))
            {
                return array('status'=>false,'msg'=>'验证码发送过于频繁，15分钟后再试');

            }

            $code =  Common::get_rand_code(5);//验证码

            $flag = json_decode(St_SMSService::send_member_msg($mobile,NoticeCommon::MEMBER_FINDPWD_CODE_MSGTAG,"","",$code));

            if($flag->Success)//发送成功
            {

                Common::session('senttime_'.$mobile,$curtime);
                $sentNum=$sentNum>=3?0:$sentNum+1;
                Common::session('sendnum_'.$mobile,$sentNum);
                Common::session('mobilecode_'.$mobile,$code);
                return array('status'=>true,'msg'=>'验证码发送成功');
            }
            else
            {
                return array('status'=>false,'msg'=> $flag->Message . '导致发送失败');
            }

        }
    }

    /*
     * 发送邮箱验证码
     * */
    private static function send_email_code($email)
    {

        $code =  Common::get_rand_code(5);//验证码
        if(empty($email))
        {
            return array('status'=>true,'msg'=>'邮箱不能为空');

        }

        $status = St_EmailService::send_member_email($email,NoticeCommon::MEMBER_FINDPWD_CODE_MSGTAG,"",$code);
        if($status)
        {

            Common::session('emailcode_'.md5($email),$code);
            return array('status'=>true,'msg'=>'发送邮箱验证码成功');
        }
        else
        {
           return  array('status'=>false,'msg'=>'发送邮箱验证码失败');
        }


    }





}