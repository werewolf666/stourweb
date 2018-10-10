<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Tongyong
 * 通用控制器
 */
class Controller_Tongyong extends Stourweb_Controller
{
    protected $typeid = 0;   //产品类型
    protected $pinyin = 0;

    public function before()
    {
        parent::before();
        $channelname = Model_Nav::get_channel_name_mobile($this->typeid);
        $this->assign('typeid', $this->typeid);
        $this->assign('pinyin', $this->pinyin);
        $this->assign('channelname', $channelname);
    }

    /**
     * 首页
     */
    public function action_index()
    {
        $this->request->redirect($this->pinyin . '/list');
    }

    public
    function action_ajax_searchnav()
    {
        $this->assign('typeid', $this->typeid);
        $this->display('tongyong/searchnav');
    }

    public function action_list()
    {
        $seoinfo = Model_Nav::get_channel_seo_mobile($this->typeid);
        $keyword = $_GET['keyword'];
        $destpy = $this->request->param('destpy');
        $sorttype = $this->request->param('sorttype');
        $attrid = $this->request->param('attrid');
        $page = $this->request->param('p');
        $destname = '目的地';
        $destid = 0;

        if (!empty($destpy)) {
            $destInfo = DB::select()->from('destinations')->where('pinyin', '=', $destpy)->execute()->current();
            if ($destInfo['id']) {
                $destid = $destInfo['id'];
                $destname = $destInfo['kindname'];
            }
        }

        //获取seo信息
        // $seo = Model_Model::search_seo($destpy);
        $seo_params = array(
            'typeid' => $this->typeid,
            'destpy' => $destpy,
            'attrid' => $attrid,
            'keyword' => $keyword,
            'p' => $page
        );
        $search_title = Model_Tongyong::gen_seotitle($seo_params);
        $this->assign('search_title', $search_title);
        $seo = array(); //需要修改seo信息
        $this->assign('seoinfo', $seo);
        $this->assign('destpy', Common::remove_xss($destpy));
        $this->assign('destname', $destname);
        $this->assign('destid', $destid);
        $this->assign('sorttype', $sorttype);
        $this->assign('attrid', $attrid);
        $this->assign('page', $page);
        $this->assign('seoinfo', $seoinfo);
        $this->assign('keyword', $keyword);
        $this->assign('typeid', $this->typeid);
        $this->display('tongyong/index', $this->pinyin . '_list');
    }

    /**
     * 详细页
     */
    public function action_show()
    {
        $aid = $this->request->param('aid');
        $webid = Arr::get($_GET, 'webid');
        $webid = $webid ? $webid : 0;
        $aid = $this->request->param('aid');
        $info = Model_Tongyong::detail($aid, $this->typeid, $webid);
        if (empty($info)) {
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
        $info['score'] = St_Functions::get_satisfy($this->typeid, $info['id'], $info['satisfyscore']);
        $info['satisfyscore'] = St_Functions::get_satisfy($this->typeid, $info['id'], $info['satisfyscore']);
        //点评数
        $info['commentnum'] = Model_Comment::get_comment_num($info['id'], $this->typeid);
        //销售数量
        $info['sellnum'] = Model_Member_Order::get_sell_num($info['id'], $this->typeid);
        //产品编号
        $info['series'] = Product::product_number($info['id'], $this->typeid);
        //产品图标
        $info['iconlist'] = Product::get_ico_list($info['iconlist']);

        $info['content'] = Product::strip_style($info['content']);
        //支付方式

        $info['jifentprice_info'] = Model_Jifen_Price::get_used_jifentprice($info['jifentprice_id'], $this->typeid);
        $info['jifenbook_info'] = Model_Jifen::get_used_jifenbook($info['jifenbook_id'], $this->typeid);
        $info['jifencomment_info'] = Model_Jifen::get_used_jifencomment($this->typeid);

        //扩展字段信息
        $extend_info = Model_Tongyong::extend($info['id'], $this->typeid);
        $minsuit = $this->getMinSuit($info['id']);

        $this->assign('minsuit', $minsuit);
        $this->assign('seoinfo', $seoInfo);
        $this->assign('info', $info);
        $this->assign('extendinfo', $extend_info);
        $this->display('tongyong/show', $this->pinyin . '_show');
    }

    /**
     * 预订
     */
    public function action_book()
    {
        $userinfo = Common::session('member');
        $userinfo = Model_Member::get_member_byid($userinfo['mid']);
        //要求预订前必须登陆
        if (!empty($GLOBALS['cfg_login_order']) && empty($userinfo['mid'])) {
            $this->request->redirect(Common::get_main_host() . '/phone/member/login?redirecturl=' . urlencode(Common::get_current_url()));
        }
        $id = intval($this->params['id']);
        $model = ORM::factory('model_archive', $id);
        if (!$model->loaded()) {
            exit('产品ID不存在');
        }
        $info = $model->as_array();
        $info['bookNumber'] = 0;
        $jifentprice_info = Model_Jifen_Price::get_used_jifentprice($info['jifentprice_id'], $this->typeid);
        $this->assign('jifentprice_info', $jifentprice_info);
        $member = Common::session('member');
        if (!empty($member)) {
            $this->assign('member', Model_Member::get_member_byid($member['mid']));
        }
        $this->assign('userinfo', $userinfo);
        $this->assign('info', $info);
        $this->display('tongyong/book');
    }

    public function action_ajax_more()
    {
        $page = $_POST['page'];
        $typeid = $_POST['typeid'];
        $attrid = $_POST['attrid'];
        $destpy = $_POST['destpy'];
        $sorttype = $_POST['sorttype'];
        $keyword = $_POST['keyword'];
        $page = empty($page) ? 1 : $page;
        $pagesize = 10;
        $offset = $pagesize * ($page - 1);

        $where = ' WHERE a.ishidden=0 and a.typeid=' . $typeid . ' ';

        if (!empty($destpy)) {
            $destid = DB::select('id')->from('destinations')->where('pinyin', '=', $destpy)->execute()->get('id');
        }

        //排序
        $orderBy = "";
        if (!empty($sorttype)) {
            if ($sorttype == 1)//价格升序
            {
                $orderBy = "  --a.price ASC,";
            }
            else if ($sorttype == 2) //价格降序
            {
                $orderBy = "  --a.price DESC,";
            }
            else if ($sorttype == 3) //销量降序
            {
                $orderBy = " a.shownum DESC,";
            }
            else if ($sorttype == 4)//推荐
            {
                $orderBy = " a.shownum DESC,";
            }
        }

        //关键词
        if (!empty($keyword)) {
            $where .= " AND a.title like '%$keyword%' ";
        }
        //按属性
        if (!empty($attrid)) {
            $where .= Product::get_attr_where($attrid);
        }
        if (!empty($destid)) {
            $where .= " AND FIND_IN_SET($destid,a.kindlist) ";
        }
        //如果选择了目的地
        if (!empty($destid)) {
            $sql = "SELECT a.* FROM `sline_model_archive` a ";
            $sql .= "LEFT JOIN `sline_kindorderlist` b ";
            $sql .= "ON (a.id=b.aid AND b.typeid=$typeid AND b.classid=$destid)";
            $sql .= $where;
            $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,{$orderBy}a.modtime DESC,a.addtime DESC ";

        }
        else {
            $sql = "SELECT a.* FROM `sline_model_archive` a ";
            $sql .= "LEFT JOIN `sline_allorderlist` b ";
            $sql .= "ON (a.id=b.aid AND b.typeid=$typeid )";
            $sql .= $where;
            $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,{$orderBy}a.modtime DESC,a.addtime DESC ";

        }

        //计算总数
        $totalSql = "SELECT count(*) as num " . strchr($sql, " FROM");
        $totalSql = str_replace(strchr($totalSql, "ORDER BY"), '', $totalSql);//去掉order by

        $totalNum = DB::query(Database::SELECT, $totalSql)->execute()->get('num');
        $hasmore = $totalNum > $pagesize * $page ? true : false;

        $sql .= "LIMIT {$offset},{$pagesize}";

        $arr = DB::query(1, $sql)->execute()->as_array();

        $model_info = ORM::factory('model', $typeid);
        foreach ($arr as &$v) {
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'], $typeid); //评论次数
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], $typeid); //销售数量
            $v['score'] = $v['satisfyscore'] . '%';
            $v['price'] = Model_Tongyong::get_minprice($v['id'], array('info' => $v));//最低价
            $v['attrlist'] = Model_Model_Attr::get_attr_list($v['attrid'], $typeid);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/{$model_info->pinyin}/show_{$v['aid']}.html";
            $v['iconlist'] = Product::get_ico_list($v['iconlist']);
            $v['litpic'] = Common::img($v['litpic'], 220, 150);
        }
        $out = array(
            'total' => $totalNum,
            'list' => $arr,
            'hasmore' => $hasmore
        );
        echo json_encode($out);
    }

    //检测库存
    public function action_ajax_check_stock()
    {
        if ($_POST['productid'] || $_POST['userdate'] || $_POST['suitid']) {
            echo 0;
        }
        $data = DB::select()->from('model_suit_price')->where('productid', '=', intval($_POST['productid']))->and_where('day', '=', strtotime($_POST['userdate']))->and_where('suitid', '=', $_POST['suitid'])->execute()->current();
        if ($data) {
            echo $data['number'];
        }
        else {
            echo 0;
        }
    }

    public function action_create()
    {
        St_Product::token_check($_POST) or Common::order_status();
        //套餐id
        $refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;

        $suitid = Arr::get($_POST, 'suitid');
        //联系人
        $linkman = Arr::get($_POST, 'linkman');
        //手机号
        $linktel = Arr::get($_POST, 'linktel');
        $linkidcard = Arr::get($_POST, 'linkidcard');
        //备注信息
        $remark = Arr::get($_POST, 'remark');
        //产品id
        $id = Arr::get($_POST, 'productid');
        //使用时间
        $usedate = Arr::get($_POST, 'usedate');
        //预订数量
        $dingnum = Arr::get($_POST, 'dingnum');

        $needJifen = $_POST['needjifen'];
        //验证部分

        $validataion = Validation::factory($_POST);

        $validataion->rule('linktel', 'not_empty');

        $validataion->rule('linktel', 'phone');

        $validataion->rule('linkman', 'not_empty');


        if (!$validataion->check()) {

            $error = $validataion->errors();

            $keys = array_keys($validataion->errors());

            Common::message(array('message' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), 'jumpUrl' => $refer_url));

        }


        $info = ORM::factory('model_archive')->where("id=$id")->find()->as_array();

        $suitArr = ORM::factory('model_suit')
            ->where("id=:suitid")
            ->param(':suitid', $suitid)
            ->find()
            ->as_array();

        $suitArr['dingjin'] = Currency_Tool::price($suitArr['dingjin']);
        $suitArr['ourprice'] = Currency_Tool::price($suitArr['ourprice']);


        $price_info = DB::select()->from('model_suit_price')
            ->and_where('suitid', '=', $suitid)
            ->and_where('day', '=', strtotime($usedate))->execute()->current();
        $price_info['price'] = Currency_Tool::price($price_info['price']);


        if (!Model_Tongyong::check_storage(0, $dingnum, $suitid, $usedate)) {
            exit('storage is not enough!');
        }

        if ($suitArr['paytype'] == '3')//这里补充一个当为二次确认时,修改订单为未处理状态.
        {
            $info['status'] = 0;

        }
        else {

            $info['status'] = 1;

        }

        $info['name'] = $info['title'] . "({$suitArr['suitname']})";

        $info['paytype'] = $suitArr['paytype'];

        $info['dingjin'] = doubleval($suitArr['dingjin']);


        $info['ourprice'] = doubleval($price_info['price']);

        $info['childprice'] = 0;

        $info['usedate'] = $usedate;

        $ordersn = Product::get_ordersn($this->typeid);

        //积分抵现.
        $userInfo = Common::session('member');
        $userInfo = Model_Member::get_member_byid($userInfo['mid']);
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


        $arr = array(

            'ordersn' => $ordersn,

            'webid' => 0,

            'typeid' => $this->typeid,

            'productautoid' => $id,

            'productaid' => $info['aid'],

            'productname' => $info['name'],

            'litpic' => $info['litpic'],

            'price' => $info['ourprice'],

            'childprice' => $info['childprice'],

            'jifentprice' => $jifentprice,
            'jifenbook' => $info['jifenbook_id'],
            'jifencomment' => $jifencomment,

            'paytype' => $info['paytype'],

            'dingjin' => $info['dingjin'],

            'usedate' => $info['usedate'],

            'departdate' => '',

            'addtime' => time(),

            'memberid' => null,

            'dingnum' => $dingnum,

            'childnum' => 0,

            'oldprice' => 0,

            'oldnum' => 0,

            'linkman' => $linkman,

            'linktel' => $linktel,

            'linkidcard' => $linkidcard,

            'suitid' => $suitid,

            'remark' => $remark,

            'status' => $info['status'] ? $info['status'] : 0,

            'usejifen' => $useJifen,
            'needjifen' => $needJifen,
            'receiver_address_id' => $_POST["address"] ? (int)$_POST['address'] : 0

        );


        /*--------------------------------------------------------------优惠券信息------------------------------------------------------------*/
        //优惠券判断
        $croleid = intval(Arr::get($_POST, 'couponid'));
        if ($croleid) {
            $cid = DB::select('cid')->from('member_coupon')->where("id=$croleid")->execute()->current();
            $totalprice = Model_Coupon::get_order_totalprice($arr);
            $ischeck = Model_Coupon::check_samount($croleid, $totalprice, 1, $info['id'], $usedate);
            if ($ischeck['status'] == 1) {
                Model_Coupon::add_coupon_order($cid, $ordersn, $totalprice, $ischeck, $croleid); //添加订单优惠券信息
            }
            else {
                exit('coupon  verification failed!');//优惠券不满足条件
            }
        }
        /*-----------------------------------------------------------------优惠券信息--------------------------------------*/


        //添加订单

        if (ST_Product::add_order($arr, 'Model_Model_Archive', $arr)) {
            St_Product::delete_token();
            $orderInfo = Model_Member_Order::get_order_by_ordersn($ordersn);
            Model_Member_Order::add_tourer($orderInfo['id'], $_POST);
            //如果是立即支付则执行支付操作,否则跳转到产品详情页面

            $html = Common::payment_from(array('ordersn' => $ordersn));
            if ($html) {
                echo $html;
            }

        }
    }

    /*
 * 选择目的地
 */
    public
    function action_ajax_get_next_dests()
    {
        $destpy = $_POST['destpy'];
        $typeid = $_POST['typeid'];
        $isparent = $_POST['isparent'];
        $destpy = empty($destpy) ? 'all' : $destpy;
        $dest_info = array('id' => '0', 'kindname' => '目的地', 'pinyin' => 'all');
        $pid = 0;
        if ($destpy != 'all') {
            $dest_info = DB::select()->from('destinations')->where('pinyin', '=', $destpy)->execute()->current();
            $subnum = DB::select(array(DB::expr("count(*)"), 'num'))->from('destinations')->where('pid', '=', $dest_info['id'])->and_where('isopen', '=', 1)->and_where(DB::expr("FIND_IN_SET({$typeid},opentypeids)"), '>', 0)->execute()->get('num');
            $pid = $isparent == 1 || $subnum <= 0 ? $dest_info['pid'] : $dest_info['id'];
        }
        $parents = null;
        if ($pid != 0) {
            $parents = Model_Destinations::get_parents($pid);
            $parents = array_reverse($parents);
            $parents[] = $dest_info;
        }
        $list = DB::select('id', 'pinyin', 'kindname')->from('destinations')->where('isopen', '=', 1)->and_where('pid', '=', $pid)->and_where(DB::expr("FIND_IN_SET({$typeid},opentypeids)"), '>', 0)->execute()->as_array();
        foreach ($list as &$child) {
            $child['subnum'] = DB::select(array(DB::expr("count(*)"), 'num'))->from('destinations')->where('pid', '=', $child['id'])->and_where('isopen', '=', 1)->and_where(DB::expr("FIND_IN_SET({$typeid},opentypeids)"), '>', 0)->execute()->get('num');
        }
        $parent = DB::select('id', 'kindname', 'pinyin')->from('destinations')->where('id', '=', $pid)->execute()->current();
        echo json_encode(array('status' => true, 'list' => $list, 'parents' => $parents, 'parent' => $parent));
    }

    public function getMinSuit($id)
    {
        $suitModel = ORM::factory('model_suit')->where('productid', '=', $id)->order_by('ourprice', 'asc')->find();
        if (!$suitModel->loaded())
            return null;
        return $suitModel->as_array();
    }

    /**
     * 当前产品的套餐价格
     */
    public function action_ajax_current_suit()
    {
        $suitid = Arr::get($_GET, 'suitid');
        $use_date = Arr::get($_GET, 'usedate');
        $productid = Arr::get($_GET, 'productid');
        $list = Model_Tongyong::current_suit_price($productid, $suitid, $use_date);
        //$bool=empty($list)?false:true;
        if (empty($list)) {
            $data = array('result' => false);
        }
        else {
            $data = array('result' => true, 'price' => $list['price']);
        }
        echo json_encode($data);
    }
}