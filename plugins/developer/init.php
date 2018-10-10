<?php
$GLOBALS['cfg_plugin_developer_public_url'] = '/' . str_replace('\\', '/', str_replace(BASEPATH, '', dirname(__FILE__))) . '/public/';




//后台路由,在这里添加需要重写或者操作的路由
if(defined('ISADMIN'))
{

    //include  dirname(__FILE__).'/route/demo/admin.php';
    //以上是一个测试例子....可以在这里加载更多开发者自己的路由
}
//手机端路由,在这里添加手机端的路由
else if(defined('ISMOBILE'))
{
    //include  dirname(__FILE__).'/route/demo/mobile.php';
    //以上是一个测试例子....可以在这里加载更多开发者自己的路由
}
//PC端路由
else
{
    //include  dirname(__FILE__).'/route/demo/pc.php';
    //以上是一个测试例子....可以在这里加载更多开发者自己的路由
}

//开发者默认路由规则,
Route::set('developer', 'developer(/<controller>(/<action>(/<params>)))', array(
    'params' => '.*'
))->defaults(array(
    'controller' => 'index',
    'action' => 'index',
    'directory' => 'developer'
));







