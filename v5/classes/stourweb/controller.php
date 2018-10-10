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
        $params = $this->request->param('params');
        $this->params = $this->analyze_param($params);
        $controller = $this->request->controller();
        $action = $this->request->action();


    }

    /*
     * 显示模板
     * @param string $tpl,模板名
     * */
    public function display($tpl)
    {
        $file = '';
        //判断是否是标准模板
        if (!file_exists(APPPATH . '/views/' . $GLOBALS['cfg_templet'] . '/' . $tpl . '.php'))
        {
            $file = 'default/' . $tpl;
        }
        else
        {
            $file = "{$GLOBALS['cfg_templet']}/" . $tpl;
        }
        //判断是否是用户上传模板
        if (strpos($tpl, 'usertpl') !== false)
        {
            $file = $tpl;
            $tpl_info = explode('/',$tpl);
            $user_tpl = $tpl_info[1] ? $tpl_info[1] : '';
            $this->assign('user_tpl',$user_tpl);

        }
        $view = Stourweb_View::factory($file);
        //模板数据
        $this->assign("templetdata", $this->_data);

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
                $out[$key] = Common::remove_xss($value);
            }

        }
        return $out;

    }


} 