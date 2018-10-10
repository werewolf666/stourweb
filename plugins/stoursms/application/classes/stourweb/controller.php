<?php

/**
 * User: Netman
 * Date: 14-3-27
 * Time: 下午9:53
 */
class Stourweb_Controller extends Controller
{

    // 用户数据赋值
    public $_data = array();
    public $params = array();

    /*
     * before
     */
    public function before()
    {
        $params = $this->request->param('params');
        $this->params = $this->analyze_param($params);
        $this->assign('webname', Common::get_sys_para('cfg_webname'));
        $this->assign('admindir', Common::get_sys_para('cfg_admin_dirname'));
        $this->assign('cmsurl', URL::site());

        Common::addLog($this->request->controller(), $this->request->action(), $this->params['action']);
    }

	protected function validate_login()
	{
        if ($this->request->is_ajax() && Common::checkLogin(Cookie::get('username')))
            return;

        $session = Session::instance();
        $session_username = $session->get('username');
        $cookie_username = Cookie::get('username');
        $uploadcookie = Arr::get($_POST, 'uploadcookie');

        $serectkey = !empty($cookie_username) ? $cookie_username : $session_username;
        $serectkey = !empty($serectkey) ? $serectkey : $uploadcookie;

        if (isset($serectkey))
        {
            $result = Common::checkLogin($serectkey);
            if (!$result)
            {
                $session = Session::instance();
                $session->delete('username');
                $session->delete('userid');
                $session->delete('roleid');
                Cookie::delete('username');
                header("location:/" . Common::get_sys_para('cfg_admin_dirname'));
				exit;
            } else
            {
                $session = Session::instance();
                $serectkey = Common::authcode($result['username'] . '||' . $result['password'], '');
                $session->set('username', $serectkey);
                $session->set('userid', $result['id']);
                $session->set('roleid', $result['roleid']);
                Cookie::set('username', $serectkey);
                $rolemodule = ORM::factory('role_module')->where("roleid='{$result[0]['roleid']}'")->as_array();
                $session->set('rolemodule', $rolemodule);
            }

        } else
        {
            $session = Session::instance();
            $session->delete('username');
            $session->delete('userid');
            $session->delete('roleid');
            Cookie::delete('username');
            header("location:/" . Common::get_sys_para('cfg_admin_dirname'));
			exit;
        }
    }

    /*
     * 显示模板
     * @param string $tpl,模板名
     * */
    public function display($tpl)
    {


        $file = $GLOBALS['cfg_templet'] . $tpl;

        if (!file_exists(APPPATH . '/views/' . $GLOBALS['cfg_templet'] . '/' . $tpl . '.php'))
        {
            $file = 'default/' . $tpl;
        }
        //$tpl = !empty($GLOBALS['cfg_templet']) ? $GLOBALS['cfg_templet'].'/'.$tpl : $tpl;//是否定义默认模板判断.

        $view = Stourweb_View::factory($file);

        foreach ($this->_data as $key => $value)
        {
            $view->set($key, $value);
        }

        $this->response->body($view->render());


    }

    /*
     * 模板赋值,控制器赋值
     * @param string $key
     * @param string $value
     * */
    public function assign($key, $value)
    {

        $this->_data[$key] = $value;

    }

    /*
  * 变量值分析器
  * @param string $param
  * */
    public function analyze_param($param)
    {

        $arr = explode('/', $param);

        $out = array();
        for ($i = 0; isset($arr[$i]); $i = $i + 1)
        {
            if ($i % 2 == 0)
            {
                $key = Common::remove_xss($arr[$i]);
                $value = Common::remove_xss(isset($arr[$i + 1]) ? $arr[$i + 1] : 0);
                $out[$key] = $value;
            }

        }
        return $out;

    }


} 