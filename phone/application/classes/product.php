<?php

/**
 * 产品操作公共静态类
 * User: Netman
 * Date: 15-9-23
 * Time: 下午1:48
 */
class Product
{
    /**
     * 产品详情页中提取seo
     * @param $arr
     * @return array
     */
    public static function seo($arr)
    {
        $seoArr = array(
            'seotitle' => empty($arr['seotitle']) ? $arr['title'] : $arr['seotitle'],
            'keyword' => $arr['keyword'],
            'description' => $arr['description']
        );
        foreach ($seoArr as &$v) {
            $v = trim($v);
        }
        return $seoArr;
    }

    /**
     * 产品图片
     * @param $picStr
     * @return array
     */
    public static function pic_list($picStr)
    {


        if (empty($picStr)) {
            return;
        }
        $arr = explode(',', $picStr);
        foreach ($arr as &$v) {
            $v = explode('||', $v);
        }
        return $arr;
    }

    /**
     * 产品编号 共6位,不足6位前面被0
     * @param $id
     * @param $prefixId
     * @return string
     */
    public static function product_number($id, $prefixId)
    {
        $arr = array(
            'A' => '01',
            'B' => '02',
            'C' => '05',
            'D' => '03',
            'E' => '08',
            'G' => '13',
            'H' => '14',
            'I' => '15',
            'J' => '16',
            'K' => '17',
            'L' => '18',
            'M' => '19',
            'N' => '20',
            'O' => '21',
            'P' => '22',
            'Q' => '23',
            'R' => '24',
            'S' => '25',
            'T' => '26'
        );
        return array_search($prefixId, $arr) . str_pad($id, 5, "0", STR_PAD_LEFT);
    }

    /**
     * 产品内容页去除style 图片如为相对路径加上图片域名
     * @param $str
     * @return mixed
     */
    public static function strip_style($str)
    {
        $str = preg_replace('~<strong(.*?)style="(.*?)"([^>]*?)>~is', '<strong \\1 style="\\2;font-weight: bold;" \\3>', $str);
        $str = str_replace('<strong>', '<strong style="font-weight:bold;">', $str);
        $str = preg_replace_callback('~style="(.*?)"~', array('self', '_strip_style'), $str);
        $str = preg_replace('~<([^>]*)>(?:\s|&nbsp;)*</\1>~', '', $str);
        $str = preg_replace_callback('~src="(.*?\.(?:jpg|gif|png|jpeg))"~', array('self', '_cut_image'), $str);
        $str = preg_replace(array('~width\s*=\s*([\'"]).*?\1~', '~height\s*=\s*([\'"]).*?\1~'), '', $str);
        return $str;
    }

    /**
     * @function 保留html标签
     * @param $match
     * @return string
     */
    public static function _strip_style($match)
    {
        preg_match_all('~((?:font-(?:size|weight)|(?:[a-zA-z-])*color|text-decoration|text-indent|text-align)\s*:[^;]*?;)~is', $match[1], $rs);
        $bool = preg_match_all('~(font-family\s*:\s*.*?;;)~is', $match[1], $fm);
        if (!$bool) {
            preg_match_all('~(font-family\s*:\s*.*?;)~is', $match[1], $fm);
        }
        if (isset($rs[1]) && isset($fm[1])) {
            $rs[1] = array_merge($rs[1], $fm[1]);
        }
        return 'style="' . implode('', $rs[1]) . '"';
    }

    /**
     * 详情页图片裁剪
     * @param $match
     * @return string
     */
    public static function _cut_image($match){

        return sprintf(' src="/phone/public/images/grey.gif" st-src="%s"',Common::img($match[1],540,0,true));

    }
    /*
     * 属性生成where条件,用于多条件属性搜索.
     * */
    public static function get_attr_where($attrid)
    {
        $arr = Common::remove_arr_empty(explode('_', $attrid));
        foreach ($arr as $value) {
            if ($value != 0) {
                $str .= " and FIND_IN_SET($value,a.attrid) ";
            }
        }
        return $str;
    }

    /*
    * 生成订单编号
    * */
    public static function get_ordersn($kind)
    {
        return St_Product::get_ordersn($kind);
    }


    //添加订单
    public static function add_order($arr)
    {
        $model = ORM::factory('member_order');
        $flag = 0;
        if (is_array($arr)) {
            //会员查询及自动增加
            $member = Common::session('member');
            if (!isset($member['mid'])) {
                $member = Model_Member::member_find($arr['linktel']);
                if (empty($member)) {
                    $pwd = str_shuffle('abcdefthigklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
                    $pwd = substr($pwd, 0, 6);
                    $member['mobile'] = $arr['linktel'];
                    $member['pwd'] = md5($pwd);
                    $member['nickname'] = substr($arr['linktel'], 0, 5) . '***';
                    $member_result = Model_Member::register($member);
                    if (!is_array($member_result)) {
                        return false;
                    }
                    $member['mid'] = $member_result[0];

                    Plugin_Core_Factory::factory()->add_listener('on_member_register', $member)->execute();
                    //发送短信
                    $content = "尊敬的用户{$arr['linktel']}你好,你已经成功注册成为{$GLOBALS['cfg_webname']}会员,你的账号是:{$arr['linktel']},密码是:{$pwd},为了你的帐户安全,请尽快修改密码!";
                    self::send_msg($arr['linktel'], $GLOBALS['cfg_webname'], $content);

                }
                //写入session
                Model_Member::write_session($member);
            }
            //订单会员ID
            $arr['memberid'] = $member['mid'];
            //二次确认
            if ($arr['paytype'] == '3')//这里补充一个当为二次确认时,修改订单为未处理状态.
            {
                $arr['status'] = 0;
            }
            //积分抵现
            if ($arr['usejifen'] == 1) {
                $needjifen = $GLOBALS['cfg_exchange_jifen'] * $arr['jifentprice'];
                $flag = Model_Member::operate_jifen($arr['memberid'], $needjifen, 1);
                if ($flag) {
                    Product::add_jifen_log($arr['memberid'], '预订产品' . $arr['productname'] . '积分抵现消费积分' . $needjifen, $needjifen, 1);
                }
                else {
                    $arr['usejifen'] = 0;
                }
            }

            $supplierlist = Model_Model::get_product_bymodel($arr['typeid'], $arr['productautoid'], 'supplierlist');
            if ($supplierlist != null) {
                $arr['supplierlist'] = $supplierlist[0]['supplierlist'];
            }

            foreach ($arr as $k => $v) {
                $model->$k = $v;
            }
            if ($arr['typeid'] == 2) {
                $arr['pid'] = 0;
            }
            $mainid = $model->save();
            /*if ($arr['typeid'] == 2)
            {
                $arr['ordersn'] = self::get_ordersn('02');
                $arr['pid'] = $mainid;
                $m = ORM::factory('member_order');
                foreach ($arr as $k => $v)
                {
                    $m->$k = $v;
                }
                $m->save();
            }*/
            $flag = $model->saved();

            if ($flag) {

                $detectresult = Model_Member_Order_listener::detect($arr['ordersn']);
                if ($detectresult !== true)
                    return false;

                //减库存
                $dingnum = intval($arr['dingnum']) + intval($arr['childnum']) + intval($arr['oldnum']);
                if ($arr['typeid'] != 2) {
                    self::minus_storage($arr['usedate'], $arr['typeid'], $arr['suitid'], $arr['productautoid'], $dingnum);
                }
                else {
                    self::minus_storage($arr['usedate'], $arr['typeid'], $arr['suitid'], $arr['productid'], $dingnum, $arr['departdate']);
                }

                $memberinfo = Model_Member::get_member_byid($arr['memberid']);
                $mobile = $arr['linktel'] ? $arr['linktel'] : $memberinfo['mobile'];
                $prefix = !empty($memberinfo['nickname']) ? $memberinfo['nickname'] : $memberinfo['mobile'];
                $orderAmount = self::calculate_price($model->as_array());
                if ($arr['paytype'] == '3') //二次确认支付
                {
                    $msgInfo = self::get_define_msg($arr['typeid'], 1);
                    if ($msgInfo['isopen'] == 1) //等待客服处理短信
                    {
                        $content = $msgInfo['msg'];
                        $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
                        $content = str_replace('{#PHONE#}', $GLOBALS['cfg_phone'], $content);
                        $content = str_replace('{#MEMBERNAME#}', $memberinfo['nickname'], $content);
                        $content = str_replace('{#PRODUCTNAME#}', $arr['productname'], $content);
                        $content = str_replace('{#PRICE#}', $orderAmount['priceDescript'], $content);
                        $content = str_replace('{#NUMBER#}', $orderAmount['numberDescript'], $content);
                        $content = str_replace('{#TOTALPRICE#}', $orderAmount['totalPrice'], $content);
                        $content = str_replace('{#ORDERSN#}', $arr['ordersn'], $content);
                        $content = str_replace('{#ETICKETNO#}', $arr['eticketno'], $content);
                        self::send_msg($mobile, $prefix, $content); //发送短信.
                    }
                    $emailInfo = self::get_email_msg($arr['typeid'], 1);
                    if ($emailInfo['isopen'] == 1 && $memberinfo['email']) {
                        $title = "预定" . $arr['productname'] . '[' . $GLOBALS['cfg_webname'] . ']';
                        $content = $emailInfo['msg'];
                        $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
                        $content = str_replace('{#PHONE#}', $GLOBALS['cfg_phone'], $content);
                        $content = str_replace('{#MEMBERNAME#}', $memberinfo['nickname'], $content);
                        $content = str_replace('{#PRODUCTNAME#}', $arr['productname'], $content);
                        $content = str_replace('{#PRICE#}', $orderAmount['priceDescript'], $content);
                        $content = str_replace('{#NUMBER#}', $orderAmount['numberDescript'], $content);
                        $content = str_replace('{#TOTALPRICE#}', $orderAmount['totalPrice'], $content);
                        $content = str_replace('{#ORDERSN#}', $arr['ordersn'], $content);
                        $content = str_replace('{#ETICKETNO#}', $arr['eticketno'], $content);
                        self::order_email($memberinfo['email'], $title, $content);
                    }


                }
                else //全款支付/订金支付
                {
                    $msgInfo = self::get_define_msg($arr['typeid'], 2);
                    if ($msgInfo['isopen'] == 1) {
                        $content = $msgInfo['msg'];
                        $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
                        $content = str_replace('{#PHONE#}', $GLOBALS['cfg_phone'], $content);
                        $content = str_replace('{#MEMBERNAME#}', $memberinfo['nickname'], $content);
                        $content = str_replace('{#PRODUCTNAME#}', $arr['productname'], $content);
                        $content = str_replace('{#PRICE#}', $orderAmount['priceDescript'], $content);
                        $content = str_replace('{#NUMBER#}', $orderAmount['numberDescript'], $content);
                        $content = str_replace('{#TOTALPRICE#}', $orderAmount['totalPrice'], $content);
                        $content = str_replace('{#ORDERSN#}', $arr['ordersn'], $content);
                        $content = str_replace('{#ETICKETNO#}', $arr['eticketno'], $content);
                        self::send_msg($mobile, $prefix, $content);//发送短信.
                    }
                    $emailInfo = self::get_email_msg($arr['typeid'], 2);
                    if ($emailInfo['isopen'] == 1 && $memberinfo['email']) {
                        $title = "预定" . $arr['productname'] . '[' . $GLOBALS['cfg_webname'] . ']';
                        $content = $emailInfo['msg'];
                        $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
                        $content = str_replace('{#PHONE#}', $GLOBALS['cfg_phone'], $content);
                        $content = str_replace('{#MEMBERNAME#}', $memberinfo['nickname'], $content);
                        $content = str_replace('{#PRODUCTNAME#}', $arr['productname'], $content);
                        $content = str_replace('{#PRICE#}', $orderAmount['priceDescript'], $content);
                        $content = str_replace('{#NUMBER#}', $orderAmount['numberDescript'], $content);
                        $content = str_replace('{#TOTALPRICE#}', $orderAmount['totalPrice'], $content);
                        $content = str_replace('{#ORDERSN#}', $arr['ordersn'], $content);
                        $content = str_replace('{#ETICKETNO#}', $arr['eticketno'], $content);
                        self::order_email($memberinfo['email'], $title, $content);
                    }


                }

                //供应商短信发送
                if ($GLOBALS['cfg_supplier_msg_open'] == 1 && !empty($GLOBALS['cfg_supplier_msg'])) {
                    $content = $GLOBALS['cfg_supplier_msg'];
                    $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
                    $content = str_replace('{#PHONE#}', $memberinfo['mobile'], $content);
                    $content = str_replace('{#MEMBERNAME#}', $memberinfo['nickname'], $content);
                    $content = str_replace('{#PRODUCTNAME#}', $arr['productname'], $content);
                    $content = str_replace('{#PRICE#}', $orderAmount['priceDescript'], $content);
                    $content = str_replace('{#NUMBER#}', $orderAmount['numberDescript'], $content);
                    $content = str_replace('{#TOTALPRICE#}', $orderAmount['totalPrice'], $content);
                    $content = str_replace('{#ORDERSN#}', $arr['ordersn'], $content);
                    $content = str_replace('{#ETICKETNO#}', $arr['eticketno'], $content);

                    //本站管理员短信发送
                    $cfg_webmaster_phone = $GLOBALS['cfg_webmaster_phone'];
                    if (!empty($cfg_webmaster_phone)) {
                        self::send_msg($cfg_webmaster_phone, $prefix, $content);//发送短信.
                    }

                    if ($GLOBALS['cfg_supplier_send_open'] == 1) {
                        $supplierphone = self::get_supplier_link($arr['productautoid'], $arr['typeid']);
                        if (!empty($supplierphone)) {
                            self::send_msg($supplierphone, $prefix, $content);//发送短信..
                        }
                    }
                }

                //供应商email发送
                if ($GLOBALS['cfg_supplier_email_open'] == 1 && !empty($GLOBALS['cfg_supplier_emailmsg'])) {
                    $content = $GLOBALS['cfg_supplier_emailmsg'];
                    $title = "预定" . $arr['productname'] . '[' . $GLOBALS['cfg_webname'] . ']';
                    $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
                    $content = str_replace('{#PHONE#}', $memberinfo['mobile'], $content);
                    $content = str_replace('{#MEMBERNAME#}', $memberinfo['nickname'], $content);
                    $content = str_replace('{#PRODUCTNAME#}', $arr['productname'], $content);
                    $content = str_replace('{#PRICE#}', $orderAmount['priceDescript'], $content);
                    $content = str_replace('{#NUMBER#}', $orderAmount['numberDescript'], $content);
                    $content = str_replace('{#TOTALPRICE#}', $orderAmount['totalPrice'], $content);
                    $content = str_replace('{#ORDERSN#}', $arr['ordersn'], $content);
                    $content = str_replace('{#ETICKETNO#}', $arr['eticketno'], $content);


                    //本站管理员短信发送
                    $cfg_webmaster_email = $GLOBALS['cfg_webmaster_email'];
                    if (!empty($cfg_webmaster_email)) {
                        self::order_email($cfg_webmaster_email, $title, $content);
                    }

                    if ($GLOBALS['cfg_supplier_sendemail_open'] == 1) {
                        $supplieremail = self::get_supplier_link($arr['productautoid'], $arr['typeid'], false);
                        if (!empty($supplieremail)) {
                            self::order_email($supplieremail, $title, $content);
                        }
                    }
                }

                //定义平台
                Common::session('_platform', 'mobile');
            }


        }

        return $flag;


    }


    /*
    * 库存操作
    * */
    public static function minus_storage($dingdate, $typeid, $suitid, $productid, $dingnum, $departdate = '')
    {

        $day = strtotime($dingdate);
        $dingnum = $dingnum ? $dingnum : 1;
        switch ($typeid) {
            case '1':

                $sql = "UPDATE `sline_line_suit_price` SET number=number-$dingnum WHERE day='$day' AND suitid='$suitid' AND number!=0 AND number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '2':
                $sdate = strtotime($dingdate);
                $edate = strtotime($departdate);
                $sql = "UPDATE `sline_hotel_room_price` ";
                $sql .= "SET number=number-$dingnum ";
                $sql .= "WHERE suitid='$suitid' AND day>=$sdate AND day<$edate AND number!=0 AND number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '3':
                $sql = "UPDATE `sline_car_suit_price` SET number=number-$dingnum WHERE day='$day' AND suitid='$suitid' AND number!=0 and number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '5':
                $sql = "UPDATE `sline_spot_ticket` SET number=number-$dingnum WHERE spotid='$productid' AND id='$suitid' AND number!=0 AND number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '8':
                $sql = "UPDATE `sline_visa` SET number=number-$dingnum WHERE id='$productid' AND number!=0 AND number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '13':
                $sql = "UPDATE `sline_tuan` SET totalnum=totalnum-$dingnum WHERE id='$productid' AND totalnum!=0 AND totalnum!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            default:
                $sql = "UPDATE `sline_model_suit_price` SET number=number-$dingnum WHERE day='$day' AND suitid='$suitid' AND number!=0 AND number!=-1 AND productid='$productid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
        }

    }

    /**
     * 点击率加一
     * @param $aid
     * @param $typeid
     * @return mixed
     */
    public static function update_click_rate($aid, $typeid)
    {
        switch ($typeid) {
            case '1':
                $sql = "UPDATE `sline_line` SET shownum=IFNULL(shownum,0) +1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '2':
                $sql = "UPDATE `sline_hotel` SET shownum=IFNULL(shownum,0)+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '3':
                $sql = "UPDATE `sline_car` SET shownum=IFNULL(shownum,0)+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '4':
                $sql = "UPDATE `sline_article` SET shownum=IFNULL(shownum,0)+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '5':
                $sql = "UPDATE `sline_spot` SET shownum=IFNULL(shownum,0)+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '6':
                $sql = "UPDATE `sline_photo` SET shownum=IFNULL(shownum,0)+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '8':
                $sql = "UPDATE `sline_visa` SET shownum=IFNULL(shownum,0)+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '11':
                $sql = "UPDATE `sline_jieban` SET shownum=IFNULL(shownum,0)+1 WHERE id='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '13':
                $sql = "UPDATE `sline_tuan` SET shownum=IFNULL(shownum,0)+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
        }
    }

    /**
     * 获取消息msg定义
     * @param $msgtype
     * @param int $num
     * @param string $ext
     * @return mixed
     */
    public static function get_define_msg($msgtype, $num = 0, $ext = '')
    {
        if (is_numeric($msgtype)) {
            $msgtype = self::get_msg_type($msgtype, $num);
        }
        if (!empty($ext)) {
            $msgtype = $ext;
        }
        $sql = "SELECT * FROM `sline_sms_msg` WHERE msgtype='{$msgtype}'";
        $row = DB::query(1, $sql)->execute()->as_array();
        return $row[0];
    }

    /*
     * 根据typeid生成msgtype
     * @param int $typeid
     * @param int $num ,第几个状态.
     * @return string $msgtype
     * */
    public static function get_msg_type($typeid, $num)
    {
        $sql = "select pinyin,correct,maintable from sline_model where id={$typeid}";
        $arr = DB::query(Database::SELECT, $sql)->execute()->as_array();

        $msgtype = 'reg';
        if (count($arr) > 0) {
            if ($arr[0]['maintable'] == "model_archive")
                $msgtype = "tongyong_order_msg" . $num;
            else
                $msgtype = $arr[0]['pinyin'] . '_order_msg' . $num;
        }
        return $msgtype;
    }


    /**
     * @param $orderinfo
     * @return array
     */
    public static function calculate_price($orderinfo)
    {
        $result = array(
            'totalNumber' => 0,
            'totalPrice' => 0,
            'numberDescript' => '',
            'priceDescript' => ''
        );

        if (is_array($orderinfo)) {
            //如果typeid为2,则要计算预订开始与结束日期的总价
            if ($orderinfo['typeid'] == 2) {
                $dingnum = $orderinfo['dingnum'];
                $suitid = $orderinfo['suitid'];
                $startdate = $orderinfo['usedate'];
                $leavedate = $orderinfo['departdate'];
                $totalprice = Model_Hotel::suit_range_price($suitid, $startdate, $leavedate, $dingnum);
                $totalprice = $totalprice * $dingnum;

                $result['numberDescript'] = $dingnum;
                $result['totalPrice'] = $totalprice;

            }
            else {

                $totalPrice = $orderinfo['price'] * $orderinfo['dingnum'] + $orderinfo['childnum'] * $orderinfo['childprice'] + $orderinfo['oldnum'] * $orderinfo['oldprice'];
                $result['totalPrice'] = $totalPrice;
                $totalNumber = $orderinfo['dingnum'] + $orderinfo['childnum'] + $orderinfo['oldnum'];
                $result['totalNumber'] = $totalNumber;

                $priceDescript = '';
                $numberDescript = '';
                if (!empty($orderinfo['dingnum'])) {
                    $priceDescript = $priceDescript . $orderinfo['price'] . '(成)';
                    $numberDescript = $numberDescript . $orderinfo['dingnum'] . '(成)';
                }
                if (!empty($orderinfo['childnum'])) {
                    $priceDescript = $priceDescript . $orderinfo['childprice'] . '(小)';
                    $numberDescript = $numberDescript . $orderinfo['childnum'] . '(小)';
                }
                if (!empty($orderinfo['oldnum'])) {
                    $priceDescript = $priceDescript . $orderinfo['oldprice'] . '(老)';
                    $numberDescript = $numberDescript . $orderinfo['oldnum'] . '(老)';
                }
                $result['priceDescript'] = $priceDescript;
                $result['numberDescript'] = $numberDescript;


            }

        }
        return $result;
    }


    /**
     * @param $productid
     * @param $typeid
     * @return string
     * 获取供应商手机号码
     */
    public static function get_supplier_link($productid, $typeid, $istel = true)
    {
        $module_info = ORM::factory('model', $typeid)->as_array();
        //$table=$channeltable[$typeid];
        $table = 'sline_' . $module_info['maintable'];
        $sql = "SELECT supplierlist FROM {$table} where id='$productid'";
        $row = DB::query(1, $sql)->execute()->as_array();
        $supplierid = $row[0]['supplierlist'];
        $sql = "SELECT * FROM `sline_supplier` WHERE id='$supplierid'";
        $row = DB::query(1, $sql)->execute()->as_array();

        $result = ($istel == true ? $row[0]['mobile'] : $row[0]['email']);
        return $result ? $result : '';
    }

    /*
    * 发送短信方法
    * @param int phone
    * @param string prefix
    * @param string content
    * */
    public static function send_msg($phone, $prefix, $content)
    {
        require_once TOOLS_COMMON . 'sms/smsservice.php';

        $status = SMSService::send_msg($phone, $prefix, $content);
        $status = json_decode($status);
        return $status;
    }

    /**
     * @param $msgtype
     * @param $num
     * @return array
     */
    public static function get_email_msg($msgtype, $num = 0)
    {
        //参数为数字则为栏目ID
        if (is_numeric($msgtype)) {
            $msgtype = self::get_msg_type($msgtype, $num);
        }
        $sql = "SELECT * FROM `sline_email_msg` WHERE msgtype='$msgtype'";
        $ar = DB::query(1, $sql)->execute()->as_array();
        $row = $ar[0] ? $ar[0] : array();
        return $row;
    }

    /**
     * @param $maillto
     * @param $title
     * @param $content
     * @return bool
     * 发送邮件
     */

    public static function order_email($maillto, $title, $content)
    {
        require_once TOOLS_COMMON . 'email/emailservice.php';
        $status = EmailService::send_email($maillto, $title, $content);
        return $status;
    }

    /**
     * @param $memberid
     * @param $content
     * @param $jifen
     * @param $type
     * 添加积分日志
     */

    public static function add_jifen_log($memberid, $content, $jifen, $type)
    {
        $model = ORM::factory('member_jifen_log');
        $model->memberid = $memberid;
        $model->content = $content;
        $model->jifen = $jifen;
        $model->type = $type;
        $model->addtime = time();
        $model->save();
    }

    //在线支付公共接口
    /*-
	   $ordersn:订单编号
	   $subject:商品名称
	   $price:总价
	   $showurl:商品url
	-*/

    public static function pay_online($ordersn, $subject, $price, $paytype, $showurl = '', $extra_para = '', $widbody = '')
    {


        if ($paytype == 1) //支付宝
        {
            $showurl = empty($showurl) ? $GLOBALS['cfg_cmspath'] : $showurl;
            $payurl = $GLOBALS['cfg_phone_cmspath'] . '/thirdpay/alipay';

            $html = "<form method='post' action='{$payurl}' name='alipayfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="widbody" value="' . $widbody . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';
            $html .= '<input type="hidden" name="extra_common_param" value="' . $extra_para . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['alipayfrm'].submit();</script>";
            return $html;


        }
        else if ($paytype == 2)  //快钱支付
        {
            $payurl = $GLOBALS['cfg_phone_cmspath'] . '/thirdpay/bill';

            $html = "<form method='post' action='{$payurl}' name='billfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';
            $html .= '</form>';
            $html .= "<script>document.forms['billfrm'].submit();</script>";
            return $html;
        }
        else if ($paytype == 3) //微信支付
        {
            $payurl = $GLOBALS['cfg_phone_cmspath'] . '/thirdpay/weixinpay';
            $html = "<form method='post' action='{$payurl}' name='alipayfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="widbody" value="' . $widbody . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';
            $html .= '<input type="hidden" name="extra_common_param" value="' . $extra_para . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['alipayfrm'].submit();</script>";
            return $html;
        }
    }

    /**
     * 列表搜索页格式化
     * @param $data
     * @return mixed|string
     */
    public static function list_search_format($data, $page, $pagesize = 10)
    {
        $result['list'] = $data;
        $result['page'] = count($data) < $pagesize ? -1 : $page + 1;
        return json_encode($result);
    }

    /**
     * @param $ico
     * @return array
     * @desc 获取图标数组
     */
    public static function get_ico_list($ico)
    {
        $arr = array();
        if (!empty($ico)) {
            $sql = "SELECT picurl FROM `sline_icon` WHERE id IN($ico)";
            $arr = DB::query(1, $sql)->execute()->as_array();
            foreach ($arr as &$r) {
                $r['litpic'] = Common::img($r['picurl']);
            }
        }
        return $arr;
    }

    /**
     * 主站域名
     * @return mixed
     */
    public static function get_host_url()
    {
        $sql = "SELECT weburl FROM `sline_weblist` WHERE webid=0 ORDER BY id ASC LIMIT 1";
        $arr = DB::query(1, $sql)->execute()->current();
        return $arr['weburl'];
    }

    /**
     * @param $product_code ,产品编号如:stourwebcms_app_supplierlinemanage
     * @return int
     * @desc 检测app是否安装,安装返回1,没有安装返回0
     */
    public static function is_app_install($product_code)
    {
        $sql = "SELECT id FROM `sline_app` WHERE productcode = '$product_code'";
        $row = DB::query(1, $sql)->execute()->as_array();
        return !empty($row[0]['id']) ? 1 : 0;
    }

    /**
     * @return array
     * 获取登陆用户信息
     */
    public static function get_login_user_info()
    {
        $member = Common::session('member');
        return $member;
    }

    /*
     * 格式化日期显示
     * */
    public static function format_addtime($time)
    {
        /*$time=time()-$time;
     $year = floor($time / 60 / 60 / 24 / 365);
     $time -= $year * 60 * 60 * 24 * 365;
     $month = floor($time / 60 / 60 / 24 / 30);
     $time -= $month * 60 * 60 * 24 * 30;
     $week = floor($time / 60 / 60 / 24 / 7);
     $time -= $week * 60 * 60 * 24 * 7;
     $day = floor($time / 60 / 60 / 24);
     $time -= $day * 60 * 60 * 24;
     $hour = floor($time / 60 / 60);
     $time -= $hour * 60 * 60;
     $minute = floor($time / 60);
     $time -= $minute * 60;
     $second = $time;*/

        //这里修改读随机的.
        $hour = mt_rand(0, 3);
        $minute = mt_rand(0, 60);
        $second = mt_rand(0, 60);
        $elapse = '';
        $unitArr = array(
            '年' => 'year',
            '个月' => 'month',
            '周' => 'week',
            '天' => 'day',
            '小时' => 'hour',
            '分钟' => 'minute',
            '秒' => 'second'
        );
        foreach ($unitArr as $cn => $u) {
            if ($$u > 0) {
                $elapse = $$u . $cn;
                break;
            }
        }


        return $elapse . '前';
    }

    /**
     * @function 替换内同中的strong标签为span
     * @param $content 内容
     *
     */
    public static function replace_strong_to_span($content)
    {
        $content = str_replace('<strong>', '<span style="font-weight: bold">', $content);
        $content = str_replace('</strong>', '</span>', $content);
        return $content;
    }

}
