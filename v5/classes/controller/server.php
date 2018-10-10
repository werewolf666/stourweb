<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Server extends Stourweb_Controller{
    /*
     * 副导航控制器
     * */
    private  $_cache_key = '';

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


    }

    //副导航显示页面
    public function action_index()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        if(empty($aid))exit;
        $info = ORM::factory('serverlist')
            ->where("aid=$aid and webid=0")
            ->find()
            ->as_array();
        $this->assign('info', $info);
        $this->display('server/index');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);
    }










}