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
    protected $cmsurl;

    /*
     * before
     */
    public function before()
    {
        $this->cmsurl = URL::site();
        //常用变量赋值
        $this->assign('cmsurl', $this->cmsurl);
        $this->assign('webname', $GLOBALS['cfg_webname']);
        $this->assign('defaultimg', $this->cmsurl . 'public/images/grey.gif');
        $params = $this->request->param('params');
        $this->params = $this->analyze_param($params);
        $controller = $this->request->controller();
        $action = $this->request->action();
        //  $_POST = Common::remove_xss($_POST);
        //  $_GET = Common::remove_xss($_GET);
        //  $_COOKIE = Common::remove_xss($_COOKIE);
        // $_REQUEST = Common::remove_xss($_REQUEST);

        Common::before_header($controller, $action);

    }

    /**
     * @function 显示模板
     * @param $tpl
     * @param null $page
     * @throws View_Exception
     */
    public function display($tpl, $page = null,$user_define_tpl=0)
    {
        $file = null;
        if(!$user_define_tpl)
        {
            //上传模板
            if ($page)
            {
                $query = DB::select('id')->from('page')->where('pagename', '=', $page);
                $query = DB::select()->from('m_page_config')->where('pageid', 'in', $query)->and_where('isuse', '=', 1)->limit(1);
                $result = $query->execute()->current();
                if($result['path'])
                {
                    $file = "usertpl/{$result['path']}/index";
                    //赋值用户模板名称,以便在模板上使用
                    $this->assign("user_tpl",$result['path']);
                }
            }
            //系统模板
            if (!file_exists(DOCROOT . $file . EXT))
            {
                //自定义主题
                $file = trim($GLOBALS['cfg_default_templet'], '/') . '/' . $tpl;

                if (!file_exists(APPPATH . $file . EXT))
                {
                    //默认主题
                    $file = 'default/' . $tpl;
                }
            }

        }
        else
        {
            $file =  $tpl;
        }

	
        //渲染视图
        $view = Stourweb_View::factory($file);
        foreach ($this->_data as $key => $value)
        {
            $view->set($key, $value);
            $view->set_global($key, $value);
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
                $key = $arr[$i];
                $value = isset($arr[$i + 1]) ? $arr[$i + 1] : 0;
                $out[$key] = St_Filter::remove_xss($value);
            }

        }
        return $out;

    }


} 