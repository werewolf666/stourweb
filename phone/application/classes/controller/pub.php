<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Pub extends Stourweb_Controller
{
    /*
     * 公共请求控制器,此控制器不能删除.
     *
     * */

    public function before()
    {
        parent::before();

    }

    //请求CSS资源,合并输出
    public function action_css()
    {
        $this->request->headers('Content-Type', 'text/css');
        $this->request->headers('charset', 'utf-8');
        if (isset($_GET['file']))
        {
            $files = explode(",", $_GET['file']);
            $fc = '';
            foreach ($files as $val)
            {
                $fc .= file_get_contents(DOCROOT . $val);
            }
            $fc = self::replace_note($fc);
            $fc = str_replace("\/t", "", $fc);
            $fc = str_replace("\/n", "", $fc);
            $fc = str_replace("\/r\/n", "", $fc);
            echo $fc;
        }
    }

    //请求js资源,合并输出 
    public function action_js()
    {
        //输出JS
        header("Content-type:application/x-javascript; Charset: utf-8");
        if (isset($_GET['file']))
        {
            $files = explode(",", $_GET['file']);
            $str = '';
            foreach ($files as $val)
            {
                $str .= file_get_contents(DOCROOT . $val);
            }
            $str = self::replace_note($str);
            $str = str_replace("\/t", "", $str);
            $str = str_replace("\/n", "", $str);
            //$str = preg_replace('#\/\/[^\n]*#','',$str);//行注释
            echo $str;
        }
    }

    /*
     * 网站头部
     * */
    public function action_header()
    {
        //默认显示logo
        $showlogo = 1;
        $typeid = $this->params['typeid'];
        $definetitle = $this->params['definetitle'];
        $definetitle = urldecode($definetitle);
        foreach ($this->params as $k => $v)
        {
            $this->assign($k, $v);
        }
        $isshowpage = $this->params['isshowpage'] ? $this->params['isshowpage'] : 0;
        $isplpage = $this->params['isplpage'] ? $this->params['isplpage'] : 0;
        $isbookpage = $this->params['isbookpage'] ? $this->params['isbookpage'] : 0;
        $isorder = $this->params['isorder'] ? $this->params['isorder'] : 0;
        $islinkman = $this->params['islinkman'] ? $this->params['islinkman'] : 0;
        $ispaypage = $this->params['ispaypage'] ? $this->params['ispaypage'] : 0;
        $iscommontitle = $this->params['iscommontitle'] ? $this->params['iscommontitle'] : 0;
        $isparam = $this->params['isparam'] ? $this->params['isparam'] : 0;
        if (!empty($typeid))
        {
            $channelname = Model_Nav::get_channel_name_mobile($typeid);
        }

        if ($isshowpage == 1 || $isplpage == 1 || $isbookpage == 1)
        {
            $showlogo = 0;
        }
        if ($isplpage == 1)
        {
            $channelname = '评论';
        }
        if ($isbookpage == 1)
        {
            $channelname = '预订产品';
        }
        if ($isbookpage == 1)
        {
            $channelname = '预订产品';
        }
        if ($isorder == 1)
        {
            $channelname = '我的订单';
            $showlogo = 0;
        }
        if ($islinkman == 1)
        {
            $channelname = '常用联系人';
        }
        if ($ispaypage == 1)
        {
            $channelname = '确认订单';
            $showlogo = 0;
        }
        if ($iscommontitle == 1)
        {
            $showlogo = 0;
            $channelname = '<stourweb_title/>';
        }
        if (!empty($isparam))
        {
            $showlogo = 0;
            $channelname = $isparam;
        }
        $member = Common::session('member');
        if (empty($member))
        {
            $member = array(
                'litpic' => Common::member_nopic()
            );
        }
        else
        {
            //订单数量
            $this->assign('orderNum', count(Model_Member_Order::unpay($member['mid'])));
        }
        $backUrl = 'href="javascript:;" onclick="javascript:history.go(-1);"';
        if (stripos($_SERVER['HTTP_REFERER'], Common::cookie_domain()) === false)
        {
            $conf = require dirname(DOCROOT) . '/data/mobile.php';
            $url = Common::get_main_host() . '/phone/';
            if (stripos($conf['domain']['mobile'], $url) === false)
            {
                $url = $conf['domain']['mobile'];
            }
            $backUrl = 'href="' . $url . '"';
        }
        $channelname = empty($definetitle) ? $channelname : $definetitle;
        $this->assign('showlogo', $showlogo);
        $this->assign('member', $member);
        $this->assign('isshowpage', $isshowpage);
        $this->assign('backurl', $backUrl);
        $this->assign('channelname', $channelname);
        $this->display('pub/header', 'header');
    }

    /*
     * 新版网站头部
     * */
    public function action_header_new()
    {
        //默认显示logo
        $showlogo = 1;
        $typeid = $this->params['typeid'];
        $definetitle = $this->params['definetitle'];
        $definetitle = urldecode($definetitle);
        foreach ($this->params as $k => $v)
        {
            $this->assign($k, $v);
        }
        $isshowpage = $this->params['isshowpage'] ? $this->params['isshowpage'] : 0;
        $isplpage = $this->params['isplpage'] ? $this->params['isplpage'] : 0;
        $isbookpage = $this->params['isbookpage'] ? $this->params['isbookpage'] : 0;
        $isorder = $this->params['isorder'] ? $this->params['isorder'] : 0;
        $islinkman = $this->params['islinkman'] ? $this->params['islinkman'] : 0;
        $ispaypage = $this->params['ispaypage'] ? $this->params['ispaypage'] : 0;
        $iscommontitle = $this->params['iscommontitle'] ? $this->params['iscommontitle'] : 0;
        $isordershow = $this->params['isordershow'] ? $this->params['isordershow'] : 0;
        $isparam = $this->params['isparam'] ? $this->params['isparam'] : 0;
        if (!empty($typeid))
        {
            $channelname = Model_Nav::get_channel_name_mobile($typeid);
        }

        if ($isshowpage == 1 || $isplpage == 1 || $isbookpage == 1)
        {
            $showlogo = 0;
        }
        if ($isplpage == 1)
        {
            $channelname = '评论';
        }
        if ($isbookpage == 1)
        {
            $channelname = '预订产品';
        }
        if ($isbookpage == 1)
        {
            $channelname = '预订产品';
        }
        if ($isorder == 1)
        {
            $channelname = '我的订单';
            $showlogo = 0;
        }
        if ($islinkman == 1)
        {
            $channelname = '常用联系人';
        }
        if ($ispaypage == 1)
        {
            $channelname = '确认订单';
            $showlogo = 0;
        }
        if ($iscommontitle == 1)
        {
            $showlogo = 0;
            $channelname = '<stourweb_title/>';
        }
        if ($isordershow == 1)
        {
            $channelname = '订单详情';
            $showlogo = 0;
        }
        if (!empty($isparam))
        {
            $showlogo = 0;
            $channelname = $isparam;
        }
        $member = Common::session('member');
        if (empty($member))
        {
            $member = array(
                'litpic' => Common::member_nopic()
            );
        }
        else
        {
            //订单数量
            $this->assign('orderNum', count(Model_Member_Order::unpay($member['mid'])));
        }
        $backUrl = 'href="javascript:;" onclick="javascript:history.go(-1);"';
        if (stripos($_SERVER['HTTP_REFERER'], Common::cookie_domain()) === false)
        {
            $conf = require dirname(DOCROOT) . '/data/mobile.php';
            $url = Common::get_main_host() . '/phone/';
            if (stripos($conf['domain']['mobile'], $url) === false)
            {
                $url = $conf['domain']['mobile'];
            }
            $backUrl = 'href="' . $url . '"';
        }
        $channelname = empty($definetitle) ? $channelname : $definetitle;
        $this->assign('showlogo', $showlogo);
        $this->assign('member', $member);
        $this->assign('isshowpage', $isshowpage);
        $this->assign('backurl', $backUrl);
        $this->assign('channelname', $channelname);
        $this->display('pub/header_new', 'header');
    }

    /*
     * 网站底部
     * */
    public function action_footer()
    {
        $sql = "select weburl from sline_weblist where webid=0";
        $ar = DB::query(1, $sql)->execute()->as_array();
        $this->assign('weburl', $ar[0]['weburl']);
        $this->display('pub/footer', 'footer');
    }

    /**
     * ajax 发送验证码
     */
    public function action_ajax_send_message()
    {
        $validataion = Validation::factory($this->request->post());
        $validataion->rule('phone', 'not_empty');
        $validataion->rule('phone', 'phone');
        if (!$validataion->check())
        {
            exit(__('error_user_phone'));
        }
        if (!Captcha::valid(Common::remove_xss(Arr::get($_POST, 'code'))))
        {
            exit(__('error_code'));
        }
        //检测用户是否存在
        $phone = Arr::get($_POST, 'phone');
        $code = rand(1000, 9999);
        $model = ORM::factory('sms_msg');
        $content = $model->message_template('reg_msgcode');
        $content = str_replace(array('{#CODE#}', '{#WEBNAME#}', '{#PHONE#}'), array($code, $GLOBALS['cfg_webname'], $GLOBALS['cfg_phone']), $content);
        $status = $model->send_message($phone, $code, $content);
        echo intval($status);
    }

    /**
     * 验证手机短信验证码
     */
    public function action_ajax_check_msg()
    {
        //验证码检测
        if (isset($_POST['msg']))
        {
            $msg = Arr::get($_POST, 'msg');

            //短信验证是否开启
            $sms_msg = DB::select()->from('sms_msg')->where("msgtype='reg_findpwd'")->execute()->current();
            if ($sms_msg['isopen'])
            {
                $result = (bool)(Common::session('msg_code') == $msg);
            }
            else
            {
                $result = (bool)(sha1(utf8::strtoupper($msg)) === Session::instance()->get('captcha_response'));
            }
            if ($result)
            {
                print_r('true');
                exit;
            }
            else
            {
                print_r('false');
            }
        }
        else
        {
            print_r('false');
        }
    }

    /*
     * 搜索选择页面
     *
     * */
    public function action_select()
    {
        $typeid = Common::remove_xss($this->params['typeid']);
        $destid = Common::remove_xss($this->params['destid']);
        //判断目的地是否设置,未设置则使用顶级目的地
        $destid = $destid ? $destid : 0;
        $model = ORM::factory('model', $typeid);
        if (!$model->loaded())
        {
            return;
        }
        $pinyin = $model->pinyin;

        //排除通用模块和签证模块(读取属性)
        if (!empty($pinyin) && $typeid != 8 && $model->maintable != 'model_archive')
        {
            $table = "sline_{$pinyin}_attr";
            $sql = "SELECT * FROM `{$table}` ";
            $sql .= "WHERE isopen=1 AND pid=0 ";
            $sql .= "ORDER BY displayorder ASC";
            $arr = DB::query(1, $sql)->execute()->as_array();

            $attrlist = $arr;
        }
        else if ($model->maintable == 'model_archive')
        {
            $table = 'sline_model_attr';
            $sql = "SELECT * FROM `{$table}` ";
            $sql .= "WHERE isopen=1 AND pid=0 and typeid=$typeid ";
            $sql .= "ORDER BY displayorder ASC";
            $arr = DB::query(1, $sql)->execute()->as_array();

            $attrlist = $arr;
        }

        $dest_list = ORM::factory('destinations')->where("pid='$destid' AND isopen=1")->get_all();

        //当前目的地
        $currentDest = DB::select()->from('destinations')->where('id', '=', $destid)->execute()->current();
        if ($currentDest)
        {
            $parentDestArr = DB::select()->from('destinations')->where('id', '=', $currentDest['pid'])->and_where('isopen', '=', 1)->and_where(DB::expr("FIND_IN_SET($typeid,opentypeids)"), '>', 0)->execute()->current();
            $hasChild = DB::select()->from('destinations')->where('pid', '=', $currentDest['id'])->and_where('isopen', '=', 1)->and_where(DB::expr("FIND_IN_SET($typeid,opentypeids)"), '>', 0)->execute()->current();
            $parentDest = $hasChild ? $currentDest['pinyin'] : $parentDestArr['pinyin'];
        }
        else
        {
            $parentDest = 'all';
        }
        //判断出发地是否开启
        $isopen = DB::select('value')->from('sysconfig')->where('varname', '=', 'cfg_startcity_open')->select()->execute()->current();
        if ($isopen['value'])
        {
            $startcityid = Common::remove_xss($this->params['startcityid']);
            if (!$startcityid)
            {
                $cityid = 0;
                $cityname = '出发地';
            }
            else
            {
                $startcity = DB::select('id', 'cityname')->from('startplace')->where('id', '=', $startcityid)->and_where('isopen', '=', '1')->execute()->current();
                $cityid = $startcity['id'];
                $cityname = $startcity['cityname'];
            }
            $this->assign('cityid', $cityid);
            $this->assign('cityname', $cityname);
        }
        $this->assign('cfg_startcity_open', $isopen['value']);
        $this->assign('hotdest', $hot_dest);
        $this->assign('attrlist', $attrlist);
        $this->assign('destlist', $dest_list);
        $this->assign('typeid', $typeid);
        $this->assign('destpy', $parentDest);
        $this->assign('curdestpy', $currentDest['pinyin']);
        $this->display('pub/select');

    }


    /*由子级目的地获取上级目的地*/
    public function action_ajax_get_destall()
    {
        $destpy = strtolower(Arr::get($_GET, 'destpy'));
        $curdest = strtolower(Arr::get($_GET, 'curdest'));
        $destpy = Common::remove_xss($destpy);
        $typeid = intval($_GET['typeid']);
        $dest = Model_Destinations::get_all_dest($typeid);
        $pid = 0;


        //目的地导航
        $baseNav = array(
            array(
                'kindname' => '目的地',
                'pinyin' => 'all'
            )
        );

        if ($destpy != 'all')
        {
            foreach ($dest as $v)
            {
                if ($v['pinyin'] == $destpy)
                {
                    $pid = $v['id'];
                    $nav = Model_Destinations::get_dest_nav($v['id'], array(), $typeid);
                    array_unshift($nav, $baseNav[0]);
                    $baseNav = $nav;
                }
            }
        }
        //子目的地
        $destList = array();
        foreach ($dest as $v)
        {
            if ($v['pid'] == $pid)
            {
                $list = $v;
                $list['haschild'] = false;
                foreach ($dest as $sub)
                {
                    if ($sub['pid'] == $v['id'])
                    {
                        $list['haschild'] = true;
                        break;
                    }
                }
                $destList[] = $list;
            }
        }
        echo json_encode(array('nav' => $baseNav, 'list' => $destList, 'curDest' => $curdest));
    }

    /*
     * 产品评论页面
     *
     * */
    public function action_comment()
    {
        $articleid = Common::remove_xss($this->params['id']);
        $typeid = Common::remove_xss($this->params['typeid']);
        $model_info = ORM::factory('model', $typeid)->as_array();
        $pinyin = $model_info['pinyin'];
        if (!empty($pinyin))
        {
            //线路显示评论id=$articleid 换成aid=$articleid
            $main_table = $model_info['maintable'] == 'model_archive' ? 'model_archive' : $pinyin;
            $row = ORM::factory($main_table)->where("id=$articleid")->find()->as_array();
            $row['number'] = Model_Comment::get_comment_count($typeid, $articleid, $row['satisfyscore']);
        }
        $this->assign('info', $row);
        $this->assign('typeid', $typeid);
        $this->display('pub/comment_new');
    }

    /**
     * 文章评论发表页面
     */
    public function action_article_write_comment()
    {
        $articleid = intval(Arr::get($_GET, 'articleid'));
        $typeid = intval(Arr::get($_GET, 'typeid'));
        $replyid = intval(Arr::get($_GET, 'replyid'));
        $replyname = Common::remove_xss(Arr::get($_GET, 'replyname'));
        if (empty($typeid) || empty($articleid))
        {
            exit('no typeid or articleid');
        }
        $model_info = ORM::factory('model', $typeid)->as_array();
        $pinyin = $model_info['pinyin'];
        //判断来源
        $referrer = $this->request->referrer();

        $this->assign('referrer', $referrer);
        if (!empty($pinyin))
        {

            $main_table = $model_info['maintable'] == 'model_archive' ? 'model_archive' : $pinyin;
            $row = ORM::factory($main_table)->where("id=$articleid")->find()->as_array();
            $this->assign('info', $row);
        }
        //表单校验码
        $token = md5(time());
        Common::session('token', $token);
        $this->assign('token', $token);

        $this->assign('typeid', $typeid);
        $this->assign('articleid', $articleid);
        $this->assign('replyid', $replyid);
        $this->assign('replyname', $replyname);
        $this->assign('member', Product::get_login_user_info());
        $this->display('pub/article_write_comment');

    }

    /**
     * 文章评论保存
     */
    public function action_ajax_article_comment_save()
    {
        $token = Arr::get($_POST, 'token');
        $articleid = intval(Arr::get($_POST, 'articleid'));
        $typeid = intval(Arr::get($_POST, 'typeid'));
        $replyid = intval(Arr::get($_POST, 'replyid'));
        $checkcode = Common::remove_xss(Arr::get($_POST, 'checkcode'));
        $is_anonymous = intval(Arr::get($_POST, 'is_anonymous'));
        $content = Common::remove_xss(Arr::get($_POST, 'content'));
        if (Common::session('token') != $token)
        {
            echo json_encode(array('status' => 0, 'msg' => '安全校验码出错'));
            exit;
        }

        //验证码验证
        if (!Captcha::valid($checkcode) || empty($checkcode))
        {
            echo json_encode(array('status' => 0, 'msg' => '验证码错误'));
            exit;
        }
        else
        {
            //清空验证码
            Common::session('captcha_response', null);
        }

        $arr = array();
        $arr['typeid'] = $typeid;
        $arr['articleid'] = $articleid;
        $arr['content'] = $content;
        $arr['addtime'] = time();
        //是否匿名
        if (!$is_anonymous)
        {
            $member = Product::get_login_user_info();
            $arr['memberid'] = $member['mid'];
        }
        else
        {
            $arr['memberid'] = 0;
        }

        //是否开启审核
        if ($GLOBALS['cfg_article_pinlun_audit_open'] == 0 && $typeid == 4)
        {
            $arr['isshow'] = 1;
        }
        $arr['dockid'] = $replyid ? $replyid : 0;
        $flag = DB::insert('comment', array_keys($arr))->values(array_values($arr))->execute();
        echo json_encode(array('status' => $flag));
        exit;

    }

    public function action_article_comment_list()
    {
        $typeid = intval(Arr::get($_GET, 'typeid'));
        $articleid = intval(Arr::get($_GET, 'articleid'));
        $this->assign('typeid', $typeid);
        $this->assign('articleid', $articleid);
        $this->display('pub/article_comment_list');
    }

    /*
     * 付款
     * */

    public function action_pay()
    {

        $this->display('pub/pay');
    }

    /**
     * 通用头底部html
     */
    public function action_commonhd()
    {
        $this->display('pub/commonhd');
    }

    /**
     * 积分抵现
     */
    public function action_integral()
    {
        $jifen = array();
        $bool = true;
        $member = Common::session('member');
        if ($member)
        {
            $userInfo = Model_Member::get_member_byid($member['mid']);
            $jifen['isopen'] = 1;
            $jifen['exchange'] = $GLOBALS['cfg_exchange_jifen'];
            $jifen['userjifen'] = $userInfo['jifen'];
            if (empty($jifen['exchange']))
            {
                $bool = false;
            }
        }
        $this->assign('jifen', $jifen);
        /*******************************新增优惠券********************************/
        if (St_Functions::is_normal_app_install('coupon'))
        {
            $typeid = intval($this->params['typeid']);
            $proid = intval($this->params['proid']);
            $couponlist = Model_Coupon::get_pro_coupon($typeid, $proid);
            if ($couponlist)
            {
                $bool = true;
                $this->assign('couponlist', $couponlist);
            }
        }

        if ($bool)
        {
            $this->display('pub/integral');
        }


    }

    public function action_code()
    {
        $this->display('pub/code');
    }


    /*
     * 进行第三方支付页面
     * */
    public function action_dopay()
    {
        $orderid = Arr::get($_POST, 'orderid');
        $paytype = Arr::get($_POST, 'paytype');
        $usejifen = Arr::get($_POST, 'usejifen');

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

        if ($paytype == 0)
        {
            exit('error request');
        }
        echo Product::pay_online($info['ordersn'], $info['productname'], $totalprice, $paytype);

    }


    public function tongyong_suit_price($year, $month, $productid, $suitid, $startdate)
    {
        $start = !empty($startdate) ? strtotime($startdate) : strtotime("$year-$month-1");
        $end = strtotime("$year-$month-31");
        $arr = DB::select()->from('model_suit_price')
            ->where("suitid=$suitid")
            ->and_where('day', '>=', $start)
            ->and_where('day', '<=', $end)
            ->and_where('productid', '=', $productid)
            ->and_where('number', '!=', 0)
            ->execute()
            ->as_array();
        $price = array();
        foreach ($arr as $row)
        {
            if ($row)
            {
                $day = $row['day'];
                $price[$day]['date'] = Common::mydate('Y-m-d', $row['day']);
                $price[$day]['price'] = Currency_Tool::price($row['price']);
                $price[$day]['suitid'] = $suitid;
                $price[$day]['number'] = $row['number'];//库存
                $price[$day]['description'] = $row['description'];//描述

            }
        }
        return $price;
    }

    /**
     * 生成格式化的数据
     * 用于日历中进行呈现
     * @param $arr
     */
    public function get_suit_price($year, $month, $suitid, $typeid, $startdate)
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
                $price[$day]['old_profit'] = Currency_Tool::price($price[$day]['old_profit']);

                $price[$day]['suitid'] = $suitid;
                $price[$day]['number'] = $row['number'];//库存
                $price[$day]['description'] = $row['description'];//描述
            }

        }


        return $price;
    }

    /**
     * 通用产品日历报价
     * @param string $year
     * @param string $month
     * @param $price_arr
     * @return string
     */
    public function calender($year = '', $month = '', $price_arr = NUL)
    {

        $first_day_time = strtotime($year . '-' . $month . '-' . '01');
        $last_day_time = strtotime(date('Y-m-d', $first_day_time) . " +1 month -1 day");
        $start_week = date('w', $first_day_time);
        $start_week = $start_week == 0 ? 7 : $start_week;
        $currency_symbol = Currency_Tool::symbol();
        $html = '<div class="calendar-wrap"><h3 class="calendar-date">';
        $html .= '<strong class="calendar-cur" time-data="">' . date('Y年m月', $first_day_time) . '</strong></h3>';
        $html .= '<table width="100%">';
        $html .= '<tr class="calendar-hd"><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th><th>日</th></tr>';
        for ($j = 1; $j <= 8; $j++)
        {
            $html .= '<tr class="calendar-bd">';
            for ($i = 1; $i <= 7; $i++)
            {
                $number = ($j - 1) * 7 + $i;
                $cur_day_time = $first_day_time + ($number - $start_week) * 24 * 3600;
                $cur_date = date('Y-m-d', $cur_day_time);
                $cur_day = date('j', $cur_day_time);
                $price_info = $price_arr[$cur_day_time];
                if ($cur_day_time < $first_day_time || $cur_day_time > $last_day_time)
                {
                    $html .= '<td><div class="item"></div></td>';
                }
                else if (empty($price_info))
                {
                    $html .= '<td><div class="item opt"><span class="date">' . $cur_day . '</span></div></td>';
                }
                else if (!empty($price_info))
                {
                    $html .= '<td adultprice="' . $price_info['adult_price'] . '" childprice="' . $price_info['child_price'] . '" oldprice="' . $price_info['old_price'] . '" number="' . $price_info['number'] . '" roombalance="' . $price_info['roombalance'] . '" date="' . $cur_date . '"  onclick="choose_day(\'' . $cur_date . '\')">';
                    $html .= '<div class="item opt"><span class="date">' . $cur_day . '</span>';
                    $html .= '<span class="price">' . $currency_symbol . $price_info['price'] . '<br></span>';
                    $stock = $price_info['number'] == '-1' ? '库存充足' : $price_info['number'];
                    $html .= '<span class="stock">' . $stock . '</span>';
                    $html .= '</div></td>';
                }
            }
            $html .= '</tr>';
            if ($cur_day_time && $cur_day_time > $last_day_time)
            {
                break;
            }
        }
        $html .= '</table></div>';
        return $html;
    }

    /**
     * @param $str
     * @return mixed
     * @desc 替换注释
     */
    public
    function replace_note($str)
    {


        $pos0 = strpos($str, '/*');
        while ($pos0 !== false)
        {
            $pos1 = strpos($str, '*/');
            if ($pos1 === false)
            {
                $pos0 += 2;
            }
            else
            {
                $rp = substr($str, $pos0, $pos1 - $pos0 + 2);
                $str = str_replace($rp, '', $str);
                $pos0 = strpos($str, '/*');
            }
        }

        return $str;

    }


    /*ajax请求*/
    /**
     * 获取目的地
     * @return string
     */

    public
    function action_ajax_get_dest()
    {
        if (!$this->request->is_ajax())
        {
            return '';
        }
        $flag = Arr::get($_GET, 'flag');
        $destid = Arr::get($_GET, 'destid');
        $typeid = Arr::get($_GET, 'typeid');
        if ($flag == 'desthot')
        {
            $dest = Model_Destinations::get_hot_dest($typeid, 0, 20);
            $flag = '';
            $liclass = 'hotdest';
        }
        else
        {
            $sql = "SELECT id,kindname,pinyin FROM `sline_destinations` ";
            $sql .= "WHERE isopen=1 AND pid='$destid' AND FIND_IN_SET($typeid,opentypeids) ";
            $dest = DB::query(1, $sql)->execute()->as_array();
            $ajaxdiv = 'list-spot';
            $flag = 'dest';
            $liclass = 'hasnext';
        }
        $out = array(
            'ajaxdiv' => $ajaxdiv,
            'list' => $dest,
            'flag' => $flag,
            'liclass' => $liclass
        );
        echo json_encode($out);

    }

    /**
     * 异步获取属性组下级
     *
     */
    public
    function action_ajax_get_attr()
    {

        $typeid = Arr::get($_GET, 'typeid');
        $pid = Arr::get($_GET, 'attrid');
        $pinyin = ORM::factory('model', $typeid)->get('pinyin');
        $flag = '';
        $liclass = '';
        $arr = array();


        //排除通用模块和签证模块(读取属性)
        if (!empty($pinyin) && $typeid != 8 && $typeid < 17)
        {
            $table = "sline_{$pinyin}_attr";
            $sql = "SELECT *,attrname AS kindname FROM `{$table}` ";
            $sql .= "WHERE isopen=1 AND pid=$pid ";
            $sql .= "ORDER BY displayorder ASC";
            $arr = DB::query(1, $sql)->execute()->as_array();

        }
        $out = array(
            'ajaxdiv' => '',
            'list' => $arr,
            'flag' => $flag,
            'liclass' => $liclass,
            'type' => 'attrid'

        );
        echo json_encode($out);
    }

    public
    function action_ajax_calendar()
    {
        $typeid = $_REQUEST['typeid'];
        $suitid = $_REQUEST['suitid'];
        $productid = $_REQUEST['productid'];
        $startdate = Arr::get($_REQUEST, 'startdate');
        $year = date("Y"); //当前月
        $month = date("m");//当前年
        $out = '';
        for ($i = 1; $i <= 24; $i++)
        {
            if ($month == 13)
            {
                $year = $year + 1;
                $month = 1;
            }
            $priceArr = $typeid > 5 ? self::tongyong_suit_price($year, $month, $productid, $suitid, $startdate) : self::get_suit_price($year, $month, $suitid, $typeid, $startdate);
            $out .= empty($priceArr) ? '' : self::calender($year, $month, $priceArr);
            $month++;
        }
        echo $out;
    }

    /*
    * 异步获取评论
    *
    * */
    public
    function action_ajax_comment()
    {

        if (!$this->request->is_ajax())
        {
            return '';
        }
        $pagesize = 5;
        $typeid = intval(Arr::get($_GET, 'typeid'));
        $articleid = intval(Arr::get($_GET, 'articleid'));
        $pageno = intval(Arr::get($_GET, 'page'));
        $pageno = $pageno <= 0 ? 1 : $pageno;
        //评论类型:pic all well mid bad
        $flag = Common::remove_xss(Arr::get($_GET, 'flag'));
        $out = Model_Comment::search_result($typeid, $articleid, $flag, $pageno, $pagesize);


        $out['page'] = count($out['list']) < $pagesize ? -1 : $pageno + 1;
        $out['base_img_url'] = $GLOBALS['cfg_m_img_url'] ? $GLOBALS['cfg_m_img_url'] : St_Functions::get_http_prefix() . $GLOBALS['main_host'];
        echo json_encode($out);
        /* $articleid = Arr::get($_GET, 'articleid');
         $typeid = Arr::get($_GET, 'typeid');
         $page = Arr::get($_GET, 'page');

         $model_info = ORM::factory('model', $typeid)->as_array();
         $pinyin = $model_info['pinyin'];
         $main_table = $model_info['maintable']=='model_archive'?'model_archive':$pinyin;

         $row = ORM::factory($main_table)->where("id=$articleid")->find()->as_array();


         $row['commentnum'] = Model_Comment::get_comment_num($articleid, $typeid);
         $row['score'] = Model_Comment::get_score($articleid, $typeid, $row['satisfyscore'], $row['commentnum']);
         $page = $page ? $page : 1;
         $pagesize = 5;
         $offset = ($page - 1) * $pagesize;
         $sql = "SELECT * FROM `sline_comment` WHERE articleid='$articleid' AND typeid='$typeid' AND isshow=1 LIMIT {$offset},{$pagesize}";
         $pl = DB::query(1, $sql)->execute()->as_array();
         foreach ($pl as $key => $v)
         {
             $score = $pl[$key]['level'] * 20;
             if(!empty($v['memberid']))
             {
                 $memberinfo = Model_Member::get_member_byid($v['memberid']);
                 $pl[$key]['litpic'] = $memberinfo['litpic'] ? $memberinfo['litpic'] : Common::member_nopic();
                 $pl[$key]['nickname'] = $memberinfo['nickname'];
             }
             else
             {
                 $pl[$key]['litpic'] = $v['vr_headpic']?$v['vr_headpic']:Common::member_nopic();
                 $pl[$key]['nickname'] = $v['vr_nickname'];
             }
             $pl[$key]['score'] = $score . '%';
             $pl[$key]['content'] = $v['content'];
         }
         echo json_encode(array('list' => $pl));*/
    }

    public function action_paystatus()
    {
        $info = array('status' => false, 'url' => $this->cmsurl);
        if (!isset($_GET['sign']))
        {
            die('No direct script access.');
        }
        $sign = $_GET['sign'];
        switch ($sign)
        {
            //支付失败
            case '00':
                $info = array('sign' => 0, 'title' => '支付失败', 'msg' => '对不起，支付失败！');
                break;
            case '01':
                $info = array('sign' => 1, 'title' => '支宝付支付异常', 'msg' => '支付宝支付异常中断！');
                break;
            //支付成功
            case '11':
                $info = array('sign' => 11, 'title' => '支付成功', 'msg' => '恭喜，购买成功!');
                break;
            case '12':
                $info = array('sign' => 12, 'title' => '线下支付', 'msg' => '您的订单已提交成功，我们会尽快为您确认！');
                break;
            case '13':
                $info = array('sign' => 13, 'title' => '等待确认', 'msg' => '你的订单已提交成功，请等待确认！');
                break;
        }
        $this->assign('info', $info);
        $this->display('pub/paystatus');
    }

    /**
     * 订单状态
     */
    public function action_order_status()
    {
        $this->display('pub/order_status');
    }

    /**
     * 404
     */
    public
    function action_404()
    {
        $this->response->status('404');
        $this->display('pub/404');
    }

    /**
     * 第三方登陆跳转
     */
    public
    function action_thirdlogin()
    {
        $type = Arr::get($_GET, 'type');
        $refer = Arr::get($_GET, 'refer');
        if (empty($type) || empty($refer))
        {
            $this->request->redirect('pub/404');
        }
        Cookie::set('_version', 'mobile_5.0');
        header("location:" . Common::get_main_host() . "/plugins/login_{$type}/index/index/?refer={$refer}");
        exit;
    }

    /**
     * 附件下载
     */
    public
    function action_download()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad'))
        {
            $client_version = 'IOS';
        }


        if ($client_version == 'IOS')
        {

            $filename = $_GET['file'];
            $fileinfo = pathinfo($filename);

            if ($fileinfo['extension'] == 'pdf')
            {
                header('Content-type: application/pdf');
            }
            else
            {
                header('Content-type: application/msword');
            }
            header('Content-Disposition: attachment; filename=' . $_GET['name']);
            header('Content-Length: ' . filesize(BASEPATH . $filename));

            $file_path = BASEPATH . $filename;
            $fp = fopen($file_path, "r");
            $buffer = 1024;
            $file_con = 0;
            $file_count = 0;
            $file_size = filesize($file_path);
            while (!feof($fp) && $file_count < $file_size)
            {
                $file_con = fread($fp, $buffer);
                $file_count += $buffer;
                echo $file_con;
            }
            fclose($fp);
            exit();

        }
        else
        {
            $file = $_GET['file'];
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-Type:application/force-download");
            header("Content-Disposition:attachment;filename={$_GET['name']}");
            header("Accept-Length:" . filesize($file));
            readfile(BASEPATH . $file);

        }


    }


}