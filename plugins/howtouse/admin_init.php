<?php defined('SYSPATH') or die('No direct script access.');
/**后台路由规则**/
Route::set('howtouse_admin_main', 'howtouse/admin(/<controller>(/<action>(/<params>)))', array('params' => '.*'))
    ->defaults(array(
        'controller' => 'index',
        'action' => 'index',
        'directory'=>'admin/howtouse'
    ));
