<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception
{

    public static function handler(Exception $e)
    {
        if (Kohana::$environment === Kohana::DEVELOPMENT)
        {
            parent::handler($e);
        }
        else
        {
		   //输出显示原生错误,暂时没有页面.
           echo parent::text($e);
        }
    }
}