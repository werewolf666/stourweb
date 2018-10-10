<?php defined('SYSPATH') or die('No direct script access.');
//前台路由规则
Route::set('mobile_ship_search', 'query/ship_line(/<action>)', array(
    'pinyin' => '[a-zA-Z0-9]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'ship',
    'directory' => 'mobile/global'
));

//前台路由规则
Route::set('mobile_global_search', 'query/<controller>(/<action>)(/<pinyin>)', array(
    'pinyin' => '[a-zA-Z0-9]+',
))->defaults(array(
    'action' => 'index',
    'controller' => 'search',
    'directory' => 'mobile/global'
));
