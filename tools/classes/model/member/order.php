<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 订单管理
 * Class Order
 */
Class Model_Member_Order extends ORM
{
    /**
     * 订单状态
     * @var array
     */
    public static $order_status = array(
        0 => '等待处理',
        1 => '等待付款',
        2 => '付款成功',
        3 => '订单取消',
        4 => '已退款',
        5 => '交易完成',
        6 => '退款确认'
    );
    public static $paytype_names = array('', '全款支付', '定金支付', '二次确认支付', '线下支付');

    /**
     * @function 获取订单状态的中文名称
     * @param $status 状态数字
     * @return string
     */
    public static function get_status_name($status)
    {

        return self::$orderStatus[$status];
    }

    /**
     * @function 通过Ordersn获取订单信息
     * @param $ordersn
     * @return array
     * @throws Kohana_Exception
     */
    public static function get_order_by_ordersn($ordersn)
    {
        $row = ORM::factory('member_order')
            ->where("ordersn='$ordersn'")
            ->find()
            ->as_array();
        return $row;
    }

    /**
     * @function 通过Ordersn获取订单信息并计算其总价
     * @param $ordersn
     * @param null $memberid
     * @return mixed
     */
    public static function order_info($ordersn, $memberid = null)
    {
        $memberWhere = !is_null($memberid) ? " and memberid={$memberid} " : '';
        $sql = "SELECT * FROM `sline_member_order` WHERE ordersn='{$ordersn}' {$memberWhere} ORDER BY id DESC LIMIT 1 ";
        $rs = DB::query(Database::SELECT, $sql)->execute()->current();
        $rs['totalprice'] = self::order_total_price($rs['id'], $rs);
        $rs['paytype_name'] = self::get_paytype_name($rs['paytype']);
        $rs['privileg_price'] = self::order_privileg_price($rs['id'], $rs);
        $rs['actual_price'] = $rs['totalprice'] - $rs['privileg_price'];
        $rs['statusname'] = self::$order_status[$rs['status']];
        $model = ORM::factory('model', $rs['typeid']);
        $table = $model->maintable;
        if ($table && class_exists('Model_' . $table)) {
            $infoModel = 'Model_' . $table;
            if (is_callable(array($infoModel, 'pay_status'))) {
                $infoModel = new $infoModel();
                $status = $infoModel::pay_status();
                foreach ($status as $v) {
                    if ($v['status'] == $rs['status']) {
                        $rs['statusname'] = $v['status_name'];
                        break;
                    }
                }
            }
        }
        if (St_Functions::is_normal_app_install('coupon')) {

            $rs['iscoupon'] = Model_Coupon::order_view($ordersn);
            $rs['cmoney'] = $rs['iscoupon']['cmoney'];
        }
        if($rs['online_transaction_no'])
        {
            $pay_online = json_decode($rs['online_transaction_no'],true);
            if($pay_online['source']=='wxpay'||$pay_online['source']=='alipay')
            {
                $rs['pay_online'] = 1;
                $rs['pay_online_source'] = $pay_online['source'];
            }
        }


        //支付金额写入数组
        $rs['payprice'] = self::order_total_payprice($rs['id'], $rs);
        return $rs;
    }

    /**
     * @function 通过order_id获取订单总价
     * @param $orderid
     * @param null $orderInfo
     * @return float
     */
    public static function order_total_payprice($orderid, $orderInfo = null)
    {
        if (is_null($orderInfo)) {
            $rs = DB::select()->from('member_order')->where('id', '=', $orderid)->execute()->current();
        } else {
            $rs = $orderInfo;
        }

        $num = $rs['dingnum'] + $rs['childnum'] + $rs['oldnum'];
        if (doubleval($rs['dingjin']) > 0 && $rs['paytype'] == 2) {
            //定金支付
            $total = doubleval($rs['dingjin']) * $num;
        } else if ($rs['typeid'] != 2) {
            //全额支付
            $total = $rs['dingnum'] * $rs['price'] + $rs['childnum'] * $rs['childprice'] + $rs['oldnum'] * $rs['oldprice'];
        } else {
            $total = abs($rs['dingnum']) * $rs['price'];
        }
        //单房差
        if ($rs['roombalancenum'] && $rs['roombalance'] && $rs['roombalance_paytype'] == 1) {
            $total = $total + doubleval($rs['roombalance']) * intval($rs['roombalancenum']);
        }
        $rs['totalprice'] = $total;//订单金额
        //积分抵现
        if (intval($rs['usejifen']) === 1) {
            $total = $total - $rs['jifentprice'];
        }
        //全款支付，支付金额应该减去优惠券金额
        if ($rs['paytype'] == 1 || $rs['paytype'] == 3) {
            if (St_Functions::is_normal_app_install('coupon')) {
                $info['iscoupon'] = Model_Coupon::order_view($rs['ordersn']);
                $total -= $info['iscoupon']['cmoney'];
            }
        }

        return $total;
    }

    /**
     * @function 通过order_id获取订单总金额
     * @param $orderid
     * @param null $orderInfo
     * @return mixed
     */
    public static function order_total_price($orderid, $orderInfo = null)
    {
        if (empty($orderInfo)) {
            $rs = DB::select()->from('member_order')->where('id', '=', $orderid)->execute()->current();
        } else {
            $rs = $orderInfo;
        }
        $total = $rs['dingnum'] * $rs['price'] + $rs['childnum'] * $rs['childprice'] + $rs['oldnum'] * $rs['oldprice'];
        //单房差

        if ($rs['roombalancenum'] && $rs['roombalance'] && $rs['roombalance_paytype'] == 1) {
            $total += doubleval($rs['roombalance']) * intval($rs['roombalancenum']);
        }

        //是否使用了优惠券
        // if(St_Functions::is_normal_app_install('coupon'))
        //  {
        //      $iscoupon = Model_Coupon::order_view($rs['ordersn']);
        //      $info['cmoney'] = $iscoupon['cmoney'];
        //      $total -=$info['cmoney'];
        //  }

        return $total;
    }

    /**
     * @function 获取订单的优惠总额，包括优惠券、积分抵现等
     * @param $orderid
     * @param null $orderInfo
     */
    public static function order_privileg_price($orderid, $orderInfo = null)
    {
        if (empty($orderInfo)) {
            $orderInfo = DB::select()->from('member_order')->where('id', '=', $orderid)->execute()->current();
        }
        $privileg_price = 0;
        if (St_Functions::is_normal_app_install('coupon')) {
            $iscoupon = Model_Coupon::order_view($orderInfo['ordersn']);
            $privileg_price += $iscoupon['cmoney'];
        }
        if ($orderInfo['usejifen'] == 1 && $orderInfo['jifentprice']) {
            $privileg_price += $orderInfo['jifentprice'];
        }
        return $privileg_price;
    }


    /**
     * @function 通过order_id获取订单信息
     * @param $orderid
     * @return array
     */
    public static function get_order_detail($orderid, $memberid = 0)
    {
        $query = DB::select()->from('member_order')->where('id', '=', $orderid);
        if (!empty($memberid)) {
            $query->and_where('memberid', '=', $memberid);
        }
        $row = $query->execute()->current();
        return $row;
    }

    /**
     * @function 获取产品销售量
     * @param int $id
     * @param $typeid
     * @return int
     */
    public static function get_sell_num($id = 0, $typeid)
    {
        $where = empty($id) ? "typeid='$typeid'" : "productautoid='$id' and typeid='$typeid'";
        $sql = "SELECT COUNT(*) as dd FROM `sline_member_order` WHERE $where";
        $ar = DB::query(1, $sql)->execute()->as_array();
        return $ar[0]['dd'] ? $ar[0]['dd'] : 0;
    }

    /**
     * @function 获取订单列表
     * @param $typeid
     * @param $mid
     * @param $ordertype
     * @param $currentpage
     * @param string $pagesize
     * @param string $linktel
     * @return array
     */
    public static function order_list($typeid, $mid, $ordertype, $currentpage, $pagesize = '10', $linktel = '')
    {
        $page = $currentpage ? $currentpage : 1;
        $offset = (intval($page) - 1) * $pagesize;

        $value_arr = array();
        if (!empty($mid)) {
            $where = "WHERE a.memberid='$mid'";
        } else {
            $where = "WHERE a.memberid>0";
        }
        if (!empty($typeid)) {
            $where .= " AND a.typeid=$typeid";
        }
        switch ($ordertype) {
            //全部订单
            case 'all':
                break;
            //未付款订单
            case 'unpay':
                $where .= " AND a.status=1";
                break;
            //未点评订单(需要交易完成后才能点评)
            case 'uncomment':
                $where .= " AND a.status=5 AND a.ispinlun=0";
                break;
        }
        //模块
        $typeids = array();
        $model = DB::select('id')->from('model')->execute()->as_array();
        foreach ($model as $v) {
            array_push($typeids, $v['id']);
        }
        if ($typeids) {
            $typeids = implode(',', $typeids);
            $where .= " and typeid in($typeids)";
        }
        //按手机号进行查询
        if (!empty($linktel)) {
            $where .= " AND (a.linktel=:linktel OR b.mobile=:linktel)";
            $value_arr[':linktel'] = $linktel;
        }
        $sql = "SELECT a.* FROM `sline_member_order` a LEFT JOIN `sline_member` b ON(a.memberid=b.mid)  $where   ORDER BY a.addtime DESC ";
        //计算总数
        $totalSql = "SELECT count(*) as dd " . strchr($sql, " FROM");
        $totalSql = str_replace(strchr($totalSql, "ORDER BY"), '', $totalSql);//去掉order by
        $totalN = DB::query(1, $totalSql)->parameters($value_arr)->execute()->as_array();
        $totalNum = $totalN[0]['dd'] ? $totalN[0]['dd'] : 0;
        $sql .= "LIMIT {$offset},{$pagesize}";
        $arr = DB::query(1, $sql)->parameters($value_arr)->execute()->as_array();
        foreach ($arr as &$v) {
            $orderInfo = self::order_info($v['ordersn']);
            $productInfo = St_Product::get_product_info($v['typeid'], $v['productautoid']);
            $model = ORM::factory('model', $v['typeid']);
            $table = $model->maintable;
            $v['statusname'] = self::$order_status[$v['status']];
            if ($table && class_exists('Model_' . $table)) {
                $infoModel = 'Model_' . $table;
                if (is_callable(array($infoModel, 'pay_status'))) {
                    $infoModel = new $infoModel();
                    $status = $infoModel::pay_status();
                    foreach ($status as $s) {
                        if ($s['status'] == $v['status']) {
                            $v['statusname'] = $s['status_name'];
                            break;
                        }
                    }
                }
            }
            $v['producturl'] = $productInfo['url'];
            $v['litpic'] = $productInfo['litpic'];
            $v['totalprice'] = $orderInfo['payprice'];


            $v['price'] = $v['price'] == '0' ? $v['childprice'] : $v['price'];
        }
        $out = array(
            'total' => $totalNum,
            'list' => $arr
        );
        return $out;
    }

    /**
     * @function 根据order_id获取该订单的所有评论
     * @param $orderid
     * @return mixed
     */
    public static function get_comment($orderid)
    {
        $sql = "SELECT * FROM sline_comment ";
        $sql .= "WHERE orderid={$orderid} ";
        $sql .= "ORDER BY id DESC";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * @function 根据order_id获取积分奖励及订单价格
     * @param $orderid
     * @return array
     */
    public static function get_award_info($orderid)
    {
        $result = DB::select('jifenbook', 'jifentprice', 'jifencomment', 'price')->from('member_order')->where('id', '=', $orderid)->execute()->current();
        return $result;
    }

    /**
     * @function 监听订单处理
     * @param $ordersn
     * @return bool
     */
    public static function detect($ordersn)
    {
        $sql = "select * from sline_member_order where ordersn='{$ordersn}'";
        $orderlist = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($orderlist) <= 0) {
            self::write_order_listener_log($ordersn, "query_order", "no order,sql:{$sql}");
            return true;
        }
        $sql = <<<sql
        SELECT
            *
        FROM
            sline_member_order_listener
        WHERE
            (webid IS NULL OR webid = {$orderlist[0]['webid']})
        AND (typeid IS NULL OR typeid = {$orderlist[0]['typeid']})
        AND (
            supplierlist IS NULL
            OR supplierlist = ''
            OR supplierlist = '{$orderlist[0]['supplierlist']}'
        )
        AND (
            distributor IS NULL
            OR distributor = ''
            OR distributor = '{$orderlist[0]['distributor']}'
        )
        AND (
            productautoid IS NULL
            OR productautoid = {$orderlist[0]['productautoid']}
        )
        AND (suitid IS NULL OR suitid = {$orderlist[0]['suitid']})
        AND (
            order_status IS NULL
            OR order_status = {$orderlist[0]['status']}
        )
        AND isenabled = 1
sql;

        $listenerlist = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($listenerlist) <= 0) {
            self::write_order_listener_log($ordersn, "query_order_listener", "no order listener,sql:{$sql}");
            return true;
        }

        $host = DB::query(Database::SELECT, "select weburl from sline_weblist where webid=0")->execute()->as_array();
        if (count($host) > 0 && !empty($host[0]['weburl']))
            $host = $host[0]['weburl'];
        else
            $host = St_Functions::get_http_prefix() . "{$_SERVER['HTTP_HOST']}";

        foreach ($listenerlist as $listener) {
            $execurl = $listener['execute_url'];
            if (stristr($execurl, '?') === false) {
                $execurl = $execurl . "?ordersn={$ordersn}";
            } else {
                $execurl = $execurl . "&ordersn={$ordersn}";
            }

            $execurl = trim($execurl);
            if (stripos($execurl, "http://") !== 0 || stripos($execurl, "https://") !== 0)
                $execurl = "{$host}/{$execurl}";

            $execresult_text = self::_request($execurl);
            self::write_order_listener_log($execurl, "call_order_listener", $execresult_text);
            $execresult = json_decode($execresult_text);

            $retry = 0;
            while (!$execresult && $retry < 3) {
                $execresult_text = self::_request($execurl);
                self::write_order_listener_log($execurl, "call_order_listener_{$retry}", $execresult_text);
                $execresult = json_decode($execresult_text);

                $retry++;
            }

            if ($execresult->status != 1) {
                return $execresult->msg;
            }
        }
        return true;
    }

    private static function write_order_listener_log($ordersn, $action, $result)
    {
        $payLogDir = BASEPATH . '/data/order_listener_log/';
        if (!file_exists($payLogDir)) {
            mkdir($payLogDir, 0777, true);
        }
        //日志文件
        $file = $payLogDir . date('ymd') . '.txt';
        $now = date('YmdHis');

        $data = "=========================" . PHP_EOL;
        $data .= "ordersn:{$ordersn} {$now}" . PHP_EOL;
        $data .= "action:{$action}" . PHP_EOL;
        $data .= "result:{$result}" . PHP_EOL;

        file_put_contents($file, $data, FILE_APPEND);
    }

    /**
     * @function 后台订单获取操作
     * @param array $params
     * @return array
     */
    public static function back_order_list($params)
    {
        $typeid = $params['typeid'];
        $status = $params['status'];
        $start = $params['start'] ? $params['start'] : 0;
        $limit = $params['limit'] ? $params['limit'] : 20;
        $keyword = $params['keyword'] ? $params['keyword'] : '';
        $paysource = $params['paysource'] ? $params['paysource'] : '';
        $start_time = $params['start_time'] ? $params['start_time'] : '';
        $end_time = $params['end_time'] ? $params['end_time'] : '';
        $eticketno = $params['eticketno'] ? $params['eticketno'] : '';
        $memberid = $params['memberid'];
        $isconsume = $params['isconsume'];
        $webid = $params['webid'] ? $params['webid'] : 0;
        $sort = $params['sort'] ? $params['sort'] : array();

        $order = 'order by a.addtime desc';
        if (!empty($sort)) {
            $order = " order by a.{$sort['property']} {$sort['direction']},a.addtime desc";
        }


        $w = "where 1=1 ";
        if (!empty($typeid) || $typeid === 0 || $typeid === '0') {
            $w .= ' and a.typeid=' . $typeid;
        }
        if (!empty($status) || $status === 0 || $status === '0') {
            $w .= ' and a.status=' . $status;
        }
        if (!empty($isconsume) || $isconsume === 0 || $isconsume === '0') {
            $w .= ' and a.isconsume=' . $isconsume;
        }
        if (!empty($memberid) || $memberid === 0 || $memberid === '0') {
            $w .= ' and a.memberid=' . $memberid;
        }
        if (!empty($eticketno)) {
            $w .= " and a.eticketno='$eticketno'";
        }
        if (!empty($paysource)) {
            $w .= " and a.paysource='$paysource'";
        }
        if (!empty($start_time)) {
            $w .= " and a.addtime>=unix_timestamp('$start_time')";
        }
        if (!empty($end_time)) {
            $w .= " and a.addtime<=unix_timestamp('$end_time')";
        }
        if (!empty($keyword)) {
            $w .= " and (a.ordersn like '%{$keyword}%' or a.linkman like '%{$keyword}%' or a.linktel like '%{$keyword}%' or a.linkemail like '%{$keyword}%' or a.productname like '%{$keyword}%' or a.eticketno='{$keyword}')";
        }
        if ($webid >= 0) {
            $w .= " and a.webid=" . $webid;
        }

        $w .= " and b.virtual!=2";

        $sql = "SELECT a.*  FROM `sline_member_order` AS a LEFT JOIN `sline_member` AS b ON(a.memberid=b.mid) $w $order";

        $count = DB::query(1, $sql)->execute()->count();
        $sql .= " LIMIT $start,$limit";


        $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $new_list = array();
        foreach ($list as $k => $v) {
            $v['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
            $v['price'] = self::order_total_payprice($v['id'], $v);
            $v['statusname'] = self::get_status_name($v['status']); //$order_status[$v['status']];
            $v['dingnum'] = $v['dingnum'] + $v['childnum'] + $v['oldnum'];
            $new_list[] = $v;
        }

        $result['total'] = $count;
        $result['lists'] = $new_list;
        $result['success'] = true;
        $result['sql'] = $sql;
        return $result;


    }

    /**
     * @function 后台订单单个字段更新操作
     * @param $params
     *
     */
    public static function back_order_update_field($params)
    {
        $id = $params['orderid'] ? intval($params['orderid']) : 0;
        $val = $params['value'] ? $params['value'] : 0;
        $field = $params['field'] ? $params['field'] : '';
        $product_model = $params['product_model'] ? $params['product_model'] : '';
        $order = DB::select()->from('member_order')->where('id', '=', $id)->execute()->current();
        $opreate_status = true;

        if ($order['id']) {
            $oldstatus = $order['status'];
            $update_arr = array();
            $update_arr[$field] = $val;
            $flag = DB::update('member_order')->set($update_arr)->where('id', '=', $id)->execute();

            if ($flag) {

                $order = DB::select()->from('member_order')->where('id', '=', $id)->execute()->current();
                $detectresult = self::detect($order['ordersn']);
                if ($detectresult !== true) {
                    $opreate_status = false;
                }
                if ($field == 'status') {
                    $opreate_status = self::back_order_status_changed($oldstatus, $order, $product_model);

                }

            }

        }
        return $opreate_status;

    }

    /**
     * @function 订单状态改变操作
     * @param $org_status
     * @param $new_status
     * @param $order ,更新后的订单数组
     * @param $product_model
     */
    public static function back_order_status_changed($org_status, $order, $product_model)
    {
        if ($org_status == $order['status'])
            return true;
        $status = true;

        Model_Message::add_order_msg($order);

        //添加日志
        Model_Member_Order_Log::add_log($order, $org_status);

        $dingnum = intval($order['dingnum']) + intval($order['childnum']) + intval($order['oldnum']);

        if ($order['status'] == "2" && $order['eticketno'] == "" && $order['typeid'] == 5) {
            $update_arr['eticketno'] = self::get_eticketno();
        }
        //完成交易
        if ($order['status'] == 2) {
            self::back_order_paysuccess($order['id']);//支付成功
            Model_Member_Jifen::refund($order['id']);//返积分

        }
        //取消订单(加库存)
        if ($order['status'] == 3 && $org_status != 4)
        {
            call_user_func(array($product_model, 'storage'), $order['suitid'], $dingnum, $order);
            St_SMSService::send_product_order_msg(NoticeCommon::PRODUCT_ORDER_CANCEL_MSGTAG, $order);
            St_EmailService::send_product_order_email(NoticeCommon::PRODUCT_ORDER_CANCEL_MSGTAG, $order);
            self::order_back_coupon_jifen($order['ordersn'],$order);//处理优惠券，积分
        }
        //退款(加库存)
        if ($order['status'] == 4 && $org_status != 3) {
            call_user_func(array($product_model, 'storage'), $order['suitid'], $dingnum, $order);
            $order_info = Model_Member_Order::info($order['ordersn']);
            if ($org_status == 2 || $org_status == 5 || $org_status == 6) {
                Pay_Online_Refund::refund_start($order['ordersn'],$product_model,true);
            }
            self::order_back_coupon_jifen($order['ordersn'],$order);//处理优惠券，积分
        }
        //由取消变为处理中(减库存)
        if ($org_status == 3 && in_array($order['status'], array(0, 1, 2, 5))) {
            call_user_func(array($product_model, 'storage'), $order['suitid'], '-' . $dingnum, $order);
        }

        //由退款变为处理中(减库存)
        if ($org_status == 4 && in_array($order['status'], array(0, 1, 2, 5))) {
            call_user_func(array($product_model, 'storage'), $order['suitid'], '-' . $dingnum, $order);
        }


        if ($org_status != $order['status']) {
            $new_order = self::order_info($order['ordersn']);
            Plugin_Core_Factory::factory()->add_listener('on_orderstatus_changed', $new_order)->execute();
        }
        //二次确认订单发送短信
        if ($order['status'] == 1) {
            St_SMSService::send_product_order_msg(NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG, $order);
            St_EmailService::send_product_order_email(NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG, $order);
        }

        //订单监控
        $detectresult = Model_Member_Order::detect($order['ordersn']);
        if ($detectresult !== true) {
            return false;
        }
        return $status;
    }


    /**
     * @function 订单取消，退款处理优惠券积分
     * @param $ordersn 订单号
     * @param $order 订单
     */
    public static function order_back_coupon_jifen($ordersn,$order=null)
    {
        if (St_Functions::is_normal_app_install('coupon'))
        {
            Model_Coupon::cancel_order_back($ordersn);
        }
        if(empty($order))
        {
            $order = DB::select()->from('member_order')
                ->where('ordersn','=',$ordersn)
                ->execute()->current();
        }
        if($order['jifentprice'])
        {
            Model_Member_Jifen::order_back_jifen($order);
        }

    }




    /**
     * @function 获取paysource
     * @return array
     */
    public static function get_pay_source()
    {
        $sql = "SELECT paysource FROM `sline_member_order` WHERE paysource is not null GROUP BY paysource";
        $result = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $arr = array();
        foreach ($result as $v)
        {
            if (!empty($v))
            {
                $arr[] = $v['paysource'];
            }

        }
        return $arr;
    }

    /**
     * @function 订单删除
     * @param $orderid
     */
    public static function order_delete($orderid)
    {
        return DB::delete('member_order')->where('id', '=', $orderid)->execute();
    }

    /**
     * @function 后台设置订单支付成功
     * @param $orderid
     * @param string $paySource
     * @param null $params
     * @return bool
     */
    public static function back_order_paysuccess($orderid, $paySource = '后台', $params = null)
    {

        $arr = DB::select()->from('member_order')->where('id', '=', $orderid)->execute()->current();
        if (St_Functions::is_normal_app_install('supplierverifyorder'))
        {
            $eticketno = St_Product::add_eticketno($arr['ordersn']);
            $arr['eticketno'] = !empty($eticketno) ? $eticketno : $arr['eticketno'];
        }
        St_SMSService::send_product_order_msg(NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG, $arr);
        St_EmailService::send_product_order_email(NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG, $arr);

        return true;
    }

    /**
     * @function 根据时间范围获取某个产品类型订单总价.
     * @param $timearr 时间范围,如果为0则不限制时间
     * @param $typeid
     * @return int
     */
    public static function back_caculate_price_by_timerange($timearr, $typeid)
    {
        $obj = DB::select()->from('member_order')->where('typeid', '=', $typeid);
        if (is_array($timearr))
        {
            $obj = $obj->and_where('addtime', '>=', $timearr[0])->and_where('addtime', '<=', $timearr[1]);
        }
        $arr = $obj->and_where('status', 'in', array(2, 5))->execute()->as_array();
        $price = 0;
        foreach ($arr as $row)
        {
            $price += Model_Member_Order::order_total_price($row['id'],$row);/*intval($row['dingnum']) * $row['price'] + intval($row['childnum']) * $row['childprice'] + intval($row['oldnum']) * $row['oldprice'];
            if ($row['usejifen'])
            {
                $price -= $row['jifentprice'];
            }*/
        }
        return $price;
    }

    /**
     * @function 后台订单按年统计
     * @param $timearr
     * @param
     * @return array
     */
    public static function back_order_price_year($timearr, $typeid)
    {
        $where = '';
        $out = array();
        if (is_array($timearr))
        {
            $starttime = $timearr[0];
            $endtime = $timearr[1];
            $where = " AND addtime >= $starttime AND addtime <= $endtime";

        }
        if ($typeid)
        {
            $sql = "SELECT * FROM `sline_member_order` WHERE typeid='$typeid' {$where}";
        }
        else
        {
            $sql = "SELECT * FROM `sline_member_order` WHERE 1=1 {$where}";
        }

        $pay_sql = $sql . " AND (status=2 OR status=5) ";
        //已付款
        $arr = DB::query(1, $pay_sql)->execute()->as_array();

        $price = 0;
        foreach ($arr as $row)
        {
            $price += intval($row['dingnum']) * $row['price'] + intval($row['childnum']) * $row['childprice'] + intval($row['oldnum']) * $row['oldprice'];
        }
        $out['pay'] = array(
            'num' => count($arr),
            'price' => $price
        );
        //未付款
        $unapy_sql = $sql . " AND (status=0 OR status=1)  ";
        $arr = DB::query(1, $unapy_sql)->execute()->as_array();
        $price = 0;
        foreach ($arr as $row)
        {
            $price += intval($row['dingnum']) * $row['price'] + intval($row['childnum']) * $row['childprice'] + intval($row['oldnum']) * $row['oldprice'];
        }
        $out['unpay'] = array(
            'num' => count($arr),
            'price' => $price
        );
        //已取消
        $cancel_sql = $sql . " AND status=3  ";
        $arr = DB::query(1, $cancel_sql)->execute()->as_array();
        $price = 0;
        foreach ($arr as $row)
        {
            $price += intval($row['dingnum']) * $row['price'] + intval($row['childnum']) * $row['childprice'] + intval($row['oldnum']) * $row['oldprice'];
        }

        $out['cancel'] = array(
            'num' => count($arr),
            'price' => $price
        );
        return $out;


    }


    /**
     * @function 后台订单生成Excel
     * @param $time_arr
     * @param $typeid
     * @param $status
     * @return string
     */
    public static function back_order_excel($time_arr, $typeid, $status)
    {

        $w = "addtime>=$time_arr[0] and addtime<=$time_arr[1] and typeid='$typeid'";
        if ($status !== null && $status !== '')
        {
            $w .= " and status=$status";
        }
        $sql = "SELECT * FROM `sline_member_order` WHERE $w";
        $arr = DB::query(1, $sql)->execute()->as_array();


        $table = "<table><tr>";
        $table .= "<td>订单号</td>";
        $table .= "<td>产品名称</td>";
        $table .= "<td>预订日期</td>";
        $table .= "<td>使用日期</td>";
        $table .= "<td>成人数量</td>";
        $table .= "<td>成人价格</td>";
        if ($typeid == 1)
        {
            $table .= "<td>儿童数量</td>";
            $table .= "<td>儿童价格</td>";
            $table .= "<td>老人数量</td>";
            $table .= "<td>老人价格</td>";
            $table .= "<td>保险</td>";
        }
        $table .= "<td>应付总额</td>";
        $table .= "<td>交易状态</td>";
        $table .= "<td>预订人</td>";
        $table .= "<td>联系电话</td>";

        $table .= "</tr>";

        foreach ($arr as $row)
        {
            $order = $row;
            $price = 0;
            $insurancePrice = 0;
            if ($order['typeid'] != 2)
            {

                $price = intval($order['dingnum']) * $order['price'] + intval($order['childnum']) * $order['childprice'] + intval($order['oldnum']) * $order['oldprice'];
                if (!empty($order['usejifen']) && !empty($order['jifentprice']))
                {
                    $price = $price - intval($order['jifentprice']);//减去积分抵现的价格.
                }

            }

            /*if ($order['typeid'] == 1)
            {
                $insInfo = ORM::factory('insurance_booking')->where("bookordersn", '=', $order['ordersn'])->find()->as_array();
                if ($insInfo['payprice'])
                {
                    $price += $insInfo['payprice'];
                    $insurancePrice = $insInfo['payprice'];
                }

            }*/

            $childOrderLabel = $row['pid'] == 0 ? '' : "[子订单]";
            $table .= "<tr>";
            $table .= "<td style='vnd.ms-excel.numberformat:@'>" . $childOrderLabel . "{$row['ordersn']}</td>";
            $table .= "<td>{$row['productname']}</td>";
            $table .= "<td>" . date('Y-m-d H:i:s', $row['addtime']) . "</td>";
            $table .= "<td>{$row['usedate']}</td>";
            $table .= "<td>{$row['dingnum']}</td>";
            $table .= "<td>{$row['price']}</td>";

            if ($typeid == 1)
            {
                $table .= "<td>{$row['childnum']}</td>";
                $table .= "<td>{$row['childprice']}</td>";
                $table .= "<td>{$row['oldnum']}</td>";
                $table .= "<td>{$row['oldprice']}</td>";
                $table .= "<td>{$insurancePrice}</td>";


            }
            $table .= "<td>{$price}</td>";
            $table .= "<td>" . self::$order_status[$row['status']] . "</td>";
            $table .= "<td>{$row['linkman']}</td>";
            $table .= "<td>{$row['linktel']}</td>";

            $table .= "</tr>";

        }

        $table .= "</table>";
        return $table;
    }

    /**
     * @function 生成消费码
     * @return string
     */
    public static function get_eticketno()
    {

        $eticketno = "";
        while (true)
        {
            $eticketno = substr(St_Functions::get_random_number(9), 1, 8);
            $check_sql = "SELECT id FROM `sline_member_order` WHERE eticketno='{$eticketno}'";
            $row = DB::query(1, $check_sql)->execute()->as_array();
            if (count($row) <= 0)
                break;
            sleep(1);
        }
        return $eticketno;
    }


    /**
     * @function 监听订单执行回调函数
     * @param $url
     * @return mixed
     */
    private static function _request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    ///************************ PC端开始  *************************************///


    public static $orderStatus = array(
        0 => '等待处理',
        1 => '等待付款',
        2 => '付款成功',
        3 => '订单取消',
        4 => '已退款',
        5 => '交易完成',
        6 => '退款确认'
    );
    /**
     * @var array
     * typeid 对应栏目链接
     */
    public static $typeidTolink = array(
        1 => 'lines',
        2 => 'hotels',
        3 => 'cars',
        5 => 'spots',
        8 => 'visa',
        13 => 'tuan'
    );


    /**
     * @function 添加游客信息
     * @param $orderid
     * @param $arr
     */
    public static function add_tourer($orderid, $arr)
    {

        $tourname = $arr['tourname'];
        $toursex = $arr['toursex'];
        $tourmobile = $arr['tourmobile'];
        $tourcard = $arr['touridcard'];
        $tourcardtype = $arr['touridcardtype'];

        //兼容新版
      $tourname = empty($tourname)?$arr['t_tourername']:$tourname;
      $tourmobile = empty($tourmobile)?$arr['t_mobile']:$tourmobile;
      $toursex = empty($toursex)?$arr['t_sex']:$toursex;
      $tourcard = empty($tourcard)?$arr['t_cardnumber']:$tourcard;
      $tourcardtype = empty($tourcardtype)?$arr['t_cardtype']:$tourcardtype;

        foreach ($tourname as $i=>$row)
        {

            $ar = array(
                'orderid' => $orderid,
                'tourername' => $tourname[$i],
                'cardtype' =>$tourcardtype[$i],
                'cardnumber' => $tourcard[$i],
                'mobile' => $tourmobile[$i],
                'sex'=>$toursex[$i]

            );
            $m = ORM::factory('member_order_tourer');
            foreach ($ar as $k => $v)
            {
                $m->$k = $v;
            }
            $m->save();
            $m->clear();
        }


    }

    /**
     * @function 添加游客信息
     * @param $orderid
     * @param $arr
     */
    public static function add_tourer_pc($orderid, $arr, $memberid)
    {

        for ($i = 0; isset($arr[$i]); $i++)
        {
            $ar = array(
                'orderid' => $orderid,
                'tourername' => $arr[$i]['name'],
                'cardtype' => $arr[$i]['cardtype'],
                'cardnumber' => $arr[$i]['cardno'],
                'mobile' => ''
            );
            $m = ORM::factory('member_order_tourer');

            foreach ($ar as $k => $v)
            {
                $m->$k = $v;
            }
            $m->save();
            if ($m->saved())
            {

                self::add_tourer_to_linkman($ar, $memberid);
            }
            $m->clear();
        }
    }

    /**
     * @function 检测常用联系人是否存在,不存在则添加
     * @param $ar
     * @param $mid
     * @throws Kohana_Exception
     */
    public static function add_tourer_to_linkman($ar, $mid)
    {
        $m = ORM::factory('member_linkman')
            ->where("memberid=$mid and linkman='{$ar['tourername']}'")
            ->find();

        $new = array(
            'linkman' => $ar['tourername'],
            'idcard' => $ar['cardnumber'],
            'cardtype' => $ar['cardtype'],
            'memberid' => $mid

        );
        //如果没有找到,则自动加入常用联系人表
        if (!$m->loaded())
        {
            $_m = ORM::factory('member_linkman');

            foreach ($new as $k => $v)
            {
                $_m->$k = $v;
            }

            $_m->save();


        }
    }


    /**
     * @function 添加发票信息
     * @param $orderId
     * @param $billInfo
     */
    public static function add_bill_info($orderId, $billInfo)
    {
        $m = ORM::factory('member_order_bill');
        $m->orderid = $orderId;
        foreach ($billInfo as $k => $v)
        {
            $m->$k = $v;
        }
        $m->save();
    }

    /**
     * @function 添加保险订单
     * @param $insuranceCodes
     * @param $orderSn 产品订单编号
     * @param $dingNum 预订数量
     * @param $memberId 会员ID
     * @param $useDate 出发日期
     * @param $lineDay 天数
     * @param $tourer 游客表
     */
    public static function add_insurance_order($insuranceCodes, $orderSn, $dingNum, $memberId, $useDate, $lineDay, $tourer)
    {


        $codes = explode(',', $insuranceCodes);
        foreach ($codes as $code)
        {
            $sql = "SELECT * FROM `sline_insurance` WHERE productcode='$code'";
            $ar = DB::query(1, $sql)->execute()->as_array();
            $info = $ar[0];
            if (empty($info))
            {
                continue;
            }
            $curtime = time();
            $m = ORM::factory('insurance_booking');
            $insInfo = array(
                'bookordersn' => $orderSn,
                'productcasecode' => $code,
                'insurednum' => $dingNum,
                'memberid' => $memberId,
                'payprice' => $info['ourprice'] * $dingNum * $lineDay,
                'begindate' => $useDate,
                'enddate' => date('Y-m-d', strtotime($useDate) + ($lineDay - 1) * 24 * 3600),
                'ordersn' => 'INS' . $curtime . mt_rand(11, 99),
                'addtime' => $curtime,
                'modtime' => $curtime
            );
            foreach ($insInfo as $k => $v)
            {
                $m->$k = $v;
            }
            $m->save();
            if ($m->saved())
            {
                $insOrderId = $m->id;
                foreach ($tourer as $t)
                {

                    $insuredModel = ORM::factory('insurance_booking_tourer');
                    $insuredModel->name = $t['name'];
                    $insuredModel->sex = 0;
                    $insuredModel->mobile = '';
                    $insuredModel->cardcode = $t['cardno'];
                    $insuredModel->cardtype = self::get_card_type($t['cardtype']);
                    $insuredModel->orderid = $insOrderId;
                    $insuredModel->insurantrelation = 6;
                    $insuredModel->count = 1;
                    $insuredModel->save();
                    $insuredModel->clear();


                }
            }


        }


    }

    /**
     * @function 获取证件id,仅保险使用
     * @param $name
     * @return mixed
     */
    private static function get_card_type($name)
    {
        $_arr = array(
            array('name' => '身份证', 'id' => 1),
            array('name' => '军官证', 'id' => 2),
            array('name' => '因私护照', 'id' => 3),
            array('name' => '港澳通行证', 'id' => 4),
            array('name' => '台胞证', 'id' => 7));
        foreach ($_arr as $v)
        {
            if ($v['name'] == $name)
                return $v['id'];
        }


    }

    /**
     * @function 获取产品地址 (弃用)
     * @param $id
     * @param $typeid
     * @param string $productname
     * @return string
     */
    public static function get_product_url($id, $typeid, $productname = '')
    {

        $channeltable = array(
            "1" => "sline_line",
            "2" => "sline_hotel",
            "3" => "sline_car",
            "4" => "",
            "5" => "sline_spot",
            "6" => "",
            "7" => "",
            "8" => "sline_visa",
            "9" => "",
            "10" => "",
            "11" => "",
            "12" => "",
            "13" => "sline_tuan");
        $tablename = $channeltable[$typeid];
        $fields = array(
            '1' => array('field' => 'title', 'link' => 'lines'),
            '2' => array('field' => 'title', 'link' => 'hotels'),
            '3' => array('field' => 'title', 'link' => 'cars'),
            '4' => array('field' => 'title', 'link' => 'article'),
            '5' => array('field' => 'title', 'link' => 'spots'),
            '8' => array('field' => 'title', 'link' => 'visa'),
            '13' => array('field' => 'title', 'link' => 'tuan')
        );
        $field = $fields[$typeid]['field'];
        $link = $fields[$typeid]['link'];

        //如果为空,则是通用模块
        if (empty($field))
        {
            $moduleinfo = Model_Model::getModuleInfo($typeid);
            $field = 'title';
            $link = $moduleinfo['pinyin'];
            $tablename = 'sline_model_archive';

        }
        $sql = "SELECT aid,{$field} AS title,webid FROM {$tablename} WHERE id='$id'";
        $ar = DB::query(1, $sql)->execute()->as_array();
        $row = $ar[0];
        $title = !empty($productname) ? $productname : $row['title'];
        $weburl = Common::get_web_url($row['webid']);
        $out = "<a href=\"{$weburl}/{$link}/show_{$row['aid']}.html\" target=\"_blank\">{$title}</a>";
        return $out;

    }


    /**
     * @function 获取产品详细信息
     * @param $typeid
     * @param $productid
     * @return array
     */
    public static function get_product_info($typeid, $productid)
    {
        $out = array();
        if ($typeid)
        {
            $model = ORM::factory('model', $typeid);

            $table = $model->maintable;
            $pinyin = $model->pinyin;
            if ($table)
            {
                $info = ORM::factory($table, $productid)->as_array();
                $py = empty($model->correct) ? $pinyin : $model->correct;
                $url = Common::get_web_url($info['webid']) . "/{$py}/show_{$info['aid']}.html";
                $info['url'] = $url;
                $out = $info;
            }

        }
        return $out;
    }
    ///************************ PC端结束  *************************************///

    ///************************ 后台开始  ************************************///
    public static $statusNames = array(0 => '未处理', 1 => '处理中', 2 => '付款成功', 3 => '取消订单', 4 => '已退款', 5 => '交易完成');

    /**
     * @function 反积分
     * @param $orderid
     * @throws Kohana_Exception
     */
    public static function refundJifen($orderid)
    {
        $row = ORM::factory('member_order')->where('id=' . $orderid)->find()->as_array();
        if (isset($row))
        {
            $memberid = $row['memberid'];
            $jifenbook = intval($row['jifenbook']);
            $member = ORM::factory('member')->where("mid=$memberid");
            $member->jifen = intval($member->jifen) + $jifenbook;
            $member->save();
            if ($member->saved())
            {
                $memberid = $member->mid;
                $content = "预订{$row['productname']}获得{$jifenbook}积分";
                self::addJifenLog($memberid, $content, $jifenbook, 2);
            }

        }

    }

    /**
     * @function 添加积分日志
     * @param $memberid
     * @param $content
     * @param $jifen
     * @param $type
     */
    public static function addJifenLog($memberid, $content, $jifen, $type)
    {
        $addtime = time();
        $sql = "insert into sline_member_jifen_log(memberid,content,jifen,`type`,addtime) values ('$memberid','$content','$jifen','$type','$addtime')";
        DB::query(Database::INSERT, $sql)->execute();

    }


    /**
     * @function 返库存
     * @param $orderid
     * @param $op
     * @throws Kohana_Exception
     */
    public static function refundStorage($orderid, $op)
    {
        $row = ORM::factory('member_order')->where('id=' . $orderid)->find()->as_array();
        if (isset($row))
        {
            $dingnum = intval($row['dingnum']) + intval($row['childnum']);
            $suitid = $row['suitid'];
            $productid = $row['productautoid'];
            $typeid = $row['typeid'];
            $usedate = strtotime($row['usedate']);


            $storage_table = array(
                '1' => 'sline_line_suit_price',
                '2' => 'sline_hotel_room_price',
                '3' => 'sline_car_suit_price',
                '5' => 'sline_spot_ticket',
                '8' => 'sline_visa',
                '13' => 'sline_tuan'
            );
            $table = $storage_table[$typeid];
            if (empty($table))
                return;
            //加库存
            if ($op == 'plus')
            {
                if ($typeid == 1 || $typeid == 2 || $typeid == 3)
                    $sql = "update {$table} set number=number+$dingnum where day='$usedate' and suitid='$suitid' and number!=-1";
                elseif ($typeid == 13)
                    $sql = "update {$table} set totalnum=CAST(totalnum AS SIGNED)+$dingnum where id=$productid and number!=-1";
                elseif ($typeid == 5)
                    $sql = "update {$table} set number=number+$dingnum where id='$suitid' and number!=-1";
                else
                    $sql = "update {$table} set number=number+$dingnum where id=$productid and number!=-1";
            }
            else if ($op == 'minus')
            {
                if ($typeid == 1 || $typeid == 2 || $typeid == 3)
                    $sql = "update {$table} set number=number-$dingnum where day='$usedate' and suitid='$suitid' and number!=-1";
                elseif ($typeid == 13)
                    $sql = "update {$table} set totalnum=CAST(totalnum AS SIGNED)-$dingnum where id=$productid and number!=-1";
                elseif ($typeid == 5)
                    $sql = "update {$table} set number=number-$dingnum where id='$suitid' and number!=-1";
                else
                    $sql = "update {$table} set number=number-$dingnum where id=$productid and number!=-1";
            }
            DB::query(2, $sql)->execute();
        }

    }

    /**
     * @function 获取状态名称
     * @param $key
     * @return mixed
     */
    public static function getStatusName($key)
    {
        return self::$statusNames[$key];
    }

    /**
     * @function 获取状态的数组
     * @return array
     */
    public static function getStatusNamesJs()
    {
        $jsonArr = array();
        foreach (self::$order_status as $k => $v)
        {
            $jsonArr[] = array('status' => $k, 'name' => $v);
        }
        return $jsonArr;
    }

    /**
     * @function 获取支付来源数组
     * @return array
     */
    public static function getPaySources()
    {
        $sql = "select paysource from sline_member_order where paysource is not null group by paysource";
        $result = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $arr = array();
        foreach ($result as $k => $v)
        {
            $arr[] = $v['paysource'];
        }
        return $arr;
    }

    /**
     * @function 获取支付总额
     * @param $rs
     * @return float
     */
    public static function get_payed_amount($rs)
    {
        $num = $rs['dingnum'] + $rs['childnum'] + $rs['oldnum'];

        //全额支付
        $total = $rs['dingnum'] * $rs['price'] + $rs['childnum'] * $rs['childprice'] + $rs['oldnum'] * $rs['oldprice'];

        //保险
        if ($rs['typeid'] == 1)
        {
            $sql = "select bookordersn,insurednum,payprice from sline_insurance_booking where bookordersn='{$rs['ordersn']}'";
            $insurance = DB::query(Database::SELECT, $sql)->execute()->as_array();
            //叠加保险金额
            foreach ($insurance as $v)
            {
                if (!empty($v['insurednum']))
                {
                    $total += $v['payprice'];
                }
            }
            if ($rs['roombalance_paytype'] == 1)
            {
                $balanceTotal = doubleval($rs['roombalance'] * intval($rs['roombalancenum']));
                $total += $balanceTotal;
            }
        }
        //积分抵现
        if (intval($rs['usejifen']) === 1)
        {
            $total = $total - $rs['jifentprice'];
        }
        return $total;
    }
    ///************************ 后台结束  ************************************///


    ///********************** 手机端开始  *****************************///


    /**
     * 订单列表
     * @param $mid
     * @param $page
     * @return mixed
     */
    public static function order_list_mobile($mid, $page = 1, $param = null)
    {
        if (!is_null($param) && isset($param['isquery']))
        {
            $data = self::query_order($page, $param);
        }
        else
        {
            $data = self::my_order($mid, $page, $param);
        }
        return $data;
    }

    /**
     * @function 订单查询
     * @param $mid
     * @param int $page
     * @param null $param
     */
    public static function query_order($page, $param)
    {
        $offset = ($page - 1) * 10;
        $sql = "SELECT * FROM sline_member_order ";
        $sql .= "WHERE pid=0 AND `memberid` in (select mid from sline_member where mobile={$param['isquery']}) ";
        if (!is_null($param) && $param['type'] != -1)
        {
            switch ($param['type'])
            {
                case 0:
                    $sql .= "and `status` < 2 ";
                    break;
                case 1:
                    $sql .= "and `status` > 1 and `status` !=3 ";
                    break;
            }
        }
        $sql .= "ORDER BY id DESC ";
        $sql .= "LIMIT {$offset},10";
        $arr = DB::query(1, $sql)->execute()->as_array();
        foreach ($arr as &$v)
        {
            $model = ORM::factory('model', $v['typeid']);
            $table = $model->maintable;
            if ($table)
            {
                $info = ORM::factory($table, $v['productautoid'])->as_array();
                $out = $info;
                $v['litpic'] = Common::img($out['litpic']);
            }
        }
        return $arr;
    }

    /**
     * @function 获取我的订单列表
     * @param $mid
     * @param $page
     * @param $param
     * @return mixed
     */
    public static function my_order($mid, $page, $param)
    {
        $offset = ($page - 1) * 10;
        $sql = "SELECT * FROM sline_member_order ";
        $sql .= "WHERE pid=0 AND `memberid`={$mid} ";
        if (!is_null($param) && $param['type'] != -1)
        {

            switch ($param['type'])
            {
                case 0:
                    $sql .= "and `status` < 2 ";
                    break;
                case 1:
                    $sql .= "and `status` = 2 ";
                    break;
                case 2:
                    $uncommentable_typeids = Model_Model::get_uncommentable_typeids();
                    $uncommentable_typeids_str = implode(',', $uncommentable_typeids);
                    $sql .= 'AND status=5 AND ispinlun=0 and typeid not in (' . $uncommentable_typeids_str . ') ';
                    break;

            }
        }
        $sql .= "ORDER BY id DESC ";
        $sql .= "LIMIT {$offset},10";

        $arr = DB::query(1, $sql)->execute()->as_array();
        foreach ($arr as &$v)
        {
            $model = ORM::factory('model', $v['typeid']);
            $table = $model->maintable;
            if ($table)
            {
                $info = ORM::factory($table, $v['productautoid'])->as_array();
                $out = $info;
                $v['litpic'] = Common::img($out['litpic']);
            }
        }
        return $arr;
    }


    /**
     * @function 查询未支付的订单
     * @param $memberid
     */
    public static function unpay($memberid)
    {
        $sql = "SELECT * FROM sline_member_order ";
        $sql .= "WHERE memberid='{$memberid}'AND status<2";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * @function 订单详情
     * @param $ordersn
     */
    public static function info($ordersn)
    {
        if (!preg_match('~^\d+$~', $ordersn))
        {
            return;
        }
        $sql = "select * from sline_member_order where ordersn='{$ordersn}' order by id DESC limit 1 ";

        $rs = DB::query(Database::SELECT, $sql)->execute()->current();
        //总价
        $rs['total_fee'] = self::order_total_price($rs['id']);
        //实际支付写入数组
        $rs['total'] = self::order_total_payprice($rs['id']);

        if (St_Functions::is_normal_app_install('coupon'))
        {

            $rs['iscoupon'] = Model_Coupon::order_view($ordersn);
            $rs['cmoney'] = $rs['iscoupon']['cmoney'];
        }
        //产品详情页
        //   $rs['show_url'] = Common::show_url($rs['typeid'], $rs['productaid']);
        return $rs;
    }
    ///********************** 手机端结束  ****************************///

    /**
     * @function 某段时间内的价格和销量统计
     * @param $timearr
     * @param $typeid
     * @return array
     */
    public static function back_caculate_info_by_timerange($timearr, $typeid)
    {
        $where = '';
        $out = array();
        if (is_array($timearr))
        {
            $starttime = $timearr[0];
            $endtime = $timearr[1];
            $where = "addtime>=$starttime and addtime<=$endtime and";
        }
        $typeid_condition = "1=1";
        if ($typeid)
        {
            $typeid_condition = "typeid={$typeid}";
        }
        //已付款
        $arr = ORM::factory('member_order')->where("{$where} {$typeid_condition} and (status=2 or status=5)")->get_all();
        $price = 0;
        foreach ($arr as $row)
        {
            $price += self::order_total_price($row['id'], $row);
        }
        $out['pay'] = array(
            'num' => count($arr),
            'price' => $price
        );
        //未付款
        $arr = ORM::factory('member_order')->where("{$where} {$typeid_condition} and (status=0 or status=1)")->get_all();
        $price = 0;
        foreach ($arr as $row)
        {
            $price += self::order_total_price($row['id'], $row);
        }
        $out['unpay'] = array(
            'num' => count($arr),
            'price' => $price
        );
        //已取消
        $arr = ORM::factory('member_order')->where("{$where} {$typeid_condition} and status=3")->get_all();
        $price = 0;
        foreach ($arr as $row)
        {
            $price += self::order_total_price($row['id'], $row);
        }

        $out['cancel'] = array(
            'num' => count($arr),
            'price' => $price
        );
        return $out;

    }

    /**
     * @function 获取支付方式的中文描述文字
     * @param $code
     * @return mixed
     */
    public static function get_paytype_name($code)
    {
        $code = intval($code);
        return self::$paytype_names[$code];
    }

    ///********************** 支付端开始  ****************************///
    /**
     * 订单是否存在
     * @param $ordersn
     * @return bool
     */
    public static function not_exists($ordersn)
    {
        $sql = "select * from sline_member_order where ordersn={$ordersn} order by id DESC limit 1";
        $rs = DB::query(Database::SELECT, $sql)->execute()->as_array();
        return empty($rs) ? true : false;
    }

    /**
     * 订单是否支付
     * @param $ordersn
     * @return bool
     */
    public static function payed($ordersn)
    {
        $sql = "select * from sline_member_order where ordersn={$ordersn}  and status=2 order by id DESC limit 1";
        $rs = DB::query(Database::SELECT, $sql)->execute()->as_array();
        return empty($rs) ? false : true;
    }


}