<?php defined('SYSPATH') or die('No direct script access.');

//订单
Route::set('member_order', 'member/order(/<action>(-<ordertype>))(-<p>)',
    array(
        'action' =>"[a-zA-Z]+",
        'ordertype' => '[a-zA-Z]+',
        'p' => '[0-9]+'
    ))
    ->defaults(array(
        'action' => 'index',
        'controller' => 'order',
        'directory' => 'member'
    ));

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
Route::set('help', 'help/<action>_<aid>.html', array(
    'aid' => '\d+',
    'action' => '(index|show)'
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
//定制模块路由
if(file_exists(APPPATH.'dzroute.php'))
{
    include_once(APPPATH.'dzroute.php');
}

//目的地列表
Route::set('destlist', 'destination')->defaults(array(
    'action' => 'index',
'directory' => 'pc',
'controller' => 'destination'
));

//专题
Route::set('zhuanti', 'zhuanti', array())->defaults(array(
    'controller' => 'zhuanti',
    'action' => 'index'
));
//专题静态化处理
Route::set('new_zhuanti', 'zhuanti/<tid>.html', array('tid'=>'\d+')
)->defaults(
    array(
    'controller' => 'zhuanti',
        'action' => 'index'
 ));


//单目的地主页
Route::set('desthome', '<pinyin>', array(
    'pinyin' => '[a-zA-Z0-9]+'
))->defaults(array(
    'action' => 'main',
'directory' => 'pc',
'controller' => 'destination'
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


