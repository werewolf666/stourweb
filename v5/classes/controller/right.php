<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Right extends Stourweb_Controller{
    /*
     * 右侧模块读取
     * */


    public function before()
    {
        parent::before();

    }


    public function action_index()
    {

        $this->display('right');
    }

}