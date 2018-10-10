<?php defined('SYSPATH') or die('No direct script access.');


/**
 * 产品通用管理
 * Class product
 */
class St_Product
{
    /**
     * @function 获取产品详情
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
                if (!class_exists('Model_' . $table))
                {
                    return $out;
                }
                $infoModel = 'Model_' . $table;
                if (!is_callable(array($infoModel, 'custom_info')))
                {
                    $info = ORM::factory($table, $productid)->as_array();
                    $py = empty($model->correct) ? $pinyin : $model->correct;
                    $url = St_Functions::get_web_url($info['webid']) . "/{$py}/show_{$info['aid']}.html";
                    $info['url'] = $url;
                    $out = $info;
                }
                else
                {
                    $out = $infoModel::custom_info($productid);
                }
            }
        }
        return $out;
    }

    /**
     * @function 写入订单
     * @param $arr
     * @param $productModel
     * @return bool|int
     */
    public static function add_order($arr, $productModel, $params)
    {
        $flag = 0;
        $model = ORM::factory('member_order');
        if (is_array($arr))
        {
            $db = Database::instance();
            $db->begin();
            try
            {
                //减存
                $dingnum = intval($arr['dingnum']) + intval($arr['childnum']) + intval($arr['oldnum']);
                $bool = call_user_func(array($productModel, 'storage'), $arr['suitid'], '-' . $dingnum, $params);
                if (!$bool)
                {
                    throw new Exception('order_inventoryShortage');
                }
                //添加供应商信息
                $arr['supplierlist'] = self::get_product_supplier($arr['typeid'], $arr['productautoid']);
                if ($arr['paytype'] == '3')//这里补充一个当为二次确认时,修改订单为未处理状态.
                {
                    $arr['status'] = 0;
                }
                if (empty($arr['memberid']))
                {
                    $member_id = Cookie::get('st_userid');
                    $arr['memberid'] = $member_id ? $member_id : self::auto_reg($arr['linktel']);
                }
                if (empty($arr['typeid']))
                {
                    $arr['typeid'] = 999;
                }
                foreach ($arr as $k => $v)
                {
                    if ($k == 'ordersn')
                    {
                        //订单号重复性检测<最多9999>，未满足则直接抛出异常
                        $max_cycle = 99999;
                        while ($max_cycle > 0)
                        {
                            if (!DB::select('ordersn')->from('member_order')->where('ordersn', '=', $arr['ordersn'])->execute()->current())
                            {
                                break;
                            }
                            else
                            {
                                $arr['ordersn'] = St_Product::get_ordersn($arr['typeid']);
                            }
                            $max_cycle--;
                        }
                        if ($max_cycle < 1)
                        {
                            call_user_func(array($productModel, 'storage'), $arr['suitid'], $dingnum, $params);
                            throw new Exception('order_writeFailure');
                        }
                    }
                    $model->$k = $v;
                }
                $model->save();
                if (!$model->saved())
                {
                    //回滚库存
                    call_user_func(array($productModel, 'storage'), $arr['suitid'], $dingnum, $params);
                    throw new Exception('order_writeFailure');
                }
                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();
                Request::$current->redirect('error/tips?msg=' . $e->getMessage());
                return;
            }
            //预订送积分
            $model->jifenbook_id = $arr['jifenbook'];
            $model->jifenbook = self::calculate_jifenbook($arr['jifenbook'], $model->typeid, $model->ordersn);
            $model->update();

            //添加日志
            Model_Member_Order_Log::add_log($model->as_array());

            //扣除积分
            if ($arr['usejifen'] && $arr['needjifen'])
            {
                Model_Member_Jifen::deduct($arr['ordersn']);
            }


            Model_Message::add_order_msg($model->as_array());
            //订单监控
            $detectresult = Model_Member_Order::detect($arr['ordersn']);
            //   var_dump($detectresult);exit;
            if ($detectresult !== true)
            {
                return false;
            }


            //用户通知信息
            if ($arr['status'] == '0')
            {
                St_SMSService::send_product_order_msg(NoticeCommon::PRODUCT_ORDER_UNPROCESSING_MSGTAG, $arr);
                St_EmailService::send_product_order_email(NoticeCommon::PRODUCT_ORDER_UNPROCESSING_MSGTAG, $arr);
            }
            else
            {
                St_SMSService::send_product_order_msg(NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG, $arr);
                St_EmailService::send_product_order_email(NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG, $arr);
            }


            //返回状态
            $flag = 1;
        }
        return $flag;
    }

    /**
     * @function 获取产品的供应商
     * @param $typeid
     * @param $productautoid
     * @return string
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
            if (!empty($chrow))
            {
                $s = "SELECT supplierlist FROM sline_{$modelrow['maintable']} where id=$productautoid";
                $productsupplier = DB::query(1, $s)->execute()->current();;
                if ($productsupplier != null)
                {
                    $supplierlist = $productsupplier['supplierlist'];
                }
            }
        }
        return $supplierlist;
    }

    /**
     * @function 根据手机号自动注册账号
     * @param $mobile
     * @return int|mixed
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
            $joinip = St_Functions::get_ip();
            //  $jifen = empty($GLOBALS['cfg_reg_jifen']) ? 0 : $GLOBALS['cfg_reg_jifen'];//网上注册赠送积分
            $nickname = substr($mobile, 0, 5) . '***';
            $m = ORM::factory('member');
            $member = array(
                'nickname' => $nickname,
                'pwd' => $pwd,
                'jointime' => $jointime,
                'logintime' => $jointime,
                'email' => '',
                'mobile' => $mobile,
                'joinip' => $joinip,
                'jifen' => 0
            );
            foreach ($member as $key => $value)
            {
                $m->$key = $value;
            }
            $m->save();
            if ($m->saved())
            {
                $out = $m->mid;
                $jifen = Model_Jifen::reward_jifen('sys_member_register', $m->mid);
                if (!empty($jifen))
                {
                    self::add_jifen_log($m->mid, "注册赠送积分{$jifen}", $jifen, 2);
                }
                Plugin_Core_Factory::factory()->add_listener('on_member_register', $m->as_array())->execute();
                St_SMSService::send_member_msg($mobile, NoticeCommon::MEMBER_REG_MSGTAG, $mobile, $mobile, '');
            }
        }
        return $out;
    }

    /**
     * @function 获取短信内容模板
     * @param $typeid
     * @param int $num
     * @param string $msgtype
     * @return mixed
     */
    public static function get_define_msg($typeid, $num = 0, $msgtype = '')
    {
        $msgtype = empty($msgtype) ? self::get_msg_type($typeid, $num) : $msgtype;
        $sql = "SELECT * FROM `sline_sms_msg` WHERE msgtype='{$msgtype}'";
        $row = DB::query(1, $sql)->execute()->as_array();
        return $row[0];
    }

    /**
     * @function 根据typeid获取短信模板类型
     * @param $typeid
     * @param $num
     * @return string
     */
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
     * @function 发送短信
     * @param $phone
     * @param $prefix
     * @param $content
     * @return mixed|string
     */
    public static function send_msg($phone, $prefix, $content)
    {
        if (!$prefix)
        {
            $prefix = $GLOBALS['cfg_webname'];
        }
        $status = St_SMSService::send_msg($phone, $prefix, $content);
        $status = json_decode($status);
        return $status;
    }

    /**
     * @function 获取页面配置
     * @param $pagename
     * @return Array|bool
     * @throws Kohana_Exception
     */
    public static function get_template_list($pagename)
    {
        $model = ORM::factory('page')->where('pagename', '=', $pagename)->find();
        if (!$model->loaded())
        {
            return false;
        }
        $id = $model->id;
        $list = ORM::factory('page_config')->where('pageid', '=', $id)->get_all();
        return $list;
    }

    /**
     * @function 获取或解析产品编号
     * @param $number 产品id或编号
     * @param $typeid
     * @param $decode
     * @return string|array
     */
    public static function product_series($number, $typeid, $decode = false)
    {
        $typeid = (string)$typeid;
        $number = (string)$number;
        if ($decode)
        {
            $len = intval($number[0]);
            $typeid = substr($number, 1, $len);
            $id = substr($number, 1 + $len);
            $id = $id ? $id : $number;
            return array('typeid' => $typeid, 'id' => $id);
        }
        else
        {
            $len = strlen($typeid);
            return $len . $typeid . $number;
        }
    }

    /**
     * @function 为订单添加消费码
     * @param $ordersn
     */
    public static function add_eticketno($ordersn)
    {

        $org_eticketno = DB::select('eticketno')->from('member_order')->where('ordersn', '=', "{$ordersn}")->execute()->get('eticketno');
        if (!empty($org_eticketno))
        {
            return null;
        }
        $eticketno = self::get_eticketno();
        $result = DB::update('member_order')->where('ordersn', '=', "{$ordersn}")->set(array('eticketno' => $eticketno))->execute();
        if ($result)
        {
            return $eticketno;
        }
        else
        {
            return null;
        }
    }

    /**
     * @function 获取消费码
     * @return string
     */
    public static function get_eticketno()
    {

        $eticketno = "";

        while (true)
        {
            $eticketno = substr(self::get_random_number(9), 1, 8);

            $check_sql = "SELECT id FROM `sline_member_order` WHERE eticketno='{$eticketno}'";
            $row = DB::query(1, $check_sql)->execute()->as_array();

            if (count($row) <= 0)
            {
                break;
            }
            sleep(1);
        }
        return $eticketno;
    }

    /**
     * @function 生成随机数
     * @param int $length
     * @return int
     */
    public static function get_random_number($length = 4)
    {
        $min = pow(10, ($length - 1));
        $max = pow(10, $length) - 1;
        return mt_rand($min, $max);
    }

    /**
     * @param $year
     * @param $month
     * @param $suitid
     * @param $typeid
     * @param $startdate
     * @return array
     * @desc 获取产品套餐报价
     */
    public static function get_suit_price($year, $month, $suitid, $typeid, $startdate = '')
    {
        $priceTable = array(
            '1' => 'line_suit_price',
            '2' => 'hotel_room_price',
            '3' => 'car_suit_price',
            '5' => 'spot_ticket_price'
        );
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

    /*
  * 生成订单编号
  * */
    public static function get_ordersn($kind)
    {
        return str_pad($kind, 3, 0, STR_PAD_LEFT) . date('ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * @function 报价时间
     * @param  array $params
     * string starttime|开始时间  string endtime|结束时间  string pricerule|报价规则  array weekval|星期报价  array monthval|号数报价
     * 如 $extend = array('start' => '2016-10-1', 'end' => '2016-11-1', 'pricerule' => 'week', 'weekval' => array(1, 5, 6, 7), 'monthval' => array(1, 2, 3));
     * @return array
     */
    public static function offer_date($params)
    {
        $days = array();
        $start = strtotime($params['starttime']);
        $differ_days = (strtotime($params['endtime']) - $start) / 86400;
        //星期天
        if (isset($params['weekval']))
        {
            foreach ($params['weekval'] as $k => $v)
            {
                if ($v == 7)
                {
                    $params['weekval'][$k] = 0;
                }
            }
        }
        for ($i = 0; $i <= $differ_days; $i++)
        {
            $time = strtotime('+' . $i . ' day', $start);
            switch ($params['pricerule'])
            {
                case 'week':
                    if (in_array(date('w', $time), $params['weekval']))
                    {
                        array_push($days, $time);
                    }
                    break;
                case 'month':
                    if (in_array(intval(date('d', $time)), $params['monthval']))
                    {
                        array_push($days, $time);
                    }
                    break;
                default:
                    array_push($days, $time);
            }
        }
        return $days;
    }

    /**
     * @function 计算某个订单的预订送积分
     * @param $jifenid
     * @param $typeid
     * @param $ordersn
     */
    public static function calculate_jifenbook($jifenid, $typeid, $ordersn)
    {
        $jifenbook_info = Model_Jifen::get_used_jifenbook($jifenid, $typeid);
        if (!$jifenbook_info)
        {
            return 0;
        }
        $jifenbook = 0;
        if ($jifenbook_info['rewardway'] == 1)
        {
            $order_info = Model_Member_Order::order_info($ordersn);
            $jifenbook = floor($order_info['actual_price'] * $jifenbook_info['value'] / 100);
        }
        else
        {
            $jifenbook = $jifenbook_info['value'];
        }
        return $jifenbook;

    }

    /**
     * @function  添加积分日志
     * @param $memberid
     * @param $content
     * @param $jifen
     * @param $type
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
     * @function 动态口令
     * @param bool $html
     * @return int|string
     */
    public static function form_token($html = true)
    {
        $time = time();
        Cookie::set('__token__', md5(Cookie::$salt . $time));
        return $html ? sprintf('<input type="hidden" name="__token__" value="%s"/>', $time) : $time;
    }

    /**
     * @function 动态口令检测
     * @param $request
     * @return bool
     */
    public static function token_check(&$request)
    {
        $token = $request['__token__'];
        unset($request['__token__']);
        return $token ? Cookie::get('__token__', null) == md5(Cookie::$salt . $token) : false;
    }

    /**
     * @function 删除口令
     */
    public static function delete_token()
    {
        Cookie::delete('__token__');
    }

}