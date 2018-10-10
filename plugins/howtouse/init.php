<?php
$GLOBALS['cfg_plugin_howtouse_public_url'] ='/'.str_replace('\\','/',str_replace(BASEPATH,'',dirname(__FILE__))).'/public/';
//后台路由规则引入
if(defined('ISADMIN'))
{
    include 'admin_init.php';
}
/**前台路由规则**/
$routeFile = ISMOBILE == 1 ? 'm' : 'pc';
include $routeFile . '_init.php';


