<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member
 * 会员总控制器
 */
class Controller_Member_Index extends Stourweb_Controller
{


    private $mid = null;
    private $refer_url = null;

    public function before()
    {
        parent::before();
        $this->refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $GLOBALS['cfg_cmsurl'];
        $this->assign('backurl', $this->refer_url);
        $user = Model_Member::check_login();
        if (!empty($user['mid']))
        {
            $this->mid = $user['mid'];
        }
        else
        {
            $this->request->redirect('member/login');
        }

        $this->assign('mid', $this->mid);
    }

    //会员中心首页
    public function action_index()
    {
        $info = Model_Member::get_member_byid($this->mid);
        //未付款订单数量
        $unPay = ORM::factory('member_order')
            ->where("memberid=$this->mid and status=1")
            ->find_all()
            ->count();
        //未评论订单数量
        $unComment = ORM::factory('member_order')
            ->where("memberid=$this->mid and status=5 and ispinlun=0 and typeid<>111")
            ->find_all()
            ->count();
        //咨询数量
        $question = ORM::factory('question')
            ->where("memberid=$this->mid")
            ->find_all()
            ->count();
        //优惠券数量
        if(St_Functions::is_normal_app_install('coupon'))
        {
            $coupon = DB::select()->from('member_coupon')->where("(usenum=0 or usetime=0) and `mid`=$this->mid")->execute()->count();
            $info['coupon'] = $coupon;
        }
        $info['unpay'] = $unPay;
        $info['uncomment'] = $unComment;
        $info['question'] = $question;

        //最新订单
        $order = Model_Member_Order::order_list(0, $this->mid, 'all', 1, 5);
        foreach($order['list'] as &$v)
        {
            $v['is_commentable'] = Model_Model::is_commentable($v['typeid']);
            $v['is_standard_product'] =  Model_Model::is_standard_product($v['typeid']);
            if($v['typeid']==107){
                $v['producturl'] = St_Functions::get_web_url($v['webid']) . "/integral/show_{$v['productautoid']}.html";
            }
        }
        $this->assign('neworder', $order['list']);
        $this->assign('info', $info);
        $this->display('member/index');
    }


    //个人资料
    public function action_userinfo()
    {
        $token = md5(time());
        Common::session('crsf_usr_code', $token);
        $userinfo = Model_Member::get_member_byid($this->mid);
        $userinfo['litpic'] = empty($userinfo['litpic']) ? Common::member_nopic() : $userinfo['litpic'];
        $this->assign('constellation', $this->_constellation());
        $this->assign('info', $userinfo);
        $this->assign('frmcode', $token);
        $this->display('member/userinfo');
    }

    private function _constellation()
    {
        return array('水瓶座', '双鱼座', '白羊座', '金牛座', '双子座', '巨蟹座', '狮子座', '处女座', '天秤座', '天蝎座', '射手座', '魔羯座');
    }

    //头像修改页面
    public function action_uploadface()
    {
        $this->display('member/uploadface');
    }

    //安全中心
    public function action_safecenter()
    {
        $userinfo = Model_Member::get_member_byid($this->mid);
        $this->assign('info', $userinfo);
        $this->display('member/safecenter');
    }

    //账号绑定
    public function action_userbind()
    {
        $thirdinfo = Model_Member::get_member_byid($this->mid);
        $this->assign('info', $thirdinfo);
        $this->assign('thirdBindMsg', Session::instance()->get("thirdBindMsg"));
        Session::instance()->set("thirdBindMsg", null);
        $this->display('member/userbind');
    }

    //解除绑定
    public function action_ajax_userunbind()
    {
        $id = intval(Arr::get($_GET, 'id'));
        echo json_encode(Model_Member_Third::unbind($id));
    }

    //我的咨询
    public function action_myquestion()
    {

        $pageSize = 10;
        $questype = 0; //产品问答

        $page = intval(Arr::get($_GET, 'p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action()
        );

        $out = Model_Question::question_list($this->mid, $questype, $page, $pageSize);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->display('member/myquestion');

    }

    //我的游记
    public function action_mynotes()
    {
        $pageSize = 10;

        $page = intval(Arr::get($_GET, 'p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action()
        );

        $out = Model_Notes::member_notes_list($this->mid, $page, $pageSize);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->display('member/mynotes');
    }

    //我的积分
    public function action_jifen()
    {

        $pageSize = 10;
        $userInfo = Model_Member::get_member_byid($this->mid);

        $page = intval(Arr::get($_GET, 'p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action()
        );

        $out = Model_Member_Jifen_Log::log_list($this->mid, $page, $pageSize);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
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
            $nextGrade = null;
        }
        else
        {
            $nextArr = $grade['grade'][$grade['current']];
            $nextGrade = array('poor' => $nextArr['begin'] - $grade['jifen'], 'name' => $nextArr['name']);
        }
        //配置访问地址 当前控制器方法
        $userinfo['litpic'] = empty($userinfo['litpic']) ? Common::member_nopic() : $userinfo['litpic'];
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('userinfo', $userInfo);
        $this->assign('grade', $grade);
        $this->assign('nextGrade', $nextGrade);
        $this->display('member/jifen');

    }

    //修改密码
    public function action_modify_pwd()
    {
        $info = Model_Member::get_member_byid($this->mid);
        $token = md5(time());
        Common::session('crsf_code', $token);
        $this->assign('frmcode', $token);
        $this->assign('info', $info);
        $this->display('member/modify_pwd');
    }

    //执行修改密码
    public function action_do_changepwd()
    {
        $newpwd1 = Arr::get($_POST, 'newpwd1');
        $newpwd2 = Arr::get($_POST, 'newpwd2');
        $frmCode = Arr::get($_POST, 'frmcode');
        $setpwd = Arr::get($_POST, 'setpwd');
        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if ($orgCode != $frmCode)
        {
            exit('error token');
        }
        if ($newpwd1 != $newpwd2)
        {
            exit('error pwd not match');
        }

        $m = ORM::factory('member', $this->mid);
        $m->pwd = md5($newpwd1);
        $m->save();
        if ($m->saved())
        {
            $title = empty($setpwd) ? urlencode('修改密码') : urlencode('设置密码');
            $msg = empty($setpwd) ? urlencode('修改密码成功') : urlencode('设置密码成功');
            $this->request->redirect('member/index/msg?title=' . $title . "&msg=" . $msg);
        }
        else
        {
            Common::message(array('msg' => '修改密码失败', 'jumpUrl' => $this->refer_url));
        }

    }


    /*
     * 修改/绑定手机
     * */
    public function action_modify_phone()
    {
        $change = Arr::get($_GET, 'change');
        $token = md5(time());
        Common::session('crsf_code', $token);
        $this->assign('change', $change);
        $this->assign('frmcode', $token);
        $this->display('member/modify_phone');
    }

    //执行修改手机号
    public function action_do_modify_phone()
    {
        $msgCode = Arr::get($_POST, 'msgcode');
        $frmCode = Arr::get($_POST, 'frmcode');

        $mobile = Arr::get($_POST, 'mobile');

        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if ($orgCode != $frmCode)
        {
            exit('error token');
        }
        if (Common::session('mobilecode_' . $mobile) != $msgCode)
        {
            exit('error msgcode');
        }
        Common::session('mobilecode_' . $mobile, null);
        //检测手机是否重复
        if (Model_Member::check_member_exist($mobile))
        {
            exit('error mobile repeat');
        }
        $m = ORM::factory('member', $this->mid);
        $old_mobile = $m->mobile;
        $m->mobile = $mobile;
        $m->save();
        if ($m->saved())
        {
            if(empty($old_mobile))
            {
                $jifen = Model_Jifen::reward_jifen('sys_member_bind_phone',$this->mid);
                if(!empty($jifen))
                {
                    St_Product::add_jifen_log($this->mid,'绑定手机送积分'.$jifen,$jifen,2);
                }
            }
            $title = urlencode('修改/绑定手机');
            $msg = urlencode(__('user_modify_phone_ok'));
            $this->request->redirect('member/index/msg?title=' . $title . "&msg=" . $msg);

        }
        else
        {
            Common::message(array('msg' => __('user_modify_phone_failure'), 'jumpUrl' => $this->refer_url, 'status' => 1));
        }


    }


    /*
     * 修改/绑定邮箱
     * */
    public function action_modify_email()
    {
        $change = Arr::get($_GET, 'change');
        $token = md5(time());
        Common::session('crsf_code', $token);
        $this->assign('change', $change);
        $this->assign('frmcode', $token);
        $this->display('member/modify_email');
    }

    //执行修改邮箱
    public function action_do_modify_email()
    {
        $emailCode = Arr::get($_POST, 'emailcode');
        $frmCode = Arr::get($_POST, 'frmcode');

        $email = Arr::get($_POST, 'email');

        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if ($orgCode != $frmCode)
        {
            exit('error token');
        }
        if (Common::session('emailcode_' . md5($email)) != $emailCode)
        {
            exit('error emailCode');
        }
        Common::session('emailcode_' . $email, null);
        //检测手机是否重复
        if (Model_Member::check_member_exist($email))
        {
            exit('error email repeat');
        }
        $m = ORM::factory('member', $this->mid);
        $old_email = $m->email;
        $m->email = $email;
        $m->save();
        if ($m->saved())
        {
            if(empty($old_email))
            {
                $jifen = Model_Jifen::reward_jifen('sys_member_bind_email',$this->mid);
                if(!empty($jifen))
                {
                    St_Product::add_jifen_log($this->mid,'绑定邮箱送积分'.$jifen,$jifen,2);
                }
            }
            $title = urlencode('修改/绑定邮箱');
            $msg = urlencode(__('user_modify_email_ok'));
            $this->request->redirect('member/index/msg?title=' . $title . "&msg=" . $msg);

        }
        else
        {
            Common::message(array('msg' => __('user_modify_email_failure'), 'jumpUrl' => $this->refer_url, 'status' => 1));
        }


    }


    //常用联系人
    public function action_linkman()
    {
        $this->display('member/linkman');
    }

    //保存常用联系人
    public function action_ajax_do_save_linkman()
    {
        $t_name = Common::remove_xss(Arr::get($_POST, 't_name'));
        $t_cardtype = Common::remove_xss(Arr::get($_POST, 't_cardtype'));
        $t_cardno = Common::remove_xss(Arr::get($_POST, 't_cardno'));
        $tourer = array();
        $total = count($t_name);
        for ($i = 1; $i <= $total; $i++)
        {
            $tourer[] = array(
                'name' => $t_name[$i],
                'cardtype' => $t_cardtype[$i],
                'cardno' => $t_cardno[$i]
            );
        }
        //先删除

        $sql = "DELETE FROM `sline_member_linkman` WHERE memberid='" . $this->mid . "'";
        DB::query(Database::DELETE, $sql)->execute();
        if (count($tourer) > 0 && !empty($tourer[0]['name']))
        {

            foreach ($tourer as $t)
            {

                $m = ORM::factory('member_linkman');
                $m->memberid = $this->mid;
                $m->linkman = $t['name'];
                $m->idcard = $t['cardno'];
                $m->cardtype = $t['cardtype'];
                $m->save();
                $m->clear();
            }


        }
        echo json_encode(array('status' => 1));


    }

    //删除联系人
    public function action_ajax_do_del_linkman()
    {
        $linkman = Arr::get($_POST, 'linkman');
        $code = Arr::get($_POST, 'code');
        $sql = "delete  from sline_member_linkman where linkman='$linkman' and idcard='$code'";
        $data = DB::query(Database::DELETE, $sql)->execute();
        if ($data)
        {
            echo json_encode(array('status' => 1));
        }
        else
        {
            echo json_encode(array('status' => 0));
        }
    }

    //cardno检测重复性
    public function action_ajax_check_cardno()
    {
        $cardno = Common::remove_xss(Arr::get($_POST, 'cardno'));
        $cardtype = Common::remove_xss(Arr::get($_POST, 'cardtype'));
        $cardid = Common::remove_xss(Arr::get($_POST, 'cardid'));
        $mid = $this->mid;
        $flag = 'false';
        $where = "cardtype='$cardtype' and idcard='$cardno' and memberid='$mid'";
        $where .= !empty($cardid) ? " and id!=$cardid" : '';

        $m = ORM::factory('member_linkman')
            ->where($where)
            ->find();
        if (!$m->loaded())
        {
            $flag = 'true';
        }
        echo $flag;
    }


    //消息提示
    public function action_msg()
    {
        $title = St_Filter::remove_xss(Arr::get($_GET, 'title'));
        $msg = St_Filter::remove_xss(Arr::get($_GET, 'msg'));
        $this->assign('title', $title);
        $this->assign('msg', $msg);
        $this->assign('time', 5);
        $this->display('member/msg');
    }

    //付款
    public function action_pay()
    {
        $orderSn = Common::remove_xss(Arr::get($_GET, 'ordersn'));
        Common::session('_platform', 'pc');
        $payurl = Common::get_main_host() . "/payment/?ordersn=" . $orderSn;
        $this->request->redirect($payurl);
    }


    /*
     * ajax 上传用户头像
     * */

    public function action_ajax_uploadface()
    {

        if(!St_Functions::is_valid_image($_FILES['Filedata']))
        {
            return false;
        }
        if (!$_FILES['Filedata'])
        {
            die ('Image data not detected!');
        }
        if ($_FILES['Filedata']['error'] > 0)
        {
            switch ($_FILES ['Filedata'] ['error'])
            {
                case 1 :
                    $error_log = 'The file is bigger than this PHP installation allows';
                    break;
                case 2 :
                    $error_log = 'The file is bigger than this form allows';
                    break;
                case 3 :
                    $error_log = 'Only part of the file was uploaded';
                    break;
                case 4 :
                    $error_log = 'No file was uploaded';
                    break;
                default :
                    break;
            }
            die ('upload error:' . $error_log);
        }
        else
        {
            $img_data = $_FILES['Filedata']['tmp_name'];
            $size = getimagesize($img_data);
            $file_type = $size['mime'];
            if (!in_array($file_type, array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif')))
            {
                $error_log = 'only allow jpg,png,gif';
                die ('upload error:' . $error_log);
            }
            switch ($file_type)
            {
                case 'image/jpg' :
                case 'image/jpeg' :
                case 'image/pjpeg' :
                    $extension = 'jpg';
                    break;
                case 'image/png' :
                    $extension = 'png';
                    break;
                case 'image/gif' :
                    $extension = 'gif';
                    break;
            }
        }
        if (!is_file($img_data))
        {
            die ('Image upload error!');
        }
        //图片保存路径,默认保存在该代码所在目录(可根据实际需求修改保存路径)
        $save_path = BASEPATH;
        $file_dir = UPLOADPATH . '/member/';
        if (!is_dir($file_dir)) mkdir($file_dir);
        $uinqid = uniqid();
        $litpic = '/uploads/member/' . $uinqid . '.' . $extension;
        $filename = $save_path . $litpic;
        move_uploaded_file($img_data, $filename);
        echo $litpic;
        exit ();
    }

    //保存用户基本信息

    public function action_ajax_userinfo_save()
    {


        $frmCode = Common::remove_xss(Arr::get($_POST, 'frmcode'));
        $nickName = Common::remove_xss(Arr::get($_POST, 'nickname'));


        $address = Common::remove_xss(Arr::get($_POST, 'address'));
        $wechat = Common::remove_xss(Arr::get($_POST, 'wechat'));
        $native_place =Common::remove_xss(Arr::get($_POST, 'native_place'));
        $mid = intval(Arr::get($_POST, 'mid'));
        $litpic =Common::remove_xss(Arr::get($_POST, 'litpic')) ;
        $signature = Common::remove_xss(Arr::get($_POST, 'signature'));
        $qq = Common::remove_xss(Arr::get($_POST, 'qq'));
        $qq = $qq ? intval($qq) : '';
        $birth_date = Common::remove_xss(Arr::get($_POST, 'birth_date'));
        $sex = Common::remove_xss(Arr::get($_POST, 'sex'));
        $constellation = Arr::get($_POST, 'constellation');
        $constellation = in_array($constellation, $this->_constellation()) ? $constellation : '';
        $out = array();
        //安全校验码验证
        $orgCode = Common::session('crsf_usr_code');
        if ($orgCode != $frmCode)
        {
            $out['msg'] = '安全检验码错误';
            $out['status'] = 0;
            echo json_encode($out);
            exit;
        }
        $m = ORM::factory('member', $mid);
        $old_litpic = $m->litpic;
        if ($m->loaded())
        {
            $m->nickname = $nickName;
            $m->wechat = $wechat;
            $m->native_place = $native_place;

            $m->address = $address;
            $m->litpic = $litpic;
            $m->constellation = $constellation;
            $m->qq = $qq;
            $m->signature = $signature;
            if($m->verifystatus!=2)
            {
                $m->birth_date = $birth_date;
                $m->sex = $sex;
            }

            $m->save();
            if ($m->saved())
            {
                if($old_litpic!=$litpic && !empty($litpic))
                {
                    $jifen = Model_Jifen::reward_jifen('sys_member_upload_head',$this->mid);
                    if(!empty($jifen))
                    {
                        St_Product::add_jifen_log($this->mid,"上传头像送积分{$jifen}",$jifen,2);
                    }
                }
                Cookie::set('st_username', $nickName);
                $out['msg'] = '';
                $out['status'] = 1;
                echo json_encode($out);
                exit;
            }
        }
        else
        {
            $out['msg'] = '会员信息不正确';
            $out['status'] = 0;
            echo json_encode($out);
            exit;
        }

    }

    /*ajax检测老密码是否正确*/
    public function action_ajax_check_oldpwd()
    {
        $flag = 'false';
        $oldpwd = Arr::get($_POST, 'oldpwd');
        $userinfo = Model_Member::get_member_byid($this->mid);
        if (md5($oldpwd) == $userinfo['pwd'])
        {
            $flag = 'true';
        }
        echo $flag;
    }

    /**
     * 发送短信验证码
     */


    /*修改密码短消息*/
    public function action_ajax_send_msgcode()
    {

        $mobile = Arr::get($_POST, 'mobile');//手机
        $token = Arr::get($_POST, 'token');//
        $curtime = time();

        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if ($orgCode != $token)
        {
            echo json_encode(array('status' => false, 'msg' => '检验码错误'));
            exit;
        }

        //手机号验证
        if (empty($mobile))
        {
            echo json_encode(array('status' => false, 'msg' => '手机号不能为空'));
            exit;
        }
        else
        {

            $code = Common::get_rand_code(5);//验证码
            $flag = json_decode(St_SMSService::send_member_msg($mobile,NoticeCommon::MEMBER_REG_CODE_MSGTAG,"","",$code));
            if ($flag->Success)//发送成功
            {
                Common::session('mobilecode_' . $mobile, $code);
                echo json_encode(array('status' => true, 'msg' => '验证码发送成功'));
            }
            else
            {
                echo json_encode(array('status' => false, 'msg' => $flag->Message . '导致发送失败'));
            }

        }

    }

    /*
    * 发送邮箱验证码
    * */
    public function action_ajax_send_emailcode()
    {

        $email = Common::remove_xss(Arr::get($_POST, 'email'));
        $token = Common::remove_xss(Arr::get($_POST, 'token'));//


        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if ($orgCode != $token)
        {
            echo json_encode(array('status' => false, 'msg' => '检验码错误'));
            exit;
        }


        $code = Common::get_rand_code(5);//验证码
        if (empty($email))
        {
            echo json_encode(array('status' => true, 'msg' => '邮箱不能为空'));
            exit;
        }
        $title = $GLOBALS['cfg_webname'] . '邮箱验证码';
        $emailInfo = Product::get_email_msg_config('reg_msgcode');
        $content = $emailInfo['msg'];
        $content = str_replace('{#CODE#}', $code, $content);
        $content = str_replace('{#EMAIL#}', $email, $content);
        $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
        $status = Product::order_email($email, $title, $content);
        if ($status)
        {

            Common::session('emailcode_' . md5($email), $code);
            echo json_encode(array('status' => true, 'msg' => '发送邮箱验证码成功'));
        }
        else
        {
            echo json_encode(array('status' => false, 'msg' => '发送邮箱验证码失败'));
        }

    }

    /**
     * 检测email验证码是否正确.
     */
    public function action_ajax_check_email_code()
    {
        $emailCode = Arr::get($_POST, 'emailcode');

        $email = Arr::get($_POST, 'email');
        $flag = 'false';
        if (Common::session('emailcode_' . md5($email)) == $emailCode)
        {
            $flag = 'true';
            //Common::session('emailcode_'.md5($email),null);

        }
        echo $flag;


    }


    //删除游记
    public function action_ajax_delete_notes()
    {
        if (!$this->request->is_ajax()) exit;
        $noteid = intval(Arr::get($_POST, 'noteid'));
        $mid = $this->mid;
        $sql = "DELETE FROM `sline_notes` WHERE memberid='$mid' AND id='$noteid'";
        $flag = DB::query(Database::DELETE, $sql)->execute();
        $status = 0;
        if ($flag)
        {
            $status = 1;
        }
        echo json_encode(array('status' => $status));
    }

    //我的结伴
    public function action_myjieban()
    {
        $pageSize = 10;

        $page = intval(Arr::get($_GET, 'p'));
        $flag = Arr::get($_GET, 'flag') ? Arr::get($_GET, 'flag') : 'self';
        if (!in_array($flag, array('join', 'self')))
        {
            $this->request->redirect($this->refer_url . '/error/404', "404");
            return;
        }
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action()
        );
        $out = Model_Jieban::member_jieban_list($this->mid, $page, $pageSize, $flag);
        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('flag', $flag);
        // var_dump($out);exit;
        $this->assign('list', $out['list']);
        $this->display('member/myjieban');
    }

    /**
     * 会员中心关闭结伴
     */
    public static function action_ajax_close()
    {

        $id = intval(Arr::get($_POST, 'id'));

        $sql = "update sline_jieban set status=2 where id=$id";
        $flag = DB::query(Database::UPDATE, $sql)->execute();
        $status = 0;
        if ($flag)
        {
            $status = 1;
        }
        echo json_encode(array('status' => $status));

    }


    //我的地址
    public function action_address()
    {
        //收件地址列表
        $address_list = Model_Member_Address::get_address($this->mid);
        $this->assign('address_list', $address_list);
        $this->display('member/address');

    }

    //保存地址
    public function action_ajax_save_address()
    {
        if ($this->request->is_ajax())
        {
            //最多存储20条记录
            $num = DB::select('id')->from('member_address')->where('memberid', '=', $this->mid)->execute()->count();
            if ($num <= 19)
            {
                $data = array(
                    'memberid' => $this->mid,
                    'province' => Common::remove_xss($_POST['area_prov']),
                    'city' => Common::remove_xss($_POST['area_city']),
                    'address' => Common::remove_xss($_POST['address']),
                    'phone' => Common::remove_xss($_POST['phone']),
                    'postcode' => Common::remove_xss($_POST['postcode']),
                    'receiver' => Common::remove_xss($_POST['receiver']),
                    'is_default' => intval($_POST['is_default']) ? intval($_POST['is_default']) : 0
                );
                list($id, $flag) = DB::insert('member_address', array_keys($data))->values(array_values($data))->execute();
                //是否将当前地址设置为默认地址
                if ($flag && $data['is_default'])
                {
                    DB::update('member_address')->set(array('is_default' => 0))->where('memberid', '=', $this->mid)->and_where('id', '!=', $id)->execute();
                }
            }
            else
            {
                $flag = 0;

            }

            echo json_encode(array('status' => $flag));

        }


    }

    //修改保存
    public function action_ajax_modify_save_address()
    {
        if ($this->request->is_ajax())
        {
            $data = array();
            $prov = Common::remove_xss($_POST['m_area_prov']);
            $city = Common::remove_xss($_POST['m_area_city']);
            $address = Common::remove_xss($_POST['m_address']);
            $phone = Common::remove_xss($_POST['m_phone']);
            $postcode = Common::remove_xss($_POST['m_postcode']);
            $receiver = Common::remove_xss($_POST['m_receiver']);
            $is_default = intval($_POST['m_is_default']);
            if ($prov)
            {
                $data['province'] = $prov;
            }
            if ($city)
            {
                $data['city'] = $city;
            }
            if ($address)
            {
                $data['address'] = $address;
            }
            if ($phone)
            {
                $data['phone'] = $phone;
            }
            if ($postcode)
            {
                $data['postcode'] = $postcode;
            }
            if ($receiver)
            {
                $data['receiver'] = $receiver;
            }


            $data['is_default'] = $is_default;
            $address_id = intval($_POST['m_address_id']);
            $flag = DB::update('member_address')
                ->set($data)
                ->where('memberid', '=', $this->mid)
                ->and_where('id', '=', $address_id)
                ->execute();
            if ($flag && $data['is_default'] == 1)
            {
                DB::update('member_address')
                    ->set(array('is_default' => 0))
                    ->where('memberid', '=', $this->mid)
                    ->and_where('id', '!=', $address_id)
                    ->execute();
            }
            echo json_encode(array('status' => $flag));
        }
    }

    //前台编辑地址
    public function action_ajax_front_modify_save_address()
    {

        if ($this->request->is_ajax())
        {
            $data = array();
            $prov = Common::remove_xss($_POST['m_area_prov']);
            $city = Common::remove_xss($_POST['m_area_city']);
            $address = Common::remove_xss($_POST['m_address']);
            $phone = Common::remove_xss($_POST['m_phone']);
            $postcode = Common::remove_xss($_POST['m_postcode']);
            $receiver = Common::remove_xss($_POST['m_receiver']);
            $is_default = intval($_POST['m_is_default']);
            if ($prov)
            {
                $data['province'] = $prov;
            }
            if ($city)
            {
                $data['city'] = $city;
            }
            if ($address)
            {
                $data['address'] = $address;
            }
            if ($phone)
            {
                $data['phone'] = $phone;
            }
            if ($postcode)
            {
                $data['postcode'] = $postcode;
            }
            if ($receiver)
            {
                $data['receiver'] = $receiver;
            }


            $data['is_default'] = $is_default;
            $address_id = intval($_POST['m_address_id']);
            if (empty($address_id))
            {
                //最多存储20条记录
                $num = DB::select('id')->from('member_address')->where('memberid', '=', $this->mid)->execute()->count();
                if ($num <= 20)
                {
                    $data['memberid'] = $this->mid;
                    $status = DB::insert('member_address', array_keys($data))->values(array_values($data))->execute();
                    $new_address_id = $status[0];
                    $data['id'] = $new_address_id;
                    $flag = $status[0];
                }
                else
                {
                    $flag = 0;
                }

            }
            else
            {
                $flag = DB::update('member_address')
                    ->set($data)
                    ->where('memberid', '=', $this->mid)
                    ->and_where('id', '=', $address_id)
                    ->execute();

            }
            if ($flag && $data['is_default'] == 1)
            {
                DB::update('member_address')
                    ->set(array('is_default' => 0))
                    ->where('memberid', '=', $this->mid)
                    ->and_where('id', '!=', $address_id)
                    ->execute();
                $data = Model_Member_Address::get_address_info($address_id);
            }

            echo json_encode(array('status' => $flag, 'json' => json_encode($data), 'insert_id' => $new_address_id));
        }


    }

    /**
     * 删除地址
     */
    public function action_ajax_del_address()
    {
        if ($this->request->is_ajax())
        {
            $address_id = intval($_POST['address_id']);
            if ($address_id)
            {
                $flag = 0;
                $flag = DB::delete('member_address')
                    ->where('id', '=', $address_id)
                    ->and_where('memberid', '=', $this->mid)
                    ->execute();
                echo json_encode(array('status' => $flag));
            }
        }


    }


    /**
     * @function 实名认证
     */
    public function action_modify_idcard()
    {
        $info = DB::select('truename','cardid','verifystatus','idcard_pic')->from('member')->where('mid','=',$this->mid)->execute()->current();
        $info['idcard_pic'] = (array)json_decode($info['idcard_pic']);
        $this->assign('info',$info);
        $is_update = Arr::get($_GET,'is_update') ? Arr::get($_GET,'is_update') : 0;
        $this->assign('is_update',$is_update);
        $this->display('member/modify_idcard');
    }

    public function action_do_modify_idcard()
    {
        $truename = Common::remove_xss(Arr::get($_POST,'truename'));
        $cardid = Common::remove_xss(Arr::get($_POST,'cardid'));
        $front_pic = Common::remove_xss(Arr::get($_POST,'front_pic'));
        $verso_pic = Common::remove_xss(Arr::get($_POST,'verso_pic'));
        if(!$truename||!$cardid||!$front_pic||!$verso_pic)
        {
            Common::message(array('msg' => __('提交资料失败'), 'jumpUrl' => $this->refer_url, 'status' => 1));
        }
        $m = ORM::factory('member', $this->mid);
        $m->truename=$truename;
        $m->cardid=$cardid;
        $m->verifystatus=1;
        $m->idcard_pic = json_encode(array('front_pic'=>$front_pic,'verso_pic'=>$verso_pic));
        $m->save();
        if ($m->saved())
        {
            $title = urlencode('提交资料成功,等待管理员审核');
            $msg = '提交资料成功,等待管理员审核';
            $this->request->redirect($GLOBALS['cfg_basehost'].'/member/index/msg?title=' . $title . "&msg=" . $msg);
        }
        else
        {
            Common::message(array('msg' => __('提交资料失败'), 'jumpUrl' => $this->refer_url, 'status' => 1));
        }

    }

    /**
     * 上传图片
     */
    public function action_ajax_upload_picture()
    {
        //if(!$this->request->is_ajax())exit;
        $filedata = Arr::get($_FILES, 'filedata');
        $storepath = UPLOADPATH . '/member/';
        if (!file_exists($storepath))
        {
            mkdir($storepath);
        }
        $filename = uniqid();
        $out = array();
        $ext = end(explode('.', $filedata['name']));

        if (move_uploaded_file($filedata['tmp_name'], $storepath . $filename . '.' . $ext))
        {
            $out['status'] = 1;
            $out['litpic'] = '/uploads/member/' . $filename . '.' . $ext;
        }
        echo json_encode($out);
    }


}
