<?php
$GLOBALS['cfg_plugin_destination_public_url'] = '/' . str_replace('\\', '/', str_replace(BASEPATH, '', dirname(__FILE__))) . '/public/';
//后台路由规则引入

if (defined('ISADMIN'))
{
    include 'admin_init.php';
}
else
{
    $routeFile = ISMOBILE == 1 ? 'm' : 'pc';
    include $routeFile . '_init.php';
}


