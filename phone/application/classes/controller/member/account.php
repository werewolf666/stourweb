<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 * 帐户首页
 */
class Controller_Member_Account extends Stourweb_Controller
{
    private $_member = NULL;

    public function before()
    {
        parent::before();
        $this->_member = Common::session('member');
        $this->assign('member', $this->_member);
    }

    /**
     * 帐户首页
     */
    public function action_index()
    {
        $this->check_login();
        $user_info = Model_Member::get_member_byid($this->_member['mid']);
        $this->assign('info', $user_info);
        $this->display('member/account/index');
    }

    //帐户编辑
    public function action_edit()
    {
        $this->check_login();
        $token = md5(time());
        Common::session('crsf_code', $token);
        $user_info = Model_Member::get_member_byid($this->_member['mid']);
        $this->assign('info', $user_info);
        $this->assign('token', $token);
        $this->display('member/account/edit');

    }

    /**
     * 帐户资料保存
     */
    public function action_ajax_account_save()
    {
        $this->check_login();
        //安全校验码验证
        $token = Arr::get($_POST, 'token');
        $org_code = Common::session('crsf_code');
        if ($org_code != $token) {
            echo json_encode(array('status' => 0, 'msg' => '安全检验码错误'));
            exit;
        }
        $mid = $this->_member['mid'];
        $data = array();
        $verifystatus = DB::select('verifystatus')->from('member')->where('mid', '=', $mid)->execute()->get('verifystatus');
        if ($verifystatus != 2) {
            $data['sex'] = Common::remove_xss(Arr::get($_POST, 'sex'));
            $data['truename'] = Common::remove_xss(Arr::get($_POST, 'truename'));
            $data['birth_date'] = Common::remove_xss(Arr::get($_POST, 'birth_date'));
            $data['cardid'] = Common::remove_xss(Arr::get($_POST, 'cardid'));
            $data['constellation'] = Common::remove_xss(Arr::get($_POST, 'constellation'));
        }
        $data['nickname'] = Common::remove_xss(Arr::get($_POST, 'nickname'));
        $data['litpic'] = Common::remove_xss(Arr::get($_POST, 'litpic'));
        $data['native_place'] = Common::remove_xss(Arr::get($_POST, 'native_place'));
        $data['address'] = Common::remove_xss(Arr::get($_POST, 'address'));
        $data['qq'] = Common::remove_xss(Arr::get($_POST, 'qq'));
        $data['wechat'] = Common::remove_xss(Arr::get($_POST, 'wechat'));
        $data['signature'] = Common::remove_xss(Arr::get($_POST, 'signature'));
        $flag = DB::update('member')->set($data)->where('mid', '=', $mid)->execute();
        if ($flag) {
            $status = 1;
        }
        echo json_encode(array('status' => $status, 'msg' => '保存成功'));


    }

    /**
     * 绑定手机
     */
    public function action_phone()
    {
        $this->check_login();
        $token = md5(time());
        Common::session('crsf_code', $token);
        $user_info = Model_Member::get_member_byid($this->_member['mid']);
        $this->assign('info', $user_info);
        $this->assign('token', $token);
        $this->display('member/account/phone');
    }

    /**
     * 绑定手机帐号保存
     */
    public function action_ajax_phone_save()
    {
        $this->check_login();
        //安全校验码验证
        $token = Arr::get($_POST, 'token');
        $org_code = Common::session('crsf_code');
        if ($org_code != $token) {
            echo json_encode(array('status' => 0, 'msg' => '安全检验码错误'));
            exit;
        }
        //短信验证码
        $msg_code = Arr::get($_POST, 'checkcode');
        $mobile = Arr::get($_POST, 'phone');
        if (Common::session('mobilecode_' . $mobile) != $msg_code) {
            echo json_encode(array('status' => 0, 'msg' => '验证码错误'));
            exit;
        }
        $data = array(
            'mobile' => $mobile
        );
        $is_bundle = false; //是否绑定
        $mid = $this->_member['mid'];
        $mobile_member = DB::select()->from('member')->where('mobile', '=', $data['mobile'])->and_where('mid', '!=', $mid)->execute()->current();
        if (!$mobile_member) {
            $member_info = Model_Member::get_member_byid($mid);
            $old_mobile = $member_info['mobile'];
            $flag = DB::update('member')->set($data)->where('mid', '=', $mid)->execute();
            if ($flag) {
                if (empty($old_mobile)) {
                    $jifen = Model_Jifen::reward_jifen('sys_member_bind_phone', $mid);
                    if (!empty($jifen)) {
                        St_Product::add_jifen_log($mid, '绑定手机送积分' . $jifen, $jifen, 2);
                    }
                }
                $is_bundle = true;
            }
        }
        $data = $is_bundle ? array('status' => false, 'msg' => '手机号码绑定成功') : array('status' => false, 'msg' => '手机号已经注册');
        echo json_encode($data);
    }

    //检测手机是否注册
    public function action_ajax_check_phone()
    {

        $inputMobile = Arr::get($_POST, 'phone');
        $flag = Model_Member::check_member_exist($inputMobile);
        if (!$flag) {
            $flag = 'true';
        }
        else {
            $flag = 'false';
        }
        $flag = 'true';
        echo $flag;
    }

    public function action_email()
    {
        $this->check_login();
        $token = md5(time());
        Common::session('crsf_code', $token);
        $user_info = Model_Member::get_member_byid($this->_member['mid']);
        $this->assign('info', $user_info);
        $this->assign('token', $token);
        $this->display('member/account/email');
    }

    /**
     * 绑定邮箱保存
     */
    public function action_ajax_email_save()
    {
        $this->check_login();
        //安全校验码验证
        $token = Arr::get($_POST, 'token');
        $org_code = Common::session('crsf_code');
        if ($org_code != $token) {
            echo json_encode(array('status' => 0, 'msg' => '安全检验码错误'));
            exit;
        }
        //短信验证码
        $msg_code = Arr::get($_POST, 'checkcode');
        $email = Arr::get($_POST, 'email');
        if (Common::session('emailcode_' . md5($email)) != $msg_code) {
            echo json_encode(array('status' => 0, 'msg' => '验证码错误'));
            exit;
        }
        $data = array(
            'email' => $email
        );
        $is_bundle = false; //是否绑定
        $mid = $this->_member['mid'];
        $mobile_member = DB::select()->from('member')->where('email', '=', $data['email'])->and_where('mid', '!=', $mid)->execute()->current();
        if (!$mobile_member) {
            $member_info = Model_Member::get_member_byid($mid);
            $old_email = $member_info['email'];
            $flag = DB::update('member')->set($data)->where('mid', '=', $mid)->execute();
            if ($flag) {
                if (empty($old_email)) {
                    $jifen = Model_Jifen::reward_jifen('sys_member_bind_email', $mid);
                    if (!empty($jifen)) {
                        St_Product::add_jifen_log($mid, '绑定邮箱送积分' . $jifen, $jifen, 2);
                    }
                }
                $is_bundle = true;
            }
        }
        $data = $is_bundle ? array('status' => false, 'msg' => '邮箱绑定成功') : array('status' => false, 'msg' => '邮箱已经注册');
        echo json_encode($data);
    }

    /**
     * 发送邮件验证码
     */
    public function action_ajax_send_email_code()
    {
        $email = Arr::get($_POST, 'email');
        $token = Arr::get($_POST, 'token');//
        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if ($orgCode != $token) {
            echo json_encode(array('status' => false, 'msg' => '检验码错误'));
            exit;
        }
        $code = rand(1000, 9999);//验证码
        if (empty($email)) {
            echo json_encode(array('status' => true, 'msg' => '邮箱不能为空'));
            exit;
        }
        $title = $GLOBALS['cfg_webname'] . '邮箱验证码';
        $content = '邮件验证码' . $code;
        $flag = json_decode(St_EmailService::send_email($email, $title, $content));

        if ($flag)//发送成功
        {

            Common::session('emailcode_' . md5($email), $code);
            echo json_encode(array('status' => true, 'msg' => '发送邮箱验证码成功'));
        }
        else {
            echo json_encode(array('status' => false, 'msg' => '发送邮箱验证码失败'));
        }
    }

    /**
     * 发送短信验证码
     */
    public function action_ajax_send_msgcode()
    {
        $mobile = Arr::get($_POST, 'mobile');//手机
        $token = Arr::get($_POST, 'token');//
        $curtime = time();

        //安全校验码验证
        $orgcode = Common::session('crsf_code');
        if ($orgcode != $token) {
            echo json_encode(array('status' => false, 'msg' => '检验码错误'));
            exit;
        }


        //手机号验证
        if (empty($mobile)) {
            echo json_encode(array('status' => false, 'msg' => '手机号不能为空'));
            exit;
        }
        else {
            $sentNum = Common::session('sendnum_' . $mobile); //已发验证码次数
            $lastSentTime = Common::session('senttime_' . $mobile);//上次发送时间
            $sentNum = empty($sentNum) ? 0 : $sentNum;
            $lastSentTime = empty($lastSentTime) ? 0 : $lastSentTime;

            /* if($sentNum<3&&$sentNum>0&&$lastSentTime>($curtime-60))
             {
                 echo json_encode(array('status'=>false,'msg'=>'验证码发送过于频繁，请稍后再试'));
                 exit;
             }

             if($sentNum>=3&&$lastSentTime>($curtime-60*15))
             {
                 echo json_encode(array('status'=>false,'msg'=>'验证码发送过于频繁，15分钟后再试'));
                 exit;
             }*/

            $code = rand(1000, 9999);
            $flag = json_decode(St_SMSService::send_member_msg($mobile, NoticeCommon::MEMBER_REG_CODE_MSGTAG, "", "", $code));

            if ($flag->Success)//发送成功
            {

                Common::session('senttime_' . $mobile, $curtime);
                $sentNum = $sentNum >= 3 ? 0 : $sentNum + 1;
                Common::session('sendnum_' . $mobile, $sentNum);
                Common::session('mobilecode_' . $mobile, $code);
                echo json_encode(array('status' => true, 'msg' => '验证码发送成功'));
            }
            else {
                echo json_encode(array('status' => false, 'msg' => $flag->Message . '导致发送失败'));
            }

        }
    }

    //检测邮箱是否注册
    public function action_ajax_check_email()
    {

        $inputMobile = Arr::get($_POST, 'email');
        $flag = Model_Member::check_member_exist($inputMobile);
        if (!$flag) {
            $flag = 'true';
        }
        else {
            $flag = 'false';
        }
        $flag = 'true';
        echo $flag;
    }


    /**
     * 修改密码
     */
    public function action_password()
    {
        $this->check_login();
        $token = md5(time());
        Common::session('crsf_code', $token);
        $user_info = Model_Member::get_member_byid($this->_member['mid']);
        $this->assign('info', $user_info);
        $this->assign('token', $token);
        $this->display('member/account/password');
    }

    /**
     * 修改密码保存
     */
    public function action_ajax_password_save()
    {
        $this->check_login();
        //安全校验码验证
        $token = Arr::get($_POST, 'token');
        $org_code = Common::session('crsf_code');
        if ($org_code != $token) {
            echo json_encode(array('status' => 0, 'msg' => '安全检验码错误'));
            exit;
        }
        $oldpwd = Arr::get($_POST, 'oldpwd');
        $newpwd1 = Arr::get($_POST, 'newpwd1');
        $newpwd2 = Arr::get($_POST, 'newpwd2');
        if (empty($oldpwd) || $newpwd1 != $newpwd2 || empty($newpwd1)) {
            echo json_encode(array('status' => 0, 'msg' => '密码不能为空'));
            exit;
        }
        $mid = $this->_member['mid'];
        $user_pwd = DB::select('pwd')->from('member')->where('mid', '=', $mid)->execute()->get('pwd');
        if (md5($oldpwd) != $user_pwd) {
            echo json_encode(array('status' => 0, 'msg' => '旧密码输入错误'));
            exit;
        }
        $newpwd = md5($newpwd1);
        $flag = DB::update('member')->set(array('pwd' => $newpwd))->where('mid', '=', $mid)->execute();
        if ($flag) {
            $status = 1;
        }
        echo json_encode(array('status' => $status, 'msg' => '保存成功'));


    }

    /**
     * 检测是否登陆
     */
    public function check_login()
    {
        $this->member = Common::session('member');
        if (empty($this->member)) {
            $this->request->redirect('member/login');
        }
    }

    /**
     * 实名认证页
     */
    public function action_certification()
    {
        $this->check_login();
        $user_info = Model_Member::get_member_byid($this->_member['mid']);
        $idcard_pic = $user_info['idcard_pic'];
        if (!empty($idcard_pic)) {
            $idcard_pic_arr = json_decode($idcard_pic, 1);
            if (!is_null($idcard_pic_arr)) {
                $this->assign('idcard_pic', $idcard_pic_arr);
            }
        }
        $this->assign('info', $user_info);
        $this->display('member/account/certification');
    }

    /**
     * 保存实名认证信息
     */
    public function action_ajax_certification_save()
    {
        //验证是否登录状态
        $member = Common::session('member');
        if (empty($member)) {
            exit(json_encode(array('status' => 0, 'msg' => '请先登录再提交')));
        }

        $mid = $member['mid'];
        $model = ORM::factory('member')->where('mid', '=', $mid)->find();
        $truename = Common::remove_xss(Arr::get($_POST, 'truename'));
        $cardid = Common::remove_xss(Arr::get($_POST, 'cardid'));
        $idcard_positive = Common::remove_xss(Arr::get($_POST, 'idcard_positive'));
        $idcard_negative = Common::remove_xss(Arr::get($_POST, 'idcard_negative'));
        $idcard_pic = array(
            'front_pic' => $idcard_positive,
            'verso_pic' => $idcard_negative,
        );
        $model->truename = $truename;
        $model->cardid = $cardid;
        $model->idcard_pic = json_encode($idcard_pic);
        $model->verifystatus = 1;

        $model->save();
        if ($model->saved()) {
            exit(json_encode(array('status' => 1, 'msg' => '提交成功,等待审核')));
        }

        exit(json_encode(array('status' => 0, 'msg' => '提交成功,请重试')));
    }

    /**
     * 实名认证身份证照片上传
     */
    public function action_ajax_upload_img()
    {
        //验证是否登录状态
        $member = Common::session('member');
        if (empty($member)) {
            exit(json_encode(array('success' => 'false', 'msg' => '请先登录再上传')));
        }

        $pinyin = 'member';
        $file = $_FILES['Filedata'];
        $storepath = BASEPATH . '/uploads/' . $pinyin;
        $dir = $storepath . "/certification/" . date('Ymd'); //原图存储路径.

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $path_info = pathinfo($_FILES['Filedata']['name']);
        $filename = date('YmdHis');

        $i = 0;
        while (file_exists($dir . '/' . $filename . '.' . $path_info['extension'])) {
            $i = $i + 50;
            $filename = date('YmdHis') . $i;
        }
        $filename = $filename . '.' . $path_info['extension'];

        Upload::$default_directory = $dir;//默认保存文件夹
        Upload::$remove_spaces = true;//上传文件删除空格

        if (Upload::valid($file)) {
            if (Upload::size($file, "2M")) {
                if (Upload::type($file, array('jpg', 'png', 'gif'))) {
                    if (Upload::save($file, $filename)) {
                        $srcfile = $dir . '/' . $filename; //原图

                        $arr['success'] = 'true';
                        $arr['litpic'] = $GLOBALS['$cfg_basehost'] . substr(substr($srcfile, strpos($dir, '/uploads') - 1), 1);
                    }
                    else {
                        $arr['success'] = 'false';
                        $arr['msg'] = '未知错误,上传失败';
                    }
                }
                else {
                    $arr['success'] = 'false';
                    $arr['msg'] = '类型不支持';
                }
            }
            else {
                $arr['success'] = 'false';
                $arr['msg'] = '图片大小超过限制';
            }
        }
        else {
            $arr['success'] = 'false';
            $arr['msg'] = '未知错误,上传失败';
        }
        exit(json_encode($arr));
    }
}

