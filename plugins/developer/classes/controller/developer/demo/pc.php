<?php
/**
 * Copyright:www.stourweb.com
 * Author: netman
 * QQ: 1649513971
 * Time: 2017/10/24 17:26
 * Desc:开发者demo pc控制器
 */
class Controller_Developer_Demo_Pc extends Stourweb_Controller {



    public function before()
    {
        parent::before();
    }

    public function after()
    {
        parent::after();
    }

    //详情页
    public function action_show()
    {
        //echo 'this is a developer line_show page';
        $this->display('../developer/demo/show');
    }






}