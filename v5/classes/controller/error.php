<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Error extends Stourweb_Controller
{
    /*
     * 错误控制器
     * 可接收参数 message错误信息
     * */
    public function before()
    {
        parent::before();
        $this->assign('referurl', $this->request->referrer());
    }

    //404页面
    public function action_404()
    {
        $this->response->status(404);
        $this->display('error/404');
    }

    //500页面
    public function action_500()
    {
        $this->response->status(500);
        $this->display('error/500');
    }

    //通用错误提示
    public function action_tips()
    {
        $msg = Kohana::message('tips', str_replace('_', '.', $_GET['msg']));
        if (!preg_match('~[a-zA-Z0-9_]+~', $_GET['msg']) || is_null($msg))
        {
            $this->request->redirect('error/404');
        }
        $this->assign('info', $msg);
        $this->display('error/tips');
    }

}