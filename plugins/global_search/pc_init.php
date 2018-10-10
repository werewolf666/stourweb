<?php defined('SYSPATH') or die('No direct script access.');


//线路
Route::set('global_search_line', 'query/line-<destid>-<dayid>-<priceid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'dayid' => '[0-9]+',
    'priceid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'line',
    'directory' => 'pc/global'
));
//攻略
Route::set('global_search_article', 'query/article-<destid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'article',
    'directory' => 'pc/global'
));
//租车
Route::set('global_search_car', 'query/car-<destid>-<carkind>-<attrlist>', array(
    'destid' => '[0-9]+',
    'carkind' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'car',
    'directory' => 'pc/global'
));
//导游
Route::set('global_search_guide', 'query/guide-<destid>-<priceid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'priceid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'guide',
    'directory' => 'pc/global'
));

//酒店
Route::set('global_search_hotel', 'query/hotel-<destid>-<rankid>-<priceid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'rankid' => '[0-9]+',
    'priceid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'hotel',
    'directory' => 'pc/global'
));


//保险
Route::set('global_search_insurance', 'query/insurance-<destid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'insurance',
    'directory' => 'pc/global'
));

//结伴
Route::set('global_search_jieban', 'query/jieban-<destid>-<dayid>-<timeid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'dayid' => '[0-9_]+',
    'timeid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'jieban',
    'directory' => 'pc/global'
));

//咨询
Route::set('global_search_news', 'query/news-<attrlist>', array(
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'news',
    'directory' => 'pc/global'
));



//户外
Route::set('global_search_outdoor', 'query/outdoor-<destid>-<startcityid>-<dayid>-<priceid>-<groupid>-<bookstatus>-<attrlist>', array(
    'destid' => '[0-9]+',
    'startcityid' => '[0-9]+',
    'priceid' => '[0-9]+',
    'dayid' => '[0-9]+',
    'groupid' => '[0-9]+',
    'bookstatus' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'outdoor',
    'directory' => 'pc/global'
));

//相册
Route::set('global_search_photo', 'query/photo-<destid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'photo',
    'directory' => 'pc/global'
));


//邮轮
Route::set('global_search_ship', 'query/ship_line-<destid>-<startcityid>-<shipid>-<dayid>-<priceid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'startcityid' => '[0-9]+',
    'priceid' => '[0-9]+',
    'dayid' => '[0-9]+',
    'shipid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'ship',
    'directory' => 'pc/global'
));


//景点
Route::set('global_search_spot', 'query/spot-<destid>-<priceid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'priceid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'spot',
    'directory' => 'pc/global'
));


//团购
Route::set('global_search_tuan', 'query/tuan-<destid>-<statusid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'statusid' => '[0-9]+',
    'attrlist' => '[0-9_]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'tuan',
    'directory' => 'pc/global'
));

//签证
Route::set('global_search_visa', 'query/visa-<visakindid>-<visacityid>', array(
    'visakindid' => '[0-9]+',
    'visacityid' => '[0-9]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'visa',
    'directory' => 'pc/global'
));

//通用
Route::set('global_search_tongyong', 'query/general/<pinyin>-<destid>-<attrlist>', array(
    'destid' => '[0-9]+',
    'attrlist' => '[0-9_]+',
    'pinyin' => '[a-zA-Z0-9]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'general',
    'directory' => 'pc/global'
));





//前台路由规则
Route::set('mobile_ship_search', 'query/ship_line(/<action>)', array(
    'pinyin' => '[a-zA-Z0-9]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'ship',
    'directory' => 'pc/global'
));


//前台路由规则
Route::set('global_search', 'query/<controller>(/<pinyin>)', array(
    'pinyin' => '[a-zA-Z0-9]+'
))->defaults(array(
    'action' => 'index',
    'controller' => 'search',
    'directory' => 'pc/global'
));
