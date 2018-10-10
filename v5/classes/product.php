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
        foreach ($seoArr as &$v)
        {
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
        if (empty($picStr))
        {
            return;
        }
        $arr = explode(',', $picStr);
        foreach ($arr as &$v)
        {
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
            'T' => '26',
            'U' => '104'
        );
        $str = $id;
        return array_search($prefixId, $arr) . str_pad($str, 6, "0", STR_PAD_LEFT);
    }

    /**
     * 产品内容页去除style 图片如为相对路径加上图片域名
     * @param $str
     * @return mixed
     */
    public static function strip_style($str)
    {
        $str = preg_replace('~\s?style=".*?"~', '', $str);
        $str = preg_replace('~<([^>]*)>(?:\s|&nbsp;)*</\1>~', '', $str);
        $str = preg_replace('~src="[^http](.*?)"~', "src=\"{$GLOBALS['cfg_m_main_url']}/\\1\"", $str);
        $str = preg_replace(array('~width\s*=\s*([\'"]).*?\1~', '~height\s*=\s*([\'"]).*?\1~'), '', $str);
        return $str;
    }

    /*
     * 属性生成where条件,用于多条件属性搜索.
     * */
    public static function get_attr_where($attrid)
    {
        $arr = Common::remove_arr_empty(explode('_', $attrid));
        foreach ($arr as $value)
        {
            $value = intval($value);
            if ($value != 0)
            {
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

    /*
     * 生成子订单编号
     */
    public static function get_sub_ordersn($kind)
    {
        /* 选择一个随机的方案 */
        return 'SUB' . $kind . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    }


    //添加订单
    public static function add_order($arr)
    {
        $model = ORM::factory('member_order');
        $flag = 0;
        if (is_array($arr))
        {
            //添加供应商信息
            $arr['supplierlist'] = self::get_product_supplier($arr['typeid'], $arr['productautoid']);

            if ($arr['paytype'] == '3')//这里补充一个当为二次确认时,修改订单为未处理状态.
            {
                $arr['status'] = 0;
            }
            if (empty($arr['memberid']))
            {
                $arr['memberid'] = self::auto_reg($arr['linktel']);
            }
            foreach ($arr as $k => $v)
            {
                $model->$k = $v;
            }
            $model->save();
            $flag = $model->saved();

            if ($flag)
            {

                $detectresult = Model_Member_Order_listener::detect($arr['ordersn']);
                if ($detectresult !== true)
                {
                    return false;
                }

                //下单成功,设置当前平台PC
                Common::session('_platform', 'pc');
                //减库存
                $dingnum = intval($arr['dingnum']) + intval($arr['childnum']) + intval($arr['oldnum']);
                if ($arr['typeid'] != 2)
                {
                    self::minus_storage($arr['usedate'], $arr['typeid'], $arr['suitid'], $arr['productautoid'], $dingnum);
                }
                else
                {
                    self::minus_storage($arr['usedate'], $arr['typeid'], $arr['suitid'], $arr['productautoid'], $dingnum, $arr['departdate']);
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
                        self::send_msg($mobile, $prefix, $content);//发送短信.
                    }
                    $emailInfo = self::get_email_msg($arr['typeid'], 1);
                    if ($emailInfo['isopen'] == 1 && $memberinfo['email'])
                    {
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
                    if ($msgInfo['isopen'] == 1)
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
                        self::send_msg($mobile, $prefix, $content);//发送短信.
                    }
                    $emailInfo = self::get_email_msg($arr['typeid'], 2);
                    if ($emailInfo['isopen'] == 1 && $memberinfo['email'])
                    {
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
                if ($GLOBALS['cfg_supplier_msg_open'] == 1 && !empty($GLOBALS['cfg_supplier_msg']))
                {
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
                    if (!empty($cfg_webmaster_phone))
                    {
                        self::send_msg($cfg_webmaster_phone, $prefix, $content);//发送短信
                    }

                    if ($GLOBALS['cfg_supplier_send_open'] == 1)
                    {
                        $supplierphone = self::get_supplier_link($arr['productautoid'], $arr['typeid']);
                        if (!empty($supplierphone))
                        {
                            self::send_msg($supplierphone, $prefix, $content);//发送短信.
                        }
                    }
                }

                //供应商email发送
                if ($GLOBALS['cfg_supplier_email_open'] == 1 && !empty($GLOBALS['cfg_supplier_emailmsg']))
                {
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
                    if (!empty($cfg_webmaster_email))
                    {
                        self::order_email($cfg_webmaster_email, $title, $content);
                    }

                    if ($GLOBALS['cfg_supplier_sendemail_open'] == 1)
                    {
                        $supplieremail = self::get_supplier_link($arr['productautoid'], $arr['typeid'], false);
                        if (!empty($supplieremail))
                        {
                            self::order_email($supplieremail, $title, $content);
                        }
                    }
                }
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
        switch ($typeid)
        {
            case '1':

                $sql = "UPDATE `sline_line_suit_price` SET number=number-$dingnum WHERE day='$day' AND suitid='$suitid' AND number!=0 AND number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '2':

                $sdate = strtotime($dingdate);
                $edate = strtotime($departdate);
                $sql = "UPDATE `sline_hotel_room_price` ";
                $sql .= "SET number=number-$dingnum ";
                $sql .= "WHERE suitid='$suitid' AND day>=$sdate AND day<$edate AND number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '3':
                $sql = "UPDATE `sline_car_suit_price` SET number=number-$dingnum WHERE day='$day' AND suitid='$suitid' AND number!=0 and number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '5':
                $sql = "UPDATE `sline_spot_ticket` SET number=number-1 WHERE spotid='$productid' AND id='$suitid' AND number!=0";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '8':
                $sql = "UPDATE `sline_visa` SET number=number-$dingnum WHERE id='$productid' AND number!=0 AND number!=-1";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '13':
                $sql = "UPDATE `sline_tuan` SET totalnum=totalnum-1 WHERE id='$productid' AND totalnum!=0";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
        }


    }


    /**
     *获取消息msg定义
     * @param string msgtype
     */
    public static function get_define_msg($typeid, $num = 0, $msgtype = '')
    {
        $msgtype = empty($msgtype) ? self::get_msg_type($typeid, $num) : $msgtype;
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
        if (count($arr) > 0)
        {
            if ($arr[0]['maintable'] == "model_archive")
            {
                $msgtype = "tongyong_order_msg" . $num;
            }
            else
            {
                $msgtype = $arr[0]['pinyin'] . '_order_msg' . $num;
            }
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

        if (is_array($orderinfo))
        {
            //如果typeid为2,则要计算预订开始与结束日期的总价
            if ($orderinfo['typeid'] == 2)
            {
                $dingnum = $orderinfo['dingnum'];
                $suitid = $orderinfo['suitid'];
                $startdate = $orderinfo['usedate'];
                $leavedate = $orderinfo['departdate'];
                $totalprice = Model_Hotel::suit_range_price($suitid, $startdate, $leavedate, $dingnum);
                $result['numberDescript'] = $dingnum;
                $result['totalPrice'] = $totalprice * $dingnum;

            }
            else
            {

                $totalPrice = $orderinfo['price'] * $orderinfo['dingnum'] + $orderinfo['childnum'] * $orderinfo['childprice'] + $orderinfo['oldnum'] * $orderinfo['oldprice'];
                $result['totalPrice'] = $totalPrice;
                $totalNumber = $orderinfo['dingnum'] + $orderinfo['childnum'] + $orderinfo['oldnum'];
                $result['totalNumber'] = $totalNumber;

                $priceDescript = '';
                $numberDescript = '';
                if (!empty($orderinfo['dingnum']))
                {
                    $priceDescript = $priceDescript . $orderinfo['price'] . '(成)';
                    $numberDescript = $numberDescript . $orderinfo['dingnum'] . '(成)';
                }
                if (!empty($orderinfo['childnum']))
                {
                    $priceDescript = $priceDescript . $orderinfo['childprice'] . '(小)';
                    $numberDescript = $numberDescript . $orderinfo['childnum'] . '(小)';
                }
                if (!empty($orderinfo['oldnum']))
                {
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

        $prefix = $GLOBALS['cfg_webname'];


        $status = SMSService::send_msg($phone, $prefix, $content);
        $status = json_decode($status);
        return $status;


    }


    /**
     * @param $typeid
     * @param $num
     * @return array
     * 获取email配置
     */
    public static function get_email_msg($typeid, $num)
    {

        $msgtype = self::get_msg_type($typeid, $num);
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

    /**
     * @param $orderid 订单id
     * @param $paytype 支付方式
     * @param $usejifen 是否使用积分
     */
    public static function pay($orderid, $paytype, $usejifen)
    {

        $info = ORM::factory('member_order', $orderid)->as_array();

        //使用积分抵现,且当前订单没有使用过积分抵现功能.
        if (intval($usejifen) == 1 && $info['usejifen'] == 0)
        {
            $needjifen = $GLOBALS['cfg_exchange_jifen'] * $info['jifentprice']; //所需积分
            //当前登陆会员
            $member = Common::session('member');
            if ($member)
            {
                $memberid = $member['mid'];
                $flag = Model_Member::operate_jifen($memberid, $needjifen, 1);
                if ($flag)
                {
                    Product::add_jifen_log($memberid, '预订产品' . $info['productname'] . '积分抵现消费积分' . $needjifen, $needjifen, 1);
                    //如果会员积分扣除成功,则写订单信息,使用积分抵现.
                    $m = ORM::factory('member_order', $orderid);
                    $m->usejifen = 1;
                    $m->save();
                }

            }

        }
        if (intval($info['dingjin']) > 0)
        {
            $totalprice = $info['dingjin'] * ($info['dingnum'] + $info['childnum'] + $info['oldnum']);
        }
        else if ($info['typeid'] == 2)
        {
            $totalprice = Model_Hotel::suit_range_price($info['suitid'], $info['usedate'], $info['departdate'], $info['dingnum']);
            $totalprice = $totalprice * $info['dingnum'];
        }
        else if ($info['typeid'] == 3)
        {
            $totalprice = Model_Car::suit_range_price($info['suitid'], $info['usedate'], $info['departdate'], $info['dingnum']);
        }
        else
        {
            $totalprice = $info['price'] * $info['dingnum'] + $info['childnum'] * $info['childprice'] + $info['oldnum'] * $info['oldprice'];
        }

        //再次查询数据库,判断是否使用积分成功,如果成功则重新计算价格.
        $ujifen = ORM::factory('member_order', $orderid)->get('usejifen');
        if (intval($ujifen) == 1)
        {
            $jifentprice = intval($info['jifentprice']);
            $totalprice = intval($totalprice) - $jifentprice;
        }

        if ($info['typeid'] == 1)
        {
            //是否购买保险
            $insAll = ORM::factory('insurance_booking')->where("bookordersn='{$info['ordersn']}'")->get_all();
            foreach ($insAll as $ins)
            {
                $totalprice += intval($ins['payprice']);
            }
            //是否购买单房差,且选择现付
            if (intval($info['roombalancenum']) > 0 && $info['roombalance_paytype'] == 1)
            {
                $roomprice = intval($info['roombalancenum']) * intval($info['roombalance']);
                $totalprice += $roomprice;
            }
        }


        if ($paytype == 0)
        {
            exit('error request');
        }
        echo Product::pay_online($info['ordersn'], $info['productname'], $totalprice, $paytype);

    }


    //在线支付公共接口
    /*-
	   $ordersn:订单编号
	   $subject:商品名称
	   $price:总价
	   $showurl:商品url
	-*/

    /*  public static function pay_online($ordersn,$subject,$price,$paytype,$showurl='',$extra_para='',$widbody='')
      {



          if($paytype==1) //支付宝
          {
              $showurl=empty($showurl)?$GLOBALS['cfg_cmspath']:$showurl;
              $payurl=$GLOBALS['cfg_phone_cmspath'].'thirdpay/alipay';

              $html="<form method='post' action='{$payurl}' name='alipayfrm'>";
              $html.='<input type="hidden" name="ordersn" value="'.$ordersn.'">';
              $html.='<input type="hidden" name="subject" value="'.$subject.'">';
              $html.='<input type="hidden" name="price" value="'.$price.'">';
              $html.='<input type="hidden" name="widbody" value="'.$widbody.'">';
              $html.='<input type="hidden" name="showurl" value="'.$showurl.'">';
              $html.='<input type="hidden" name="extra_common_param" value="'.$extra_para.'">';

              $html.='</form>';
              $html.="<script>document.forms['alipayfrm'].submit();</script>";
              return $html;


          }
          else if($paytype==2)  //快钱支付
          {
              $payurl=$GLOBALS['cfg_phone_cmspath'].'thirdpay/bill';

              $html="<form method='post' action='{$payurl}' name='billfrm'>";
              $html.='<input type="hidden" name="ordersn" value="'.$ordersn.'">';
              $html.='<input type="hidden" name="subject" value="'.$subject.'">';
              $html.='<input type="hidden" name="price" value="'.$price.'">';
              $html.='<input type="hidden" name="showurl" value="'.$showurl.'">';
              $html.='</form>';
              $html.="<script>document.forms['billfrm'].submit();</script>";
              return $html;
          }
          else if($paytype==3) //微信支付
          {
              $payurl=$GLOBALS['cfg_phone_cmspath'].'thirdpay/weixinpay';
              $html="<form method='post' action='{$payurl}' name='alipayfrm'>";
              $html.='<input type="hidden" name="ordersn" value="'.$ordersn.'">';
              $html.='<input type="hidden" name="subject" value="'.$subject.'">';
              $html.='<input type="hidden" name="price" value="'.$price.'">';
              $html.='<input type="hidden" name="widbody" value="'.$widbody.'">';
              $html.='<input type="hidden" name="showurl" value="'.$showurl.'">';
              $html.='<input type="hidden" name="extra_common_param" value="'.$extra_para.'">';

              $html.='</form>';
              $html.="<script>document.forms['alipayfrm'].submit();</script>";
              return $html;
          }


      }*/
    //在线支付
    /*-
	   $ordersn:订单编号
	   $subject:商品名称
	   $price:总价
	   $showurl:商品url
	-*/

    public static function pay_online($ordersn, $subject, $price, $paytype, $showurl = '', $extra_para = '', $widbody = '')
    {

        if (in_array($paytype, array(11, 12, 13, 14)))
        {
            if (!empty($GLOBALS['cfg_alipay_signtype'])
                && !empty($GLOBALS['cfg_alipay_account'])
                && !empty($GLOBALS['cfg_alipay_pid'])
                && !empty($GLOBALS['cfg_alipay_key'])
            )
            {

                $alipaytypeArr = array('11' => 'cash', '12' => 'double', '13' => 'danbao', '14' => 'bank');
                $payurl = $GLOBALS['cfg_basehost'] . '/thirdpay/alipay_' . $alipaytypeArr[$paytype] . '/alipayapi.php';
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
        else if ($paytype == 2)  //快钱支付
        {
            $payurl = $GLOBALS['cfg_basehost'] . '/thirdpay/kuaiqian/send.php';
            $showurl = $GLOBALS['cfg_basehost'] . '/thirdpay/kuaiqian/receive.php';
            $html = "<form method='post' action='{$payurl}' name='billfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['billfrm'].submit();</script>";
            return $html;
        }
        else if ($paytype == 3)
        {
            $payurl = $GLOBALS['cfg_basehost'] . '/huicao/send.php';
            $showurl = $GLOBALS['cfg_basehost'] . '/huicao/receive.php';
            $html = "<form method='post' action='{$payurl}' name='billfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['billfrm'].submit();</script>";
            return $html;

        }
        else if ($paytype == 4) //银联支付
        {
            $payurl = $GLOBALS['cfg_basehost'] . '/yinlian/front.php';
            if ($GLOBALS['cfg_yinlian_type'] == 1)
            {
                $payurl = $GLOBALS['cfg_basehost'] . '/thirdpay/yinlian/front.php';
            }

            $html = "<form method='post' action='{$payurl}' name='yinlianfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['yinlianfrm'].submit();</script>";
            return $html;

        }
        else if ($paytype == 5) //钱包支付
        {
            $payurl = $GLOBALS['cfg_basehost'] . '/thirdpay/qianbao/EBCTradeUrl.php';
            $html = "<form method='post' action='{$payurl}' name='qianbaofrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['qianbaofrm'].submit();</script>";
            return $html;

        }
        else if ($paytype == 7) //paypal支付
        {
            $business = $GLOBALS['cfg_paypal_key'];
            $form_data = array(
                'cmd' => '_xclick',                        // 网站拥有自己的购物车系统
                'business' => $business,         //商家的贝宝账号
                'item_name' => $ordersn,              //订单号
                'amount' => $price,                 //商品总价
                'currency_code' => $GLOBALS['cfg_paypal_currency'],             //使用哪种货币 USD-美元
                'return' => $GLOBALS['cfg_basehost'],// 当用户支付完成后，浏览器会跳转到这个页面，一般情况下无需做复杂操作，直接告诉用户付款成功即可，无需其他逻辑，真正的付款成功与否的通知在notify_url
                'invoice' => $ordersn,
                'charset' => 'UTF-8',                    //网站使用的编码
                'no_shipping' => '1',
                'no_note' => '0',
                'image_url' => 'https://www.paypal.com/en_US/i/logo/paypal_logo.gif',
                'cancel_return' => $GLOBALS['cfg_basehost'],// 如果用户跳转到paypal支付接口后不想继续购买，点击取消付款后会跳转到这个页面
                'notify_url' => $GLOBALS['cfg_basehost'] . '/thirdpay/paypal/notify_url.php?order_id=' . $ordersn,// PAYPAL的服务器会把用户时候付款，付款成功与否的信息发送到这里，用户不感觉得到，这完全是Paypal的服务器向你的服务器发送的数据
                'rm' => '2',
            );

            $html = "<form method='post' action='https://www.paypal.com/cgi-bin/webscr' name='paypalfrm'>";
            foreach ($form_data as $key => $name)
            {
                $html .= '<input type="hidden" name="' . $key . '" value="' . $name . '" />';
            }
            $html .= '</form>';
            $html .= "<script>document.forms['paypalfrm'].submit();</script>";
            return $html;

        }
        else if ($paytype == 8)
        {

            $payurl = $GLOBALS['cfg_basehost'] . '/thirdpay/weixinpay/native.php';

            $html = "<form method='post' action='{$payurl}' name='weixinfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';
            $html .= '</form>';
            $html .= "<script>document.forms['weixinfrm'].submit();</script>";
            return $html;
        }
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
        foreach ($unitArr as $cn => $u)
        {
            if ($$u > 0)
            {
                $elapse = $$u . $cn;
                break;
            }
        }


        return $elapse . '前';
    }

    /**
     * @param $ico
     * @return array
     * @desc 获取图标数组
     */
    public static function get_ico_list($ico)
    {
        $arr = array();
        if (!empty($ico))
        {

            $sql = "SELECT picurl FROM `sline_icon` WHERE id IN($ico)";
            $arr = DB::query(1, $sql)->execute()->as_array();
            foreach ($arr as &$r)
            {
                $r['litpic'] = Common::img($r['picurl']);
            }
        }
        return $arr;
    }


    /*
     * 价格格式化函数,此函数暂时不实现其它功能,后期可能实现货币转换功能
     * */
    public static function price($price)
    {
        $current = 'rmb';

    }

    /**
     * @param $number
     * 数量小写转大写
     */
    public static function to_upper($number)
    {
        $out = '';
        $arr = array("零", "一", "二", "三", "四", "五", "六", "七", "八", "九");
        if (strlen($number) == 1)
        {
            $out = $arr[$number];
        }
        else
        {
            if ($number == 10)
            {
                $out = "十";
            }
            else
            {
                if ($number < 20)
                {
                    $out = "十";
                }
                else
                {
                    $out = $arr[substr($number, 0, 1)] . "十";
                }
                if (substr($number, 1, 1) != "0")
                {
                    $out .= $arr[substr($number, 1, 1)];
                }
            }
        }
        return $out;
    }

    /**
     * @param $attrid
     * @param $typeid
     * @return array
     * @desc 获取属性上级id
     */
    public static function get_attr_parent($attrid, $typeid)
    {
        $attrtable = ORM::factory('model', $typeid)->get('attrtable');
        $orignalArr = explode('_', $attrid);
        $out = array();
        foreach ($orignalArr as $atid)
        {
            $sql = "SELECT pid FROM `sline_" . $attrtable . "` WHERE id =" . $atid;
            $ar = DB::query(1, $sql)->execute()->as_array();
            if (isset($ar[0]['pid']))
            {
                $out[$ar[0]['pid']] = $atid;
            }

        }
        return $out;


    }


    /**
     *
     * @param $year
     * @param $month
     * @param $suitid
     * @param $typeid
     * @param string $startdate
     * @param array $ext
     * @return array
     */
    public static function get_suit_price($year, $month, $suitid, $typeid, $startdate = '', $ext = array())
    {
        $priceTable = array(
            '1' => 'line_suit_price',
            '2' => 'hotel_room_price',
            '3' => 'car_suit_price',
            '5' => 'spot_ticket_price'
        );
        $priceTable = $priceTable + $ext;
        $field = $typeid == 5 ? 'ticketid' : 'suitid';
        $start = !empty($startdate) ? strtotime($startdate) : strtotime("$year-$month-1");
        $end = strtotime("$year-$month-31");
        $table = $priceTable[$typeid];
        $arr = ORM::factory($table)
            ->where("{$field}=$suitid")
            ->and_where('day', '>=', $start)
            ->and_where('day', '<=', $end)
            ->and_where('number', '!=', 0)
            ->get_all();

        $price = array();
        foreach ($arr as $row)
        {
            if ($row)
            {

                $day = $row['day'];
                $price[$day]['date'] = Common::mydate('Y-m-d', $row['day']);
                $price[$day]['basicprice'] = isset($row['adultbasicprice']) ? $row['adultbasicprice'] : $row['basicprice'];
                $price[$day]['basicprice'] = Currency_Tool::price($price[$day]['basicprice']);
                $price[$day]['profit'] = isset($row['adultprofit']) ? $row['adultprofit'] : $row['profit'];
                $price[$day]['profit'] = Currency_Tool::price($price[$day]['profit']);
                $price[$day]['price'] = isset($row['adultprice']) ? $row['adultprice'] : $row['price'];
                $price[$day]['price'] = Currency_Tool::price($price[$day]['price']);
                $price[$day]['child_basicprice'] = isset($row['childbasicprice']) ? $row['childbasicprice'] : 0;
                $price[$day]['child_basicprice'] = Currency_Tool::price($price[$day]['child_basicprice']);

                $price[$day]['child_profit'] = isset($row['childprofit']) ? $row['childprofit'] : 0;
                $price[$day]['child_profit'] = Currency_Tool::price($price[$day]['child_profit']);

                $price[$day]['child_price'] = isset($row['childprice']) ? $row['childprice'] : 0;
                $price[$day]['child_price'] = Currency_Tool::price($price[$day]['child_price']);


                $price[$day]['old_basicprice'] = isset($row['oldbasicprice']) ? $row['oldbasicprice'] : 0;
                $price[$day]['old_basicprice'] = Currency_Tool::price($price[$day]['old_basicprice']);

                $price[$day]['old_profit'] = isset($row['oldprofit']) ? $row['oldprofit'] : 0;
                $price[$day]['old_profit'] = Currency_Tool::price($price[$day]['old_profit']);
                $price[$day]['old_price'] = isset($row['oldprice']) ? $row['oldprice'] : 0;
                $price[$day]['old_price'] = Currency_Tool::price($price[$day]['old_price']);

                $price[$day]['suitid'] = $suitid;
                $price[$day]['number'] = $row['number'];//库存
                $price[$day]['description'] = $row['description'];//描述

            }


        }
        return $price;
    }

    /**
     * 获取默认图片
     */
    public static function get_default_image()
    {
        return !empty($GLOBALS['cfg_df_img']) ? $GLOBALS['cfg_df_img'] : "/templets/smore/images/pic_tem.jpg";
    }

    /**
     * @param $mobile
     * @return int|mixed
     * @desc 自动按手机号注册帐号
     */
    public static function auto_reg($mobile)
    {

        $out = 0;
        $sql = "SELECT mid FROM `sline_member` WHERE mobile='$mobile'";
        $row = DB::query(1, $sql)->execute()->as_array();
        if (!empty($row[0]['mid']))
        {
            $out = $row[0]['mid'];
        }
        else
        {
            $pwd = md5($mobile);
            $jointime = time();
            $joinip = Common::get_ip();
            $jifen = empty($GLOBALS['cfg_reg_jifen']) ? 0 : $GLOBALS['cfg_reg_jifen'];//网上注册赠送积分
            $nickname = substr($mobile, 0, 5) . '***';
            $m = ORM::factory('member');
            $member = array(
                'nickname' => $nickname,
                'pwd' => $pwd,
                'jointime' => $jointime,
                'email' => '',
                'mobile' => $mobile,
                'joinip' => $joinip,
                'jifen' => $jifen
            );
            foreach ($member as $key => $value)
            {
                $m->$key = $value;
            }
            $m->save();
            if ($m->saved())
            {
                //$content="尊敬的用户{$mobile}你好,你已经成功注册成为{$GLOBALS['cfg_webname']}会员,你的登陆名是:{$mobile},密码是:{$mobile},为了你的帐户安全,请尽快修改密码!";

                $msgInfo = self::get_define_msg(0, 0);

                if ($msgInfo['isopen'] == 1)
                {

                    $content = $msgInfo['msg'];
                    $content = str_replace('{#LOGINNAME#}', $mobile, $content);
                    $content = str_replace('{#PASSWORD#}', $mobile, $content);
                    $content = str_replace('{#WEBNAME#}', $GLOBALS['cfg_webname'], $content);
                    $content = str_replace('{#PHONE#}', $GLOBALS['cfg_phone'], $content);
                    self::send_msg($mobile, '', $content);//注册短信
                }
                $out = $m->mid;

            }

        }
        return $out;


    }

    /**
     * @param $msgtype 消息类型如reg_msgcode
     * @return array
     */
    public static function get_email_msg_config($msgtype)
    {
        $row = ORM::factory('email_msg')
            ->where("msgtype='{$msgtype}'")
            ->find()
            ->as_array();
        return $row;

    }


    /*
     * 获取使用模板
     * */
    public static function get_use_templet($pagename)
    {
        global $sys_webid;
        $templet = '';
        if ($sys_webid == 0) //主站
        {
            $sql = "SELECT b.path FROM `sline_page` a LEFT JOIN `sline_page_config` b ON a.id=b.pageid WHERE a.pagename='$pagename' AND b.isuse = 1";
            $row = DB::query(1, $sql)->execute()->as_array();
            if (isset($row[0]['path']))
            {
                $templet = 'usertpl/' . $row[0]['path'] . '/index';

            }
        }
        else
        {
            $sql = "SELECT b.path FROM `sline_page` a LEFT JOIN `sline_site_page_config` b ON a.id=b.pageid WHERE a.pagename='$pagename' AND b.isuse = 1 AND b.webid='$sys_webid'";
            $row = DB::query(1, $sql)->execute()->as_array();;
            if (isset($row[0]['path']))
            {
                $templet = 'usertpl/' . $row[0]['path'] . '/index';
            }
            else
            {
                if (!in_array($pagename, array('header', 'footer')))
                {
                    $file = 'substation/' . $pagename;
                    $path = Kohana::find_file('views', $file);
                    if (!empty($path))
                    {
                        $templet = $file;
                    }
                }
                else
                {
                    $row = DB::query(1, "SELECT b.path FROM `sline_page` a LEFT JOIN `sline_page_config` b ON a.id=b.pageid WHERE a.pagename='{$pagename}' AND b.isuse = 1")->execute()->current();
                    if ($row)
                    {
                        $templet = "usertpl/{$row['path']}/index";
                    }
                }
            }
        }
        return $templet;
    }

    /**
     * 点击率加一
     * @param $aid
     * @param $typeid
     * @return mixed
     */
    public static function update_click_rate($aid, $typeid)
    {
        $aid = intval($aid);
        switch ($typeid)
        {
            case '1':
                $sql = "UPDATE `sline_line` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '2':
                $sql = "UPDATE `sline_hotel` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '3':
                $sql = "UPDATE `sline_car` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '4':
                $sql = "UPDATE `sline_article` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '5':
                $sql = "UPDATE `sline_spot` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '6':
                $sql = "UPDATE `sline_photo` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '8':
                $sql = "UPDATE `sline_visa` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '11':
                $sql = "UPDATE `sline_jieban` SET shownum=shownum+1 WHERE id='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '13':
                $sql = "UPDATE `sline_tuan` SET shownum=shownum+1 WHERE aid='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
            case '101':
                $sql = "UPDATE `sline_notes` SET shownum=shownum+1 WHERE id='$aid'";
                DB::query(Database::UPDATE, $sql)->execute();
                break;
        }
    }

    /**
     * @return array
     * 获取登陆用户信息
     */
    public static function get_login_user_info()
    {
        $uid = Cookie::get('st_userid') ? Cookie::get('st_userid') : 0;
        $userInfo = array();
        if (!empty($uid))
        {
            $userInfo = Model_Member::get_member_byid($uid);
        }
        return $userInfo;
    }

    /**
     * @param $destid
     * 获取目的地上级
     */
    public static function get_predest($destid)
    {
        $loopid = intval($destid);

        $result = array();
        $k = 1;

        while (1)
        {
            $pid = DB::select('pid')->from('destinations')->where('id', '=', $loopid)->execute()->get('pid');


            $pinfo = DB::select('id', 'pid', 'pinyin', 'kindname')->from('destinations')->where('id', '=', $pid)->execute()->current();
            if (empty($pinfo['id']))
            {
                break;
            }
            else
            {
                $result[] = $pinfo;
                $loopid = $pinfo['id'];
            }
            if ($k == 5)
            {
                break;
            }
            $k++;

        }
        $count = count($result);
        for ($i = $count - 1; $i >= 0; $i--)
        {
            $newresult[] = $result[$i];
        }

        $destinfo = DB::select('id', 'pid', 'pinyin', 'kindname')->from('destinations')->where('id', '=', $destid)->execute()->current();
        $newresult[] = $destinfo;
        return $newresult;
    }

    /**
     * 获取产品的供应商
     * @param $typeid
     * @param $productautoid
     * @return string
     *
     */
    public static function get_product_supplier($typeid, $productautoid)
    {
        $supplierlist = '';
        $sql = "SELECT * FROM `sline_model` WHERE id=$typeid";
        $modelrow = DB::query(1, $sql)->execute()->current();
        if ($modelrow != null)
        {
            $cksql = "show columns from `sline_{$modelrow['maintable']}` like 'supplierlist'";
            $chrow = DB::query(1, $cksql)->execute()->current();


            if (count($chrow) > 0)
            {
                $s = "SELECT supplierlist FROM sline_{$modelrow['maintable']} where id=$productautoid";
            }
            $productsupplier = DB::query(1, $s)->execute()->current();;
            if ($productsupplier != null)
            {
                $supplierlist = $productsupplier['supplierlist'];

            }

        }
        return $supplierlist;
    }

    /**
     * 获取网站的所有支付方式
     */
    public static function get_paytype_list()
    {
        return Model_Payset::get_payset_list();
    }

    /**
     * 更新积分
     * @param $memberid 会员id
     * @param $jifen 新的积分值
     * @return object
     */
    public static function set_new_jifen($memberid, $jifen)
    {
        $sql = "update `sline_member` set `jifen`=$jifen where `mid`=$memberid";
        $res = DB::query(Database::UPDATE, $sql)->execute();
        return $res;
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
     * @return string
     * 获取异步加载前的图片
     */
    public static function get_lazy_img()
    {
        return $GLOBALS['cfg_res_url'] . 'images/grey.gif';
    }

    /**
     * @function 内容页获取评论
     * @param $params
     * @return mixed
     */
    public static function content_comment($params)
    {
        $obj = DB::select()->from('comment')->where('typeid', '=', $params['typeid'])->and_where('articleid', '=', $params['articleid'])->and_where('isshow', '=', 1)->order_by('addtime', 'DESC');
        $total = $obj->execute();
        $result = $obj->limit($params['row'])->offset(($params['page'] - 1) * $params['row'])->execute()->as_array();
        foreach ($result as $k => $v)
        {
            $result[$k]['member'] = array();
            $result[$k]['reply'] = array();
            $result[$k]['addtime'] = date('Y-m-d H:i:s', $result[$k]['addtime']);
            $result[$k]['member'] = self::init_member($v['memberid']);
            if ($v['dockid'])
            {
                $tempInfo = DB::select()->from('comment')->where('id', '=', $v['dockid'])->and_where('isshow', '=', 1)->execute()->current();
                if ($tempInfo)
                {
                    list($tempInfo['litpic'], $tempInfo['nickname']) = self::init_member($tempInfo['memberid'], true);
                    $tempInfo['addtime'] = date('Y-m-d H:i:s', $tempInfo['addtime']);
                    $result[$k]['reply'] = $tempInfo;
                }
            }

            if (!empty($result[$k]['memberid']))
            {

                $memberinfo = Model_Member::get_member_info($result[$k]['memberid']);

                $result[$k]['nickname'] = empty($memberinfo['nickname']) ? '匿名' : Common::cutstr_html($memberinfo['nickname'], 9); //昵称
                $result[$k]['litpic'] = !empty($memberinfo['litpic']) ? $memberinfo['litpic'] : Common::member_nopic();
            }
            else
            {
                $result[$k]['jifencomment'] = $result[$k]['vr_jifencomment'];
                $result[$k]['nickname'] = $result[$k]['vr_nickname'];
                $result[$k]['litpic'] = $result[$k]['vr_headpic'];
            }

            $result[$k]['nickname'] = empty($result[$k]['nickname']) ? '匿名' : $result[$k]['nickname'];
            $result[$k]['litpic'] = empty($result[$k]['litpic']) ? Common::member_nopic() : $result[$k]['litpic'];
        }
        $_data['data'] = $result;
        //分页处理
        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'params', 'page' => $params['page']),
                'view' => 'default/pagination/show',
                'total_items' => count($total),
                'items_per_page' => $params['row'],
                'first_page_in_url' => true,
            ), Request::factory('notes_other')
        );
        $route_array = array(
            'controller' => $params['controller'],
            'action' => 'ajax_comment'
        );
        $pager->route_params($route_array);
        $pager->setup();
        $_data['page'] = $pager->render();
        return $_data;
    }

    /**
     * @function 初始化会员头像及昵称
     * @param $memberId
     * @return mixed
     */
    public static function init_member($memberId, $returnIndex = false)
    {
        $member = array(
            'litpic' => Common::member_nopic(),
            'nickname' => '匿名',
        );
        if ($memberId && ($memberInfo = Model_Member::get_member_info($memberId)))
        {
            if (!$memberInfo['litpic'])
            {
                $member['litpic'] = Common::member_nopic();
            }
            else
            {
                $member['litpic'] = $memberInfo['litpic'];
            }
            $member['nickname'] = isset($memberInfo['nickname']) ? $memberInfo['nickname'] : '匿名';
        }
        return $returnIndex ? array_values($member) : $member;
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
