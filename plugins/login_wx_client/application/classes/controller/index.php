<?php

/**
 * 微信快速登陆
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
            $user['unionid'] = $author['unionid'];
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
                    //整理多余用户
                    $richUser = DB::select()->from('member_third')->where('openid', '=', $author['unionid'])->and_where('id', '<>', $rs['id'])->execute()->current();
                    if ($richUser)
                    {
                        DB::delete('member_third')->where('id', '=', $richUser['id'])->execute();
                        DB::delete('member')->where('mid', '=',  $richUser['mid'])->execute();
                        DB::update('member_order')->set(array('memberid' => $rs['mid']))->where('memberid', '=', $richUser['mid'])->execute();
                    }
                }
                if (!empty($rs['nickname']))
                {
                    unset($user['nickname']);
                }
                Common::write_login_info($rs);
                $this->request->redirect(Cookie::get('_refer'));
            }
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

    /**
     * 配置第三方登陆信息
     */
    private function _conf()
    {
        $arr = DB::select()->from('sysconfig')->where("varname in('cfg_wx_client_appkey','cfg_wx_client_appsecret')")->execute()->as_array();
        $info = array(
            "callback" => Common::get_main_host() . "/plugins/login_wx_client/index/back/"
        );
        foreach ($arr as $v)
        {
            if ($v['varname'] == 'cfg_wx_client_appkey')
            {
                $info['appid'] = $v['value'];
            }
            if ($v['varname'] == 'cfg_wx_client_appsecret')
            {
                $info['appkey'] = $v['value'];
            }
        }
        $this->_conf = $info;
    }
}