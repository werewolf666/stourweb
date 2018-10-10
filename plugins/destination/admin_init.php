<?php defined('SYSPATH') or die('No direct script access.');

Route::set('destination_admin', 'destination/admin/destination(/<action>(/<params>))', array('params' => '.*'))
    ->defaults(array(
        'controller' => 'destination',
        'action' => 'index',
        'directory' => 'admin'
    ));
