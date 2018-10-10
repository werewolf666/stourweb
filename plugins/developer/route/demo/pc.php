<?php
/**
 * Copyright:www.stourweb.com
 * Author: netman
 * QQ: 1649513971
 * Time: 2017/10/24 17:58
 * Desc:
 */

Route::set('developer_pc_line_show', 'lines/<action>_<aid>.html', array(
    'aid' => '\d+',
    'action' => '(print|show)'
))->defaults(array(
    'directory' => 'developer/demo',
    'controller' => 'pc',
    'action' => 'show',
));












