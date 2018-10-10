<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Help extends Stourweb_Controller{
    /*
     * 帮助控制器
     * */
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

    }

    //帮助列表页
    public function action_index()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        if(empty($aid))exit;
        $info = ORM::factory('help_kind')
            ->where("id=$aid and webid=0")
            ->find()
            ->as_array();

        $this->assign('info', $info);
        $this->display('help/index');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);

    }

    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        $info = ORM::factory('help')
            ->where("aid=$aid and webid=0")
            ->find()
            ->as_array();
        $this->assign('info',$info);
        $this->display('help/show');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);

    }










}