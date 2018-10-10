<?php defined('SYSPATH') or die('No direct script access.');
//目的地列表
Route::set('destination_check', 'destination(/<action>(/<params>))')->defaults(
    array(
        'controller' => 'destination',
        'action' => 'index',
        'directory' => 'mobile',
    )
);