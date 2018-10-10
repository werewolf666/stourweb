<?php
/**
 * Copyright:www.stourweb.com
 * Author: netman
 * QQ: 1649513971
 * Time: 2017/10/24 17:58
 * Desc:
 */
//手机端详情页
Route::set('developer_mobile_line_show', 'lines(/<action>_<aid>.html)', array(
    'aid' => '\d+',
    'action' => '(print|show)'
))->defaults(array(
    'directory' => 'developer/demo',
    'action' => 'index',
    'controller' => 'mobile',

));












