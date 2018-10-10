<?php

/**
 * 微信登陆
 * Class Controller_Index
 */
class Controller_Index extends Stourweb_Controller
{
    private $_api;
    private $_conf = null;

    //初始化设置
    public function before()
    {
        parent::before();
        $this->_conf();
        $this->_api = VENDORPATH . 'api/';
        require $this->_api . 'weixin.class.php';
    }

    //首页
    public function action_index()
    {
        if (isset($_GET['refer']))
        {
            Cookie::set('_refer', $_GET['refer']);
        }
        else
        {
            die();
        }
        $author = new weixinPHP($this->_conf['appid'], $this->_conf['appkey'], $this->_conf['callback'], time());
        $url = $author->login_url();
        header("Location:{$url}");
        exit;
    }

    //第三方回调
    public function action_back()
    {
        $user = array();
        $code = $_GET['code'];
        $author = new weixinPHP($this->_conf['appid'], $this->_conf['appkey'], $this->_conf['callback'], time());
        $tokenarr = $author->access_token($code);
        $token = $tokenarr['access_token'];
        $openid = $tokenarr['openid'];
        $author = $author->get_user_info($openid, $token);

        if (isset($author['openid']))
        {
			$author['unionid'] = $author['unionid'] ? $author['unionid'] : $author['openid'];
            $user['openid'] = $author['unionid'];
            $user['nickname'] = $author['nickname'];
            $user['litpic'] = $author['headimgurl'];
            $user['from'] = 'weixin';
            //登陆状态
            $member = Common::islogin();
            if (!empty($member))
            {
                $user['mid'] = $member['mid'];
                Common::third_login_bind($user);
            }
            $rs = Db::query(Database::SELECT,'(select * from `sline_member_third` where openid="' . $author['openid'] . '") union (select * from `sline_member_third` where openid="' . $author['unionid'] . '")')->execute()->current();
            if (!empty($rs))
            {
                if ($rs['openid'] != $author['unionid'])
                {
                    //更改openId
                    DB::update('member_third')->set(array('openid' => $author['unionid']))->where('id', '=', $rs['id'])->execute();
                    //多余用户
                    $richUser = DB::select()->from('member_third')->where('openid', '=', $author['unionid'])->and_where('id', '<>', $rs['id'])->execute()->current();
                    if ($richUser)
                    {
                        //删除多余账号
                        DB::delete('member_third')->where('id', '=', $rs['id'])->execute();
                        DB::delete('member')->where('mid', '=', $rs['mid'])->execute();
                        //并合订单
                        DB::update('member_order')->set(array('memberid' => $richUser['mid']))->where('memberid', '=', $rs['mid'])->execute();
                    }
                }
                if (!empty($rs['nickname']))
                {
                    unset($user['nickname']);
                }
                Common::write_login_info($rs);
                $this->request->redirect(Cookie::get('_refer'));
            }

            $cfg_third_login_bind = Model_Sysconfig::get_configs(0,'cfg_third_login_bind',true);
            if($cfg_third_login_bind==1)
            {
                $result = Common::third_bind(array('third'=>$user));
                if($result['bool'])
                {
                    $this->request->redirect(Cookie::get('_refer'));
                    return;
                }

            }
            if($cfg_third_login_bind==2)
            {
                $this->both_bind($user);
                return;
            }

            $view = View::factory('default/' . Common::st_platform());
            $view->user = $user;
            $content = Common::head_bottom();
            $view = str_replace(array('<stourweb_title/>', '<stourweb_content/>', '<stourweb_header/>'), array('绑定账号', $view->render(), ''), $content);
            $this->response->body($view);



            $view = View::factory('default/' . Common::st_platform());
            $view->user = $user;
            $content = Common::head_bottom();
            $view = str_replace(array('<stourweb_title/>', '<stourweb_content/>', '<stourweb_header/>'), array('绑定账号', $view->render(), ''), $content);
            $this->response->body($view);
        }
        else
        {
            //跳转至登陆也
            $this->request->redirect(Common::get_main_host() . '/member/login');
        }
    }

    //绑定账号
    public function action_bind()
    {
        echo Common::third_bind($_POST);
    }

    //直接绑定
    public function both_bind($user)
    {
        $view = View::factory('default/' . Common::st_platform().'_both');
        $view->user = $user;
        $content = Common::head_bottom();
        $view = str_replace(array('<stourweb_title/>', '<stourweb_content/>', '<stourweb_header/>'), array('绑定账号', $view->render(), ''), $content);
        $this->response->body($view);
    }

    //发送验证码
    public function action_ajax_send_code()
    {
        $session_ins = Session::instance();

        $account = $_POST['account'];
        if (empty($account))
        {
            echo json_encode(array('Success' => false, 'Message' => '请录入发送号码'));
            exit;
        }

        $checkcode_img = $_POST['checkcode_img']; //图片验证码
        //验证码验证
        if (!Captcha::valid($checkcode_img) || empty($checkcode_img))
        {
            echo json_encode(array('Success' => false, 'Message' => '图片验证码错误'));
            exit;
        }
        $session_ins->set('captcha_response', '');

        $last_send_time = $session_ins->get('time_' . $account);
        $last_send_time = empty($last_send_time) ? 0 : $last_send_time;
        $curtime = time();

        if ($curtime - $last_send_time <= 60)
        {
            echo json_encode(array('Success' => false, 'Message' => '发送过于频繁，请稍后再试'));
            exit;
        }


        $checkcode = mt_rand(1000, 9999);
        $msg = "尊敬的用户，你的验证码是：" . $checkcode;
        $result = "";
        if (strpos($account, '@') != false)
        {
            $email_result = St_EmailService::send_email($account, '用户绑定验证码', $msg);
            $result = $email_result ? array('Success' => true) : array('Success' => false, 'Message' => '发送失败，请重试');
            $result = json_encode($result);
        } else
        {
            $result = St_SMSService::send_msg($account, '', $msg);
        }

        $result_arr = json_decode($result, true);
        if ($result_arr['Success'])
        {
            $session_ins->set('code_' . $account, $checkcode);
            $session_ins->set('time_' . $account, $curtime);
        }

        echo $result;
    }

    //验证验证码
    public function action_ajax_check_code()
    {
        $account = $_POST['account'];
        $checkcode = $_POST['checkcode'];
        $org_code = Session::instance()->get('code_'.$account);
        if($org_code==$checkcode && !empty($org_code))
        {
            echo json_encode(array('status'=>true));
        }
        else
        {
            echo json_encode(array('status'=>false));
        }
    }

    //绑定提交
    public function action_ajax_both_save()
    {
        $account = $_POST['account'];
        $checkcode = $_POST['checkcode'];
        $third = $_POST['third'];
        $session_obj = Session::instance();
        $code_key = 'code_'.$account;
        $org_code = $session_obj->get($code_key);
        if($org_code!=$checkcode && empty($org_code))
        {
            echo json_encode(array('bool'=>false,'msg'=>'验证码错误'));
            return;
        }
        $session_obj->delete($code_key);
        $account_field = 'mobile';
        if(strpos($account,'@')!=false)
        {
            $account_field = 'email';
        }

        $model = ORM::factory('member')->where($account_field,'=',$account)->find();
        if(!$model->loaded())
        {
            $pwd = mt_rand(100000,999999);
            $model->$account_field = $account;
            $model->pwd = md5($pwd);
            $model->nickname = $third['nickname'];
            $model->litpic = $third['litpic'];
            $model->save();
            $model->reload();
        }

        if($model->loaded())
        {
            $result = array('third'=>$third,'member'=>array('user'=>$account,'pwd'=>$model->pwd,'pwd_coded'=>1));
            echo Common::third_bind($result);
        }
        else
        {
            echo json_encode(array('bool'=>false,'msg'=>'绑定失败'));
        }

    }

    /**
     * 配置第三方登陆信息
     */
    private function _conf()
    {
        $arr = DB::select()->from('sysconfig')->where("varname in('cfg_weixi_appkey','cfg_weixi_appsecret')")->execute()->as_array();
        $info = array(
            "callback" => Common::get_main_host() . "/plugins/login_weixin/index/back/"
        );
        foreach ($arr as $v)
        {
            if ($v['varname'] == 'cfg_weixi_appkey')
            {
                $info['appid'] = $v['value'];
            }
            if ($v['varname'] == 'cfg_weixi_appsecret')
            {
                $info['appkey'] = $v['value'];
            }
        }
        $this->_conf = $info;
    }
}