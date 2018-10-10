<?php defined('SYSPATH') or die('No direct script access.');
 $kind_arr=include APPPATH.'cache/kind.php';
if(!isset($_COOKIE['current_version'])||$_COOKIE['current_version']){$kind_arr=array();} $base_array=array (
  'product' => 
  array (
    'line' => 
    array (
      'name' => '线路',
      'itemid' => '1',
      'ico' => 'chindren-ico-46.png',
      'extlink' => '0',
      'url' => 'line/line/parentkey/product/itemid/1',
    ),
    'hotel' => 
    array (
      'name' => '酒店',
      'itemid' => '2',
      'ico' => 'chindren-ico-18.png',
      'extlink' => '0',
      'url' => 'hotel/hotel/parentkey/product/itemid/2',
    ),
    'car' => 
    array (
      'name' => '租车',
      'itemid' => '3',
      'ico' => 'chindren-ico-58.png',
      'extlink' => '0',
      'url' => 'car/car/parentkey/product/itemid/3',
    ),
    'spot' => 
    array (
      'name' => '门票',
      'itemid' => '4',
      'ico' => 'chindren-ico-21.png',
      'extlink' => '0',
      'url' => 'spot/spot/parentkey/product/itemid/4',
    ),
    'visa' => 
    array (
      'name' => '签证',
      'itemid' => '5',
      'ico' => 'chindren-ico-27.png',
      'extlink' => '0',
      'url' => 'visa/visa/parentkey/product/itemid/5',
    ),
    'tuan' => 
    array (
      'name' => '团购',
      'itemid' => '6',
      'ico' => 'chindren-ico-36.png',
      'extlink' => '0',
      'url' => 'tuan/tuan/parentkey/product/itemid/6',
    ),
    'ticket' => 
    array (
      'name' => '机票',
      'itemid' => '7',
      'ico' => 'chindren-ico-jp.png',
      'extlink' => '0',
      'url' => 'ticket/ctrip/parentkey/product/itemid/7',
    ),
    'jieban' => 
    array (
      'name' => '结伴',
      'itemid' => '8',
      'ico' => 'jieban.png',
      'extlink' => '0',
      'url' => 'jieban/index/parentkey/product/itemid/8',
    ),
    'baoxian' => 
    array (
      'name' => '保险',
      'itemid' => '9',
      'ico' => 'jieban.png',
      'extlink' => '0',
      'url' => 'insurance/index/parentkey/product/itemid/9',
    ),
    'ship_line' => 
    array (
      'name' => '游轮',
      'itemid' => '20',
      'ico' => NULL,
      'extlink' => '0',
      'url' => 'ship/ship/parentkey/product/itemid/20',
    ),
  ),
  'newproduct' => 
  array (
    'line' => 
    array (
      'name' => '线路',
      'itemid' => '1',
      'ico' => 'chindren-ico-46.png',
      'extlink' => '0',
      'url' => 'line/line/parentkey/product/itemid/',
      'order' => 'order/index/parentkey/order/itemid/1/typeid/1',
      'flag' => 'line',
    ),
    'hotel' => 
    array (
      'name' => '酒店',
      'itemid' => '2',
      'ico' => 'chindren-ico-18.png',
      'extlink' => '0',
      'url' => 'hotel/hotel/parentkey/product/itemid/',
      'order' => 'order/index/parentkey/order/itemid/2/typeid/2',
      'flag' => 'hotel',
    ),
    'car' => 
    array (
      'name' => '租车',
      'itemid' => '3',
      'ico' => 'chindren-ico-58.png',
      'extlink' => '0',
      'url' => 'car/car/parentkey/product/itemid/',
      'order' => 'order/index/parentkey/order/itemid/3/typeid/3',
      'flag' => 'car',
    ),
    'spot' => 
    array (
      'name' => NULL,
      'itemid' => '4',
      'ico' => 'chindren-ico-21.png',
      'extlink' => '0',
      'url' => 'spot/spot/parentkey/product/itemid/',
      'order' => 'order/index/parentkey/order/itemid/4/typeid/',
      'flag' => 'spot',
    ),
    'visa' => 
    array (
      'name' => '签证',
      'itemid' => '5',
      'ico' => 'chindren-ico-27.png',
      'extlink' => '0',
      'url' => 'visa/visa/parentkey/product/itemid/',
      'order' => 'order/index/parentkey/order/itemid/5/typeid/8',
      'flag' => 'visa',
    ),
    'tuan' => 
    array (
      'name' => '团购',
      'itemid' => '6',
      'ico' => 'chindren-ico-36.png',
      'extlink' => '0',
      'url' => 'tuan/tuan/parentkey/product/itemid/',
      'order' => 'order/index/parentkey/order/itemid/6/typeid/13',
      'flag' => 'tuan',
    ),
    'ship_line' => 
    array (
      'name' => '邮轮',
      'itemid' => '20',
      'ico' => NULL,
      'extlink' => '0',
      'url' => 'shipadmin/shipline/index/parentkey/product/itemid/20',
      'order' => 'order/index/parentkey/order/itemid/20/typeid/104',
      'flag' => 'ship_line',
    ),
  ),
  'article' => 
  array (
    'article' => 
    array (
      'name' => '文章',
      'itemid' => '1',
      'ico' => 'wz-ico.png',
      'extlink' => '0',
      'url' => 'article/article/parentkey/article/itemid/1',
    ),
    'spot2' => 
    array (
      'name' => '景点',
      'itemid' => '2',
      'ico' => 'jd-ico.png',
      'extlink' => '0',
      'url' => 'spot/spot/parentkey/article/itemid/2',
    ),
    'notes' => 
    array (
      'name' => '游记',
      'itemid' => '3',
      'ico' => 'yj-ico.png',
      'extlink' => '0',
      'url' => 'notes/index/parentkey/article/itemid/3',
    ),
    'photo' => 
    array (
      'name' => '相册',
      'itemid' => '4',
      'ico' => 'xc-ico.png',
      'extlink' => '0',
      'url' => 'photo/photo/parentkey/article/itemid/4',
    ),
    'question' => 
    array (
      'name' => '问答',
      'itemid' => '5',
      'ico' => 'wd-ico.png',
      'extlink' => '0',
      'url' => 'question/index/parentkey/article/itemid/5',
    ),
    'pinlun' => 
    array (
      'name' => '评论',
      'itemid' => '6',
      'ico' => 'pl-ico.png',
      'extlink' => '0',
      'url' => 'comment/index/parentkey/article/itemid/6',
    ),
    'help' => 
    array (
      'name' => '帮助',
      'itemid' => '7',
      'ico' => 'bz-ico.png',
      'extlink' => '0',
      'url' => 'help/list/parentkey/article/itemid/7',
    ),
    'zhuanti' => 
    array (
      'name' => '专题',
      'itemid' => '8',
      'ico' => 'zt-ico.png',
      'extlink' => '0',
      'url' => 'zhuanti/list/parentkey/article/itemid/8',
    ),
    'jieban' => 
    array (
      'name' => '结伴',
      'itemid' => '9',
      'ico' => 'jb-ico.png',
      'extlink' => '0',
      'url' => 'jieban/index/parentkey/article/itemid/9',
    ),
  ),
  'order' => 
  array (
    'lineorder' => 
    array (
      'name' => '线路订单',
      'itemid' => '1',
      'ico' => 'chindren-ico-46.png',
      'extlink' => '0',
      'url' => 'order/index/parentkey/order/itemid/1/typeid/1',
    ),
    'hotelorder' => 
    array (
      'name' => '酒店订单',
      'itemid' => '2',
      'ico' => 'chindren-ico-18.png',
      'extlink' => '0',
      'url' => 'order/index/parentkey/order/itemid/2/typeid/2',
    ),
    'carorder' => 
    array (
      'name' => '租车订单',
      'itemid' => '3',
      'ico' => 'chindren-ico-58.png',
      'extlink' => '0',
      'url' => 'order/index/parentkey/order/itemid/3/typeid/3',
    ),
    'spotorder' => 
    array (
      'name' => '门票订单',
      'itemid' => '4',
      'ico' => 'chindren-ico-21.png',
      'extlink' => '0',
      'url' => 'order/index/parentkey/order/itemid/4/typeid/5',
    ),
    'visaorder' => 
    array (
      'name' => '签证订单',
      'itemid' => '5',
      'ico' => 'chindren-ico-27.png',
      'extlink' => '0',
      'url' => 'order/index/parentkey/order/itemid/5/typeid/8',
    ),
    'tuanorder' => 
    array (
      'name' => '团购订单',
      'itemid' => '6',
      'ico' => 'chindren-ico-36.png',
      'extlink' => '0',
      'url' => 'order/index/parentkey/order/itemid/6/typeid/13',
    ),
    'dzorder' => 
    array (
      'name' => '定制订单',
      'itemid' => '7',
      'ico' => 'chindren-ico-48.png',
      'extlink' => '0',
      'url' => 'order/dz/parentkey/order/itemid/7',
    ),
    'xyorder' => 
    array (
      'name' => '自定义订单',
      'itemid' => '8',
      'ico' => 'chindren-ico-61.png',
      'extlink' => '0',
      'url' => 'order/xy/parentkey/order/itemid/8',
    ),
    'insorder' => 
    array (
      'name' => '保险订单',
      'itemid' => '9',
      'ico' => '',
      'extlink' => '0',
      'url' => 'insurance/book/parentkey/order/itemid/9',
    ),
  ),
  'basic' => 
  array (
    'nav' => 
    array (
      'name' => '主导航',
      'itemid' => '1',
      'ico' => 'chindren-ico-56.png',
      'extlink' => '0',
      'url' => 'config/mainnav/parentkey/basic/itemid/1',
    ),
    'usernav' => 
    array (
      'name' => '自定义导航',
      'itemid' => '2',
      'ico' => 'chindren-ico-63.png',
      'extlink' => '0',
      'url' => 'app/topusernav/parentkey/basic/itemid/2',
    ),
    'footernav' => 
    array (
      'name' => '底部导航',
      'itemid' => '3',
      'ico' => 'chindren-ico-06.png',
      'extlink' => '0',
      'url' => 'footernav/index/parentkey/basic/itemid/3',
    ),
    'footer' => 
    array (
      'name' => '网页底部',
      'itemid' => '4',
      'ico' => 'chindren-ico-37.png',
      'extlink' => '0',
      'url' => 'config/footer/parentkey/basic/itemid/4',
    ),
    'logo' => 
    array (
      'name' => 'logo设置',
      'itemid' => '5',
      'ico' => 'chindren-ico-59.png',
      'extlink' => '0',
      'url' => 'config/logo/parentkey/basic/itemid/5',
    ),
    'index' => 
    array (
      'name' => '首页设置',
      'itemid' => '6',
      'ico' => 'chindren-ico-30.png',
      'extlink' => '0',
      'url' => 'config/base/parentkey/basic/itemid/6',
    ),
    'gonggao' => 
    array (
      'name' => '公告设置',
      'itemid' => '7',
      'ico' => 'chindren-ico-10.png',
      'extlink' => '0',
      'url' => 'config/gonggao/parentkey/basic/itemid/7',
    ),
    'kefu' => 
    array (
      'name' => '在线客服',
      'itemid' => '8',
      'ico' => 'chindren-ico-19.png',
      'extlink' => '0',
      'url' => 'kefu/index/parentkey/basic/itemid/8',
    ),
    'kefuthird' => 
    array (
      'name' => '三方客服',
      'itemid' => '9',
      'ico' => NULL,
      'extlink' => '0',
      'url' => 'kefu/other/parentkey/basic/itemid/9',
    ),
    'webico' => 
    array (
      'name' => '网站头像',
      'itemid' => '10',
      'ico' => 'chindren-ico-68.png',
      'extlink' => '0',
      'url' => 'config/webico/parentkey/basic/itemid/10',
    ),
    'flink' => 
    array (
      'name' => '友情链接',
      'itemid' => '11',
      'ico' => 'chindren-ico-50.png',
      'extlink' => '0',
      'url' => 'friendlink/list/parentkey/basic/itemid/11',
    ),
    'tongji' => 
    array (
      'name' => '统计代码',
      'itemid' => '14',
      'ico' => 'chindren-ico-34.png',
      'extlink' => '0',
      'url' => 'config/tongji/parentkey/basic/itemid/14',
    ),
    'templet' => 
    array (
      'name' => '模板设置',
      'itemid' => '15',
      'ico' => 'chindren-ico-22.png',
      'extlink' => '0',
      'url' => 'templet/index/parentkey/basic/itemid/15',
    ),
    'module' => 
    array (
      'name' => '模块管理',
      'itemid' => '16',
      'ico' => 'chindren-ico-24.png',
      'extlink' => '0',
      'url' => 'module/index/parentkey/basic/itemid/16',
    ),
  ),
  'kindright' => 
  array (
    'destination' => 
    array (
      'name' => '目的地',
      'itemid' => '1',
      'ico' => 'chindren-ico-04.png',
      'extlink' => NULL,
      'url' => 'destination/destination/parentkey/kind/itemid/1',
    ),
    'startplace' => 
    array (
      'name' => '出发地',
      'itemid' => '2',
      'ico' => 'chindren-ico-25.png',
      'extlink' => NULL,
      'url' => 'startplace/index/parentkey/kind/itemid/2',
    ),
  ),
  'kind' => 
  array (
    'destination' => 
    array (
      'name' => '目的地',
      'itemid' => '1',
      'ico' => 'chindren-ico-04.png',
      'extlink' => '0',
      'url' => 'destination/destination/parentkey/kind/itemid/1',
    ),
    'startplace' => 
    array (
      'name' => '出发地',
      'itemid' => '2',
      'ico' => 'chindren-ico-25.png',
      'extlink' => '0',
      'url' => 'startplace/index/parentkey/kind/itemid/2',
    ),
    'line' => 
    array (
      'name' => '线路分类',
      'itemid' => '3',
      'ico' => 'chindren-ico-46.png',
      'extlink' => '0',
      'url' => 'line/price/parentkey/kind/itemid/3',
    ),
    'hotel' => 
    array (
      'name' => '酒店分类',
      'itemid' => '4',
      'ico' => 'chindren-ico-18.png',
      'extlink' => '0',
      'url' => 'hotel/rank/parentkey/kind/itemid/4',
    ),
    'car' => 
    array (
      'name' => '租车分类',
      'itemid' => '5',
      'ico' => 'chindren-ico-58.png',
      'extlink' => '0',
      'url' => 'car/price/parentkey/kind/itemid/5',
    ),
    'spot' => 
    array (
      'name' => '门票分类',
      'itemid' => '6',
      'ico' => 'chindren-ico-21.png',
      'extlink' => '0',
      'url' => 'spot/price/parentkey/kind/itemid/6',
    ),
    'visa' => 
    array (
      'name' => '签证分类',
      'itemid' => '7',
      'ico' => 'chindren-ico-27.png',
      'extlink' => '0',
      'url' => 'visa/visatype/parentkey/kind/itemid/7',
    ),
    'tuan' => 
    array (
      'name' => '团购分类',
      'itemid' => '8',
      'ico' => 'chindren-ico-36.png',
      'extlink' => '0',
      'url' => 'attrid/list/parentkey/kind/itemid/8/typeid/13',
    ),
    'article' => 
    array (
      'name' => '文章分类',
      'itemid' => '9',
      'ico' => 'chindren-ico-40.png',
      'extlink' => '0',
      'url' => 'attrid/list/parentkey/kind/itemid/9/typeid/4',
    ),
    'photo' => 
    array (
      'name' => '相册分类',
      'itemid' => '10',
      'ico' => 'chindren-ico-47.png',
      'extlink' => '0',
      'url' => 'attrid/list/parentkey/kind/itemid/10/typeid/6',
    ),
    'helpkind1' => 
    array (
      'name' => '帮助分类',
      'itemid' => '11',
      'ico' => 'chindren-ico-02.png',
      'extlink' => '0',
      'url' => 'help/kind/parentkey/kind/itemid/11',
    ),
    'supplier' => 
    array (
      'name' => '供应商分类',
      'itemid' => '12',
      'ico' => 'chindren-ico-02.png',
      'extlink' => '0',
      'url' => 'supplier/kind/parentkey/kind/itemid/12',
    ),
    'jieban' => 
    array (
      'name' => '结伴分类',
      'itemid' => '13',
      'ico' => 'chindren-ico-02.png',
      'extlink' => '0',
      'url' => 'attrid/list/parentkey/kind/itemid/13/typeid/11',
    ),
  ),
  'ship_linekind' => 
  array (
    'ship_linecontent' => 
    array (
      'name' => '游轮内容',
      'itemid' => '0',
      'ico' => NULL,
      'extlink' => NULL,
      'url' => 'attrid/content/parentkey/kind/itemid/0/typeid/104',
    ),
    'ship_lineattr' => 
    array (
      'name' => '属性分类',
      'itemid' => '0',
      'ico' => '',
      'extlink' => NULL,
      'url' => 'attrid/list/parentkey/kind/itemid/0/typeid/104',
    ),
    'ship_linedest' => 
    array (
      'name' => '目的地分类',
      'itemid' => '0',
      'ico' => '',
      'extlink' => NULL,
      'url' => 'destination/destination/parentkey/kind/itemid/0/typeid/104',
    ),
    'ship_lineextend' => 
    array (
      'name' => '扩展字段',
      'itemid' => '0',
      'ico' => '',
      'extlink' => NULL,
      'url' => 'attrid/extendlist/parentkey/kind/itemid/0/typeid/104',
    ),
    'ship_lineprice' => 
    array (
      'name' => '价格分类',
      'itemid' => '1',
      'ico' => NULL,
      'extlink' => NULL,
      'url' => '/newtravel/shipadmin/shipline/price/parentkey/ship_linekind/itemid/1',
    ),
    'ship_lineday' => 
    array (
      'name' => '天数分类',
      'itemid' => '2',
      'ico' => NULL,
      'extlink' => NULL,
      'url' => '/newtravel/shipadmin/shipline/day/parentkey/ship_linekind/itemid/2',
    ),
    'ship_boat' => 
    array (
      'name' => '轮船',
      'itemid' => '3',
      'ico' => NULL,
      'extlink' => NULL,
      'url' => '/newtravel/shipadmin/ship/index/parentkey/ship_linekind/itemid/3',
    ),
    'ship_facilitykind' => 
    array (
      'name' => '设施分类',
      'itemid' => '4',
      'ico' => NULL,
      'extlink' => NULL,
      'url' => '/newtravel/shipadmin/ship/facilitykind/parentkey/ship_linekind/itemid/4',
    ),
    'ship_roomkind' => 
    array (
      'name' => '仓房分类',
      'itemid' => '5',
      'ico' => NULL,
      'extlink' => NULL,
      'url' => '/newtravel/shipadmin/ship/roomkind/parentkey/ship_linekind/itemid/5',
    ),
  ),
  'kefukind' => 
  array (
    'phone' => 
    array (
      'name' => '客服电话',
      'itemid' => '8',
      'ico' => '',
      'extlink' => '0',
      'url' => 'kefu/phone/parentkey/basic/itemid/8',
    ),
    'hotelprice' => 
    array (
      'name' => 'QQ客服',
      'itemid' => '8',
      'ico' => '',
      'extlink' => '0',
      'url' => 'kefu/qq/parentkey/basic/itemid/8',
    ),
    'hotelattr' => 
    array (
      'name' => '第三方客服',
      'itemid' => '8',
      'ico' => '',
      'extlink' => '0',
      'url' => 'kefu/other/parentkey/basic/itemid/8',
    ),
  ),
  'member' => 
  array (
    'member' => 
    array (
      'name' => '会员管理',
      'itemid' => '1',
      'ico' => 'chindren-ico-14.png',
      'extlink' => '0',
      'url' => 'member/index/parentkey/member/itemid/1',
    ),
    'membergrade' => 
    array (
      'name' => '会员等级',
      'itemid' => '2',
      'ico' => NULL,
      'extlink' => '0',
      'url' => 'membergrade/index/parentkey/member/itemid/2',
    ),
    'supplier' => 
    array (
      'name' => '供应商管理',
      'itemid' => '3',
      'ico' => 'chindren-ico-11.png',
      'extlink' => '0',
      'url' => 'supplier/index/parentkey/member/itemid/3',
    ),
    'user' => 
    array (
      'name' => '管理员权限',
      'itemid' => '4',
      'ico' => 'chindren-ico-62.png',
      'extlink' => '0',
      'url' => 'user/list/parentkey/member/itemid/4',
    ),
    'exchange' => 
    array (
      'name' => '积分策略',
      'itemid' => '5',
      'ico' => 'chindren-ico-67.png',
      'extlink' => '0',
      'url' => 'exchange/set/parentkey/member/itemid/5',
    ),
    'fx_phone' => 
    array (
      'name' => '分销商管理',
      'itemid' => '6',
      'ico' => NULL,
      'extlink' => '1',
      'url' => '/plugins/fx_phone/admin',
    ),
  ),
  'interface' => 
  array (
    'pay' => 
    array (
      'name' => '支付接口',
      'itemid' => '1',
      'ico' => 'chindren-ico-52.png',
      'extlink' => '0',
      'url' => 'config/payset/parentkey/interface/itemid/1',
    ),
    'msg' => 
    array (
      'name' => '短信接口',
      'itemid' => '2',
      'ico' => 'chindren-ico-42.png',
      'extlink' => '0',
      'url' => 'sms/index/parentkey/interface/itemid/2',
    ),
    'email' => 
    array (
      'name' => '邮箱接口',
      'itemid' => '3',
      'ico' => 'chindren-ico-40.png',
      'extlink' => '0',
      'url' => 'email/index/parentkey/interface/itemid/3',
    ),
    'thirdpart' => 
    array (
      'name' => '第三方登陆',
      'itemid' => '4',
      'ico' => 'chindren-ico-29.png',
      'extlink' => '0',
      'url' => 'config/thirdpart/parentkey/interface/itemid/4',
    ),
    'jipiao' => 
    array (
      'name' => '机票接口',
      'itemid' => '5',
      'ico' => 'chindren-ico-52.png',
      'extlink' => '0',
      'url' => 'ticket/ctrip/parentkey/interface/itemid/5',
    ),
    'insurance' => 
    array (
      'name' => '保险接口',
      'itemid' => '6',
      'ico' => 'chindren-ico-52.png',
      'extlink' => '0',
      'url' => 'insurance/huizhe/parentkey/interface/itemid/6',
    ),
    'ucenter' => 
    array (
      'name' => 'ucenter配置',
      'itemid' => '7',
      'ico' => 'chindren-ico-66.png',
      'extlink' => '0',
      'url' => 'config/ucenter/parentkey/interface/itemid/7',
    ),
    'blbqlinedistribution_product' => 
    array (
      'name' => '比来比去线路',
      'itemid' => '8',
      'ico' => 'chindren-ico-66.png',
      'extlink' => '1',
      'url' => '/plugins/blbqlinedistribution/product/index',
    ),
  ),
  'system' => 
  array (
    'payment' => 
    array (
      'name' => '签约流程',
      'itemid' => '1',
      'ico' => 'chindren-ico-26.png',
      'extlink' => '0',
      'url' => 'config/payment/parentkey/system/itemid/1',
    ),
    'ordermail' => 
    array (
      'name' => '订单提醒',
      'itemid' => '2',
      'ico' => 'chindren-ico-41.png',
      'extlink' => '0',
      'url' => 'config/ordermail/parentkey/system/itemid/2',
    ),
    'icon' => 
    array (
      'name' => '图标管理',
      'itemid' => '4',
      'ico' => 'chindren-ico-35.png',
      'extlink' => '0',
      'url' => 'icon/index/parentkey/system/itemid/4',
    ),
    'water' => 
    array (
      'name' => '水印设置',
      'itemid' => '5',
      'ico' => 'chindren-ico-33.png',
      'extlink' => '0',
      'url' => 'config/watermark/parentkey/system/itemid/5',
    ),
    'nophoto' => 
    array (
      'name' => '无图设置',
      'itemid' => '6',
      'ico' => 'chindren-ico-43.png',
      'extlink' => '0',
      'url' => 'config/nopic/parentkey/system/itemid/6',
    ),
    'sys' => 
    array (
      'name' => '参数开关',
      'itemid' => '7',
      'ico' => 'chindren-ico-44.png',
      'extlink' => '0',
      'url' => 'config/syspara/parentkey/system/itemid/7',
    ),
    'databack' => 
    array (
      'name' => '数据备份',
      'itemid' => '8',
      'ico' => 'chindren-ico-31.png',
      'extlink' => '0',
      'url' => 'databackup/index/parentkey/system/itemid/8',
    ),
    'htaccess' => 
    array (
      'name' => '伪静态配置',
      'itemid' => '9',
      'ico' => 'weijingtai.png',
      'extlink' => '0',
      'url' => 'config/htaccess/parentkey/system/itemid/9',
    ),
    'model' => 
    array (
      'name' => '扩展产品',
      'itemid' => '10',
      'ico' => 'moxing.png',
      'extlink' => '0',
      'url' => 'model/index/parentkey/system/itemid/10',
    ),
    'image' => 
    array (
      'name' => '图片管理',
      'itemid' => '11',
      'ico' => 'N_03.gif',
      'extlink' => '0',
      'url' => 'image/index/parentkey/system/itemid/11',
      'flag' => 'new',
    ),
    'partversionmanage' => 
    array (
      'name' => '版本管理',
      'itemid' => '12',
      'ico' => 'N_03.gif',
      'extlink' => '0',
      'url' => 'systemparts/index/parentkey/system/itemid/12',
      'flag' => 'new',
    ),
    'currency' => 
    array (
      'name' => '汇率管理',
      'itemid' => '13',
      'ico' => 'chindren-ico-51.png',
      'extlink' => '0',
      'url' => 'currency/config/parentkey/system/itemid/13',
    ),
    'site' => 
    array (
      'name' => '子站管理',
      'itemid' => '14',
      'ico' => 'chindren-ico-51.png',
      'extlink' => '0',
      'url' => 'site/index/parentkey/system/itemid/14',
    ),
    'authright' => 
    array (
      'name' => '授权管理',
      'itemid' => '15',
      'ico' => 'chindren-ico-51.png',
      'extlink' => '0',
      'url' => 'config/authright/parentkey/system/itemid/15',
    ),
    'log' => 
    array (
      'name' => '操作日志',
      'itemid' => '16',
      'ico' => 'chindren-ico-03.png',
      'extlink' => '0',
      'url' => 'userlog/index/parentkey/system/itemid/16',
    ),
    'advertise' => 
    array (
      'name' => '广告管理',
      'itemid' => '17',
      'ico' => 'chindren-ico-13.png',
      'extlink' => '0',
      'url' => 'advertise5x/index/parentkey/system/itemid/17',
    ),
    'noticemanager_sms' => 
    array (
      'name' => '短信通知',
      'itemid' => '18',
      'ico' => 'chindren-ico-13.png',
      'extlink' => '0',
      'url' => 'noticemanager/sms/parentkey/system/itemid/18',
    ),
    'noticemanager_email' => 
    array (
      'name' => '邮件通知',
      'itemid' => '19',
      'ico' => 'chindren-ico-13.png',
      'extlink' => '0',
      'url' => 'noticemanager/email/parentkey/system/itemid/19',
    ),
  ),
  'application' => 
  array (
    'upgrade' => 
    array (
      'name' => '系统升级',
      'itemid' => '1',
      'ico' => 'chindren-ico-45.png',
      'extlink' => '0',
      'url' => 'upgrade/index/parentkey/application/itemid/1',
    ),
    'contact' => 
    array (
      'name' => '服务合同',
      'itemid' => '2',
      'ico' => 'chindren-ico-09.png',
      'extlink' => '0',
      'url' => 'app/other/parentkey/application/itemid/2/type/contract',
    ),
    'templetmall' => 
    array (
      'name' => '模板商城',
      'itemid' => '3',
      'ico' => 'chindren-ico-23.png',
      'extlink' => '0',
      'url' => 'app/other/parentkey/application/itemid/3/type/moban',
    ),
    'seoservice' => 
    array (
      'name' => '营销服务',
      'itemid' => '4',
      'ico' => 'chindren-ico-49.png',
      'extlink' => '0',
      'url' => 'app/other/parentkey/application/itemid/4/type/seo',
    ),
    'mallindex' => 
    array (
      'name' => '应用商城',
      'itemid' => '5',
      'ico' => 'N_03.gif',
      'extlink' => '0',
      'url' => 'mall/index/parentkey/application/itemid/5',
      'flag' => 'new',
    ),
    'myapp' => 
    array (
      'name' => '我的应用',
      'itemid' => '6',
      'ico' => 'N_03.gif',
      'extlink' => '0',
      'url' => 'mall/app/parentkey/application/itemid/6',
      'flag' => 'new',
    ),
  ),
  'tool' => 
  array (
    'robots' => 
    array (
      'name' => 'robots设置',
      'itemid' => '1',
      'ico' => 'chindren-ico-60.png',
      'extlink' => '0',
      'url' => 'config/robots/parentkey/tool/itemid/1',
    ),
    'keyword' => 
    array (
      'name' => '关键词统计',
      'itemid' => '2',
      'ico' => 'chindren-ico-12.png',
      'extlink' => '0',
      'url' => 'keyword/index/parentkey/tool/itemid/2',
    ),
    'seolink' => 
    array (
      'name' => '智能内链',
      'itemid' => '3',
      'ico' => 'chindren-ico-54.png',
      'extlink' => '0',
      'url' => 'toollink/index/parentkey/tool/itemid/3',
    ),
    'mutititle' => 
    array (
      'name' => '批量Title',
      'itemid' => '4',
      'ico' => 'chindren-ico-64.png',
      'extlink' => '0',
      'url' => 'app/mutititle/parentkey/tool/itemid/4',
    ),
    'tagword' => 
    array (
      'name' => 'Tag管理',
      'itemid' => '5',
      'ico' => 'chindren-ico-01.png',
      'extlink' => '0',
      'url' => 'tagword/index/parentkey/tool/itemid/5',
    ),
    'ourtj' => 
    array (
      'name' => '来源分析',
      'itemid' => '6',
      'ico' => 'chindren-ico-55.png',
      'extlink' => '0',
      'url' => 'visit/index/parentkey/tool/itemid/6',
    ),
    'sitemap' => 
    array (
      'name' => 'Sitemap',
      'itemid' => '7',
      'ico' => 'chindren-ico-53.png',
      'extlink' => '0',
      'url' => 'sitemap/index/parentkey/tool/itemid/7',
    ),
    'dielink' => 
    array (
      'name' => '死链排查',
      'itemid' => '8',
      'ico' => 'chindren-ico-65.png',
      'extlink' => '0',
      'url' => 'sitemap/errorlink/parentkey/tool/itemid/8',
    ),
    'hotsearch' => 
    array (
      'name' => '热搜词分析',
      'itemid' => '9',
      'ico' => 'chindren-ico-28.png',
      'extlink' => '0',
      'url' => 'hotsearch/index/parentkey/tool/itemid/9',
    ),
  ),
  'mobile' => 
  array (
    'm_sys' => 
    array (
      'name' => '移动参数',
      'itemid' => '1',
      'ico' => 'chindren-ico-14.png',
      'extlink' => '0',
      'url' => 'mobile/sys/parentkey/mobile/itemid/1',
    ),
    'm_nav' => 
    array (
      'name' => '移动导航',
      'itemid' => '3',
      'ico' => 'chindren-ico-62.png',
      'extlink' => '0',
      'url' => 'mobile/nav/parentkey/mobile/itemid/3',
    ),
    'm_foot_nav' => 
    array (
      'name' => '底部导航',
      'itemid' => '4',
      'ico' => 'chindren-ico-62.png',
      'extlink' => '0',
      'url' => 'footernav/index/parentkey/mobile/itemid/4/ismobile/1',
    ),
    'm_templet' => 
    array (
      'name' => '移动模板',
      'itemid' => '5',
      'ico' => 'chindren-ico-62.png',
      'extlink' => '0',
      'url' => 'templet/index/parentkey/mobile/itemid/5/ismobile/1',
    ),
  ),
  'finance' => 
  array (
    'drawcash' => 
    array (
      'name' => '提现审核 ',
      'itemid' => '1',
      'ico' => 'chindren-ico-66.png',
      'extlink' => '0',
      'url' => 'finance/index/parentkey/finance/itemid/1',
    ),
  ),
  'chinesename' => 
  array (
    'product' => '产品',
    'article' => '文章',
    'kindright' => '分类设置',
    'kind' => '分类设置',
    'linekind' => '线路分类',
    'hotelkind' => '酒店分类',
    'carkind' => '租车分类',
    'spotkind' => '景点分类',
    'visakind' => '签证分类',
    'articlekind' => '文章分类',
    'tuankind' => '团购分类',
    'photokind' => '相册分类',
    'helpkind' => '帮助分类',
    'basic' => '站点设置',
    'member' => '会员管理',
    'order' => '订单管理',
    'system' => '系统设置',
    'templet' => '模板设置',
    'application' => '增值应用',
    'tool' => '优化应用',
    'sale' => '营销策略',
    'kefukind' => '客服管理',
    'mobile' => '手机配置',
    'userdefined' => '用户定义',
  ),
  'userdefined' => 
  array (
  ),
); return array_merge($base_array,$kind_arr);