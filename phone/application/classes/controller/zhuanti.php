<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Zhuanti extends Stourweb_Controller
{


    public function before()
    {
        parent::before();
    }

    /**
     * 首页
     */
    public function action_index()
    {
        $tid = intval($_GET['tid']);
        if($tid)
        {
            $this->request->redirect('zhuanti/'.$tid.'.html');
        }
        $tid = intval($this->request->param('id'));
        if(!$tid)
        {
            $this->request->redirect('error/404');
            return;
        }
        $info = ORM::factory('theme',$tid)->as_array();
        if(empty($info))
        {
            $this->request->redirect('error/404');
            return;
        }
        $info['title'] = $info['ztname'];
        //seo
        $seoInfo = Product::seo($info);
        $this->assign('seoinfo',$seoInfo);
        $this->assign('info',$info);
        $this->display('zhuanti/index','zhuanti');
    }
}