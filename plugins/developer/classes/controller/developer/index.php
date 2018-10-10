<?php
/**
 * Copyright:www.stourweb.com
 * Author: netman
 * QQ: 1649513971
 * Time: 2017/10/24 17:26
 * Desc:开发者默认控制器
 */
class Controller_Developer_Index extends Stourweb_Controller {



    public function before()
    {
        parent::before();
    }

    public function after()
    {
        parent::after();
    }

    //首页
    public function action_index()
    {
        echo 'this is a developer default page!';
    }







}