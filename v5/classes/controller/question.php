<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Question extends Stourweb_Controller
{
    /*
     * 问答控制器
     * */
    private $typeid = 10;
    private $_cache_key = '';

    public function before()
    {
        parent::before();
        //检查缓存
        $this->_cache_key = Common::get_current_url();
        $html = Common::cache('get', $this->_cache_key);
        if (!empty($html))
        {
            echo $html;
            exit;
        }
        $channelname = Model_Nav::get_channel_name($this->typeid);
        $this->assign('typeid', $this->typeid);
        $this->assign('channelname', $channelname);

    }

    //问答首页
    public function action_index()
    {

        //frmcode
        $code = md5(time());
        Common::session('code', $code);
        $userInfo = Product::get_login_user_info();

        $seoinfo = Model_Nav::get_channel_seo($this->typeid);
        $this->assign('seoinfo', $seoinfo);
        $pagesize = 12;
        $questype = 1;//一般问答
        $p = intval(Common::remove_xss(Arr::get($_GET, 'p')));
        $p = !empty($p) ? $p : 1;
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action()

        );
        $out = Model_Question::search_result($p, $pagesize, $questype);
        $pager = Pagination::factory(
            array(

                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pagesize,
                'first_page_in_url' => false,
            )
        );
        //会员信息
        $userInfo = Product::get_login_user_info();
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('currentpage', $p);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('userInfo', $userInfo);
        $this->assign('frmcode', $code);
        $this->assign('userinfo', $userInfo);
        $this->display('question/index');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set', $this->_cache_key, $content);

    }

    //添加问答
    public function action_ajax_add()
    {
        $frmCode = Common::remove_xss(Arr::get($_POST, 'frmcode'));
        $anonymous = intval(Arr::get($_POST, 'anonymous'));
        $nickname = Common::remove_xss(Arr::get($_POST, 'nickname'));
        $mobile = Common::remove_xss(Arr::get($_POST, 'mobile'));
        $email = Common::remove_xss(Arr::get($_POST, 'email'));
        $weixin = Common::remove_xss(Arr::get($_POST, 'weixin'));
        $qq = Common::remove_xss(Arr::get($_POST, 'qq'));
        $quesTitle = Common::remove_xss(Arr::get($_POST, 'questitle'));
        $quesContent = Common::remove_xss(Arr::get($_POST, 'quescontent'));
        $checkCode = Common::remove_xss(Arr::get($_POST, 'checkcode'));
        $userInfo = Product::get_login_user_info();
        //验证码验证
        if (!Captcha::valid($checkCode) || empty($checkCode))
        {
            echo json_encode(array('msg' => '验证码错误', 'status' => 0));
            exit;
        }
        else
        {
            Common::session('captcha_response', '');
        }
        //安全校验码验证
        $orgCode = Common::session('code');
        if ($orgCode != $frmCode)
        {
            echo json_encode(array('msg' => '校验码错误', 'status' => 0));
            exit;
        }
        $m = ORM::factory('question');
        $leaveArr = array();
        $leaveArr['title'] = $quesTitle;
        $leaveArr['content'] = $quesContent;
        $leaveArr['webid'] = $GLOBALS['sys_webid'];
        $leaveArr['phone'] = $mobile;
        $leaveArr['email'] = $email;
        $leaveArr['qq'] = $qq;
        $leaveArr['weixin'] = $weixin;
        $leaveArr['addtime'] = time();
        $leaveArr['nickname'] = $userInfo['nickname'] ? $userInfo['nickname'] : $nickname;
        if ($anonymous > 0)
        {
            $leaveArr['nickname'] = '';
        }
        $leaveArr['nickname'] = empty($leaveArr['nickname']) ? '匿名' : $leaveArr['nickname'];
        $leaveArr['ip'] = Common::get_ip();
        $leaveArr['questype'] = 1;
        $leaveArr['memberid'] = $userInfo['mid'];

        foreach ($leaveArr as $key => $value)
        {
            $m->$key = $value;
        }
        $m->save();
        if ($m->saved())
        {

            $model_info = Model_Model::get_module_info($this->typeid);
            $jifen = Model_Jifen::reward_jifen('sys_write_' . $model_info['pinyin'], $userInfo['mid']);
            if (!empty($jifen))
            {
                St_Product::add_jifen_log($userInfo['mid'], '发布' . $model_info['modulename'] . '送积分' . $jifen, $jifen, 2);
            }
            $msg = __('question_success');
            $status = 1;
        }
        else
        {
            $msg = __('question_failure');
            $status = 0;
        }
        echo json_encode(array('msg' => $msg, 'status' => $status));
        exit;

    }


}