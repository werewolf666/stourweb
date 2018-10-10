<?php defined('SYSPATH') or die('No direct script access.');

class Controller_tips extends Stourweb_Controller
{
    public function before()
    {
        parent::before();
    }

    /**
     * ¶©µ¥ÌáÊ¾
     */
    public function action_order()
    {
        $this->assign('referurl', $this->request->referrer());
        $this->display('tips/order');
    }
}