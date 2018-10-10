<?php defined('SYSPATH') or die('No direct script access.');

class Controller_TongYong extends Stourweb_Controller
{
    /*
     * 通用总控制器
     * */

    protected $typeid = NULL;
    protected $pinyin = NULL;
    protected $_cache_key = '';

    public function before()
    {
        parent::before();
        //检查缓存
        $this->_cache_key = Common::get_current_url();
        $html = Common::cache('get', $this->_cache_key);
        if (!empty($html)) {
            echo $html;
            exit;
        }

        $channelname = Model_Nav::get_channel_name($this->typeid);
        $this->assign('typeid', $this->typeid);
        $this->assign('module_pinyin', $this->pinyin);
        $this->assign('channelname', $channelname);
    }

    //首页
    public function action_index()
    {
        $this->request->redirect($this->pinyin . '/all');
    }

    //详细页
    public function action_show()
    {
        $aid = intval($this->request->param('aid'));
        //详情
        $info = Model_Tongyong::detail($aid, $this->typeid, $GLOBALS['sys_webid']);
        if (!$info) {
            $this->request->redirect('error/404');
        }
        //seo
        $seoInfo = Product::seo($info);
        //产品图片
        $info['piclist'] = Product::pic_list($info['piclist']);
        //属性列表
        $info['attrlist'] = Model_Tongyong::product_attr($info['attrid'], $this->typeid);
        //最低价
        $info['price'] = Model_Tongyong::get_minprice($info['id'], array('info' => ''));
        //市场价
        $info['sellprice'] = Model_Tongyong::get_min_sellprice($info['id']);
        //满意度
        $info['score'] = $info['satisfyscore'] . '%';
        //点评数
        $info['commentnum'] = Model_Comment::get_comment_num($info['id'], $this->typeid);
        //销售数量
        $info['sellnum'] = Model_Member_Order::get_sell_num($info['id'], $this->typeid);
        //产品编号
        $info['series'] = Product::product_number($info['id'], $this->typeid);
        //产品图标
        $info['iconlist'] = Product::get_ico_list($info['iconlist']);

        $info['jifentprice_info'] = Model_Jifen_Price::get_used_jifentprice($info['jifentprice_id'], $this->typeid);
        $info['jifenbook_info'] = Model_Jifen::get_used_jifenbook($info['jifenbook_id'], $this->typeid);
        $info['jifencomment_info'] = Model_Jifen::get_used_jifencomment($this->typeid);
        //支付方式
        $paytypeArr = explode(',', $GLOBALS['cfg_pay_type']);
        //扩展字段信息
        $extend_info = Model_Tongyong::extend($info['id'], $this->typeid);
        $this->assign('seoinfo', $seoInfo);
        $this->assign('info', $info);
        $this->assign('paytypeArr', $paytypeArr);
        $this->assign('extendinfo', $extend_info);

        $templet = Product::get_use_templet($this->pinyin . '_show');
        $templet = $templet ? $templet : 'tongyong/show';
        $this->display($templet);
        //缓存内容
        $content = $this->response->body();
        Common::cache('set', $this->_cache_key, $content);

    }


    //列表页
    public function action_list()
    {
        //参数值获取
        $destPy = $this->request->param('destpy');
        $sortType = intval($this->request->param('sorttype'));
        $attrId = $this->request->param('attrid');
        $p = intval($this->request->param('p'));
        $attrId = !empty($attrId) ? $attrId : 0;

        $destPy = $destPy ? $destPy : 'all';
        $pagesize = 12;
        $keyword = Common::remove_xss(Arr::get($_GET, 'keyword'));

        $route_array = array(
            'controller' => $this->pinyin,
            'action' => 'list',
            'destpy' => $destPy,
            'sorttype' => $sortType,
            'attrid' => $attrId,
            'typeid' => $this->typeid
        );

        //$start_time=microtime(true); //获取程序开始执行的时间

        $out = Model_Tongyong::search_result($route_array, $keyword, $p, $pagesize);


        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pagesize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $route_array['typeid'] = $this->typeid;
        $destId = $destPy == 'all' ? 0 : DB::select('id')->from('destinations')->where('pinyin', '=', $destPy)->execute()->get('id');
        $destId = $destId ? $destId : 0;
        //目的地信息
        $destInfo = array();
        $preDest = array();
        if ($destId) {
            //$destInfo = ORM::factory('destinations', $destId)->as_array();
            $destInfo = Model_Tongyong::get_dest_info($destId, $this->typeid);

        }
        $channel_info = Model_Nav::get_channel_info($this->typeid);
        $channel_name = empty($channel_info['seotitle']) ? $channel_info['shortname'] : $channel_info['seotitle'];
        $seo_params = array(
            'typeid' => $this->typeid,
            'destpy' => $destPy,
            'attrid' => $attrId,
            'keyword' => $keyword,
            'p' => $p,
            'channel_name' => $channel_name
        );
        $chooseitem = Model_Tongyong::get_selected_item($route_array);
        $search_title = Model_Tongyong::gen_seotitle($seo_params);
        $seoinfo = Model_Nav::get_channel_seo($this->typeid);
        $this->assign('searchtitle', $search_title);
        $this->assign('seoinfo', $seoinfo);
        $this->assign('destid', $destId);
        $this->assign('destinfo', $destInfo);
        $this->assign('list', $out['list']);
        $this->assign('chooseitem', $chooseitem);

        $this->assign('param', $route_array);
        $this->assign('currentpage', $p);
        $this->assign('pageinfo', $pager);

        $templet = St_Functions::get_list_dest_template_pc($this->typeid, $destId);
        $templet = empty($templet) ? Product::get_use_templet($this->pinyin . '_list') : $templet;
        $templet = $templet ? $templet : 'tongyong/list';
        $this->display($templet);
        //缓存内容
        $content = $this->response->body();
        Common::cache('set', $this->_cache_key, $content);
    }

    //预订
    public function action_book()
    {
        //会员信息
        $userInfo = Product::get_login_user_info();
        //要求预订前必须登陆
        if (!empty($GLOBALS['cfg_login_order']) && empty($userInfo['mid'])) {
            $this->request->redirect(Common::get_main_host() . '/member/login/?redirecturl=' . urlencode(Common::get_current_url()));
        }
        $productId = intval(Arr::get($_GET, 'productid'));
        $suitId = intval(Arr::get($_GET, 'suitid'));
        //如果参数为空,则返回上级页面
        if (empty($productId) || empty($suitId)) {
            $this->request->redirect($this->request->referrer());
        }

        //产品信息
        $info = Model_Tongyong::detail_id($productId);

        $model_info = DB::select()->from('model')->where('id', '=', $this->typeid)->execute()->current();
        $info['url'] = Common::get_web_url($info['webid']) . "/{$model_info['pinyin']}/show_{$info['aid']}.html";
        $info['price'] = Model_Tongyong::get_minprice($productId, $suitId);
        //产品编号
        $info['series'] = Product::product_number($info['id'], $this->typeid);
        //最新时间
        $ticket = DB::select('day')->from('model_suit_price')->where('suitid' . '=' . $suitId)->and_where('productid', '=', $productId)->and_where('day', '>=', strtotime(date('Y-m-d 0:0:0')))->and_where('number', '!=', 0)->order_by('day', 'asc')->execute()->current();
        $useDate = !empty($ticket) ? $ticket['day'] : time();
        $info['usedate'] = date('Y-m-d', $useDate);
        //套餐信息
        $suitInfo = Model_Tongyong::suit_info($suitId);

        //frmcode
        $code = md5(time());
        Common::session('code', $code);
        //积分抵现所需积分
        $jifentprice_info = Model_Jifen_Price::get_used_jifentprice($info['jifentprice_id'], $this->typeid);

        $userInfo = Product::get_login_user_info();

        $this->assign('info', $info);
        $this->assign('userInfo', $userInfo);
        $this->assign('suitInfo', $suitInfo);
        $this->assign('jifentprice_info', $jifentprice_info);
        $this->assign('frmcode', $code);
        $this->display('tongyong/book');
    }

    /**
     * 套餐当天价格
     */
    public function action_suit_day_price()
    {
        $inputdate = Arr::get($_GET, 'inputdate');
        $productid = Arr::get($_GET, 'productid');
        $suitid = Arr::get($_GET, 'suitid');
        $info = Model_Tongyong::current_price($suitid, $productid, strtotime($inputdate));
        $price = !empty($info) ? $info['price'] : 0;
        echo json_encode(array('price' => $price));
    }

    /**
     * 日历报价
     */
    public function action_dialog_calendar()
    {

        $suitid = Arr::get($_POST, 'suitid');
        $year = Arr::get($_POST, 'year');
        $month = Arr::get($_POST, 'month');
        $containdiv = Arr::get($_POST, 'containdiv');
        $nowDate = new DateTime();
        $year = !empty($year) ? $year : $nowDate->format('Y');
        $month = !empty($month) ? $month : $nowDate->format('m');
        $out = '';
        $priceArr = Model_Tongyong::get_suit_price($year, $month, $suitid);
        $out .= Common::calender($year, $month, $priceArr, $suitid, $containdiv);
        echo $out;
    }

    //保存订单
    public function action_create()
    {

        $frmCode = Arr::get($_POST, 'frmcode');
        $checkCode = strtolower(Arr::get($_POST, 'checkcode'));
        //验证码验证
        if (!Captcha::valid($checkCode) || empty($checkCode)) {
            exit();
        }
        //安全校验码验证
        $orgCode = Common::session('code');
        if ($orgCode != $frmCode) {
            exit();
        }

        //会员信息
        $userInfo = Product::get_login_user_info();

        $memberId = $userInfo ? $userInfo['mid'] : 0;//会员id
        $webid = intval(Arr::get($_POST, 'webid'));//网站id
        $dingNum = intval(Arr::get($_POST, 'dingnum'));//数量
        $suitId = intval(Arr::get($_POST, 'suitid'));//套餐id
        $productId = intval(Arr::get($_POST, 'productid'));//产品id
        $useDate = Arr::get($_POST, 'usedate');//使用日期

        $linkMan = Arr::get($_POST, 'linkman');//联系人姓名
        $linkTel = Arr::get($_POST, 'linktel');//联系人电话
        $linkEmail = Arr::get($_POST, 'linkemail');//联系人邮箱
        $remark = Arr::get($_POST, 'remark');//订单备注
        $receiver_address_id = 0;
        if ($userInfo['mid']) {
            $receiver_address_id = intval(Arr::get($_POST, 'receive_address_id'));
            if (!Model_Member_Address::check_address_id($userInfo['mid'], $receiver_address_id)) {
                $receiver_address_id = 0;
            }
        }


        $payType = Arr::get($_POST, 'paytype');//支付方式
        $needJifen = intval($_POST['needjifen']);
        //检测订单有效性
        $check_result = common::before_order_check(array('model' => $this->pinyin, 'productid' => $productId, 'suitid' => $suitId, 'day' => strtotime($useDate)));
        $check_result['price'] = Currency_Tool::price($check_result['price']);
        if (!$check_result) {
            $this->request->redirect('/tips/order');
        };
        //产品信息
        $info = Model_Tongyong::detail_id($productId);

        $orderSn = Product::get_ordersn($this->typeid);

        $suitInfo = Model_Tongyong::suit_info($suitId);//套餐信息

        $suitInfo['ourprice'] = $check_result['price'];
        //判断积分使用是否满足需求.
        //积分抵现.
        $jifentprice = 0;
        $useJifen = 0;
        if ($needJifen) {
            $jifentprice = Model_Jifen_Price::calculate_jifentprice($info['jifentprice_id'], $this->typeid, $needJifen, $userInfo);
            $useJifen = empty($jifentprice) ? 0 : 1;
            $needJifen = empty($jifentprice) ? 0 : $needJifen;
        }
        //积分评论
        $jifencomment_info = Model_Jifen::get_used_jifencomment($this->typeid);
        $jifencomment = empty($jifencomment_info) ? 0 : $jifencomment_info['value'];

        //订单状态(全款支付,定金支付,二次确认)
        $status = $suitInfo['paytype'] != 3 ? 1 : 0;

        //判断库存
        if (!Model_Tongyong::check_storage(0, $dingNum, $suitId, $useDate)) {
            exit('storage is not enough!');
        }
        $arr = array(
            'ordersn' => $orderSn,
            'webid' => $webid,
            'typeid' => $this->typeid,
            'productautoid' => $info['id'],
            'productaid' => $info['aid'],
            'productname' => $info['title'],
            'price' => $suitInfo['ourprice'],
            'usedate' => $useDate,
            'dingnum' => $dingNum,
            'departdate' => '',

            'linkman' => $linkMan,
            'linktel' => $linkTel,
            'linkemail' => $linkEmail,
            'jifentprice' => $jifentprice,
            'jifenbook' => $info['jifenbook_id'],
            'jifencomment' => $jifencomment,
            'addtime' => time(),
            'memberid' => $memberId,
            'dingjin' => $suitInfo['dingjin'],
            'paytype' => $suitInfo['paytype'],
            'suitid' => $suitId,
            'usejifen' => $useJifen,
            'needjifen' => $needJifen,

            'status' => $status,
            'remark' => $remark,
            'isneedpiao' => 0,
            'receiver_address_id' => $receiver_address_id

        );


        /*--------------------------------------------------------------优惠券信息------------------------------------------------------------*/
        //优惠券判断
        $croleid = intval(Arr::get($_POST, 'couponid'));
        if ($croleid) {
            $cid = DB::select('cid')->from('member_coupon')->where("id=$croleid")->execute()->current();
            $totalprice = Model_Coupon::get_order_totalprice($arr);
            $ischeck = Model_Coupon::check_samount($croleid, $totalprice, $this->typeid, $info['id'], $useDate);
            if ($ischeck['status'] == 1) {
                Model_Coupon::add_coupon_order($cid, $orderSn, $totalprice, $ischeck, $croleid); //添加订单优惠券信息
            }
            else {
                exit('coupon  verification failed!');//优惠券不满足条件
            }
        }
        /*-----------------------------------------------------------------优惠券信息--------------------------------------*/


        //添加订单
        if (ST_Product::add_order($arr, 'Model_Model_Archive', $arr)) {
            //$orderInfo = Model_Member_Order::get_order_by_ordersn($orderSn);
            Common::session('_platform', 'pc');
            //这里作判断是跳转到订单查询页面

            $payurl = Common::get_main_host() . "/payment/?ordersn=" . $orderSn;
            $this->request->redirect($payurl);

        }
    }

    //判断库存
    public function action_ajax_check_storage()
    {

        $dingnum = $_POST['dingnum'];
        $suitid = $_POST['suitid'];
        $startdate = $_POST['startdate'];
        $status = Model_Tongyong::check_storage(0, $dingnum, $suitid, $startdate);
        echo json_encode(array('status' => $status));
    }


}