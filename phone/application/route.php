<?php defined('SYSPATH') or die('No direct script access.');

//会员中心
Route::set('member', 'member(/<controller>(/<action>(/<query>)))', array('query' => '.*'))
    ->defaults(array(
        'action' => 'index',
        'controller' => 'index',
        'directory' => 'member'
    ));

//消息提示
Route::set('message', 'message/<param>', array(
    'param' => '[a-zA-Z0-9]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'message'
));


//添加问答
Route::set('question', 'questions/<action>', array(
    'action' => '\w+'
))->defaults(array(
    'action' => 'add',
    'controller' => 'question'
));
//问答列表
Route::set('question_list', 'questions', array(
))->defaults(array(
    'action' => 'index',
    'controller' => 'question'
));


//帮助
Route::set('help_list', 'help/index_<aid>.html', array(
    'aid' => '\d+',
    'action' => 'list'
))->defaults(array(
    'action' => 'list',
    'controller' => 'help'
));
Route::set('help_show', 'help/show_<aid>.html', array(
    'aid' => '\d+',
    'action' => 'show'
))->defaults(array(
    'action' => 'show',
    'controller' => 'help'
));
Route::set('help_all', 'help(/<action>)', array(
))->defaults(array(
    'action' => 'index',
    'controller' => 'help'
));


//服务选项
Route::set('servers','servers/index_<aid>.html',array(
    'aid'=>'\d+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'server'
));






//全局搜索
Route::set('search', 'search')->defaults(array(
    'action' => 'index',
    'controller' => 'search'
));


//通用模块路由
include_once(APPPATH.'tyroute.php');
include_once(APPPATH.'userroute.php');

//目的地列表
Route::set('destlist', 'destination')->defaults(array(
    'action' => 'index',
'directory' => 'mobile',
'controller' => 'destination'
));
//专题
Route::set('zhuanti', 'zhuanti(_<id>.html)', array(
    'id' => '\d+'
))->defaults(array(
    'controller' => 'zhuanti',
    'action' => 'index'
));
//404错误页
Route::set('404', '404')->defaults(array(
    'action' => '404',
    'controller' => 'pub'
));

//单目的地主页
Route::set('desthome', '<pinyin>', array(
    'pinyin' => '[a-zA-Z0-9]+'
))->defaults(array(
    'action' => 'main',
'directory' => 'mobile',
'controller' => 'destination'
));
//子站内容显示
Route::set('substation', '<pinyin>/<model>/show_<aid>.html', array(
    'pinyin' => '[a-zA-Z0-9]+',
    'model' => '[a-zA-Z0-9]+',
    'aid' => '\d+'
))->defaults(array(
    'action' => 'sub_station',
    'controller' => 'index'
));
//错误页面
Route::set('error', 'error/<action>(/<message>)', array('action' => '[0-9]++', 'message' => '.+'))
    ->defaults(array(
        'controller' => 'error'
    ));
//默认
Route::set('default', '(<controller>(/<action>(/<params>)))', array(
    'params' => '.*'
))->defaults(array(
    'controller' => 'index',
    'action' => 'index',
));

//专题静态化处理
Route::set('new_zhuanti', 'zhuanti/<id>.html', array('tid'=>'\d+')
)->defaults(
    array(
        'controller' => 'zhuanti',
        'action' => 'index'
    ));