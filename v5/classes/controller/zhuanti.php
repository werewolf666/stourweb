<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Zhuanti extends Stourweb_Controller{
    /*
     * 专题总控制器
     * */

    private $typeid = 0;
    private $_cache_key = '';
    public function before()
    {
        parent::before();
        //检查缓存
        $this->_cache_key = Common::get_current_url();
        $html = Common::cache('get',$this->_cache_key);
        if(!empty($html))
        {
            echo $html;
            exit;
        }

        $this->assign('typeid', $this->typeid);

    }

    //首页
    public function action_index()
    {

        $tid = intval(Common::remove_xss(Arr::get($_GET,'tid')));
        if (empty($tid))
        {
            $tid = $this->request->param('tid');
        }
        else
        {
            $this->request->redirect('/zhuanti/'.$tid.'.html');
        }
        if(empty($tid))
        {
            $this->request->redirect($this->request->referrer());
        }
        $info = ORM::factory('theme',$tid)->as_array();
        $info['title'] = $info['ztname'];
        //seo
        $seoInfo = Product::seo($info);

        $this->assign('seoinfo',$seoInfo);

        $templet = $info['templet'];
        if(strpos($templet,'usertpl')===false)
        {
            $templet = 'zhuanti/index';
        }
        else
        {
            $templet.='/index';
        }
        $this->assign('info',$info);
        $this->display($templet);
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);
    }









}