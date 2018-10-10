<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 此文件主要用于用户定制有其它文件请求时使用.
 * Class Controller_UserDz
 */
class Controller_Dz extends Stourweb_Controller{

    private  $_php_file = NULL;
    private  $_class_name = NULL;
    private  $_method = NULL;

    public function before()
    {
        parent::before();
        $this->_php_file =BASEPATH.'usertpl/'.Common::remove_xss(Arr::get($_POST,'phpfile'));
        $this->_method = Common::remove_xss(Arr::get($_POST,'method'));
        $this->_class_name = Common::remove_xss(Arr::get($_POST,'classname'));

    }
    public function action_index()
    {
        require_once($this->_php_file);
        $m = new $this->_class_name();
        $method = $this->_method;
        $m->$method($_POST);
    }

}