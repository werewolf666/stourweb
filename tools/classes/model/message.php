<?php defined('SYSPATH') or die('No direct access allowed.');

require_once TOOLS_COMMON . 'sms/noticecommon.php';
class Model_Message extends ORM
{

    /**
     * @function 订单通知类消息
     * @param $order
     * @return bool
     */
    public static function add_order_msg($order)
    {
        if(empty($order['memberid']))
        {
            return false;
        }
        $msg_config = DB::select()->from('message_config')->where('type','=',$order['status'])->and_where('typeid','=',$order['typeid'])->and_where('isopen','=',1)->execute()->current();
        if(empty($msg_config) || empty($msg_config['content']))
        {
            return false;
        }

        $cfg_webname = Model_Sysconfig::get_configs(0,'cfg_webname',true);
        $content = $msg_config['content'];
        $order_price_num_summary = NoticeCommon::summary_price_number($order);

        $content = str_ireplace('{#WEBNAME#}', $cfg_webname, $content);
        $content = str_ireplace('{#PHONE#}', $order['linktel'], $content);
        $content = str_ireplace('{#MEMBERNAME#}', $order['linkman'], $content);
        $content = str_ireplace('{#PRODUCTNAME#}', $order["productname"], $content);
        $content = str_ireplace('{#PRICE#}', $order_price_num_summary['priceDescript'], $content);
        $content = str_ireplace('{#NUMBER#}', $order_price_num_summary['numberDescript'], $content);
        $content = str_ireplace('{#TOTALPRICE#}', $order_price_num_summary['totalPrice'], $content);
        $content = str_ireplace('{#ORDERSN#}', $order['ordersn'], $content);
        $content = str_ireplace('{#ETICKETNO#}', $order['eticketno'], $content);
        $content = str_ireplace('{#USEDATE#}', $order['usedate'], $content);
        $content = str_ireplace('{#DEPARTDATE#}', $order['departdate'], $content);

        $msg_model = ORM::factory('message');
        $msg_model->type = $order['status'];
        $msg_model->memberid = $order['memberid'];
        $msg_model->orderid = $order['id'];
        $msg_model->product_typeid = $order['typeid'];
        $msg_model->content = $content;
        $msg_model->productid = $order['productautoid'];
        $msg_model->addtime = time();
        $msg_model->save();
        return $msg_model->saved();
    }

    /**
     * @function 添加资讯信息
     * @param $type
     * @param $memberid
     * @param $productid
     * @return bool
     */
    public static function add_news_msg($type,$memberid,$newsid,$news_info=null)
    {
        $type = empty($type)?200:$type;
        if($type<200 || $type>299)
        {
            return false;
        }
        $info = empty($news_info)? DB::select()->from('news')->where('id','=',$newsid)->execute()->current():$news_info;

        $attrname = DB::select('attrname')->from('news_attr')->where('id','=',$info['category_two'])->execute()->get('attrname');
        $attrname = empty($attrname)?'':$attrname;

        if(empty($info) || empty($info['id']))
        {
            return false;
        }

        $msg_config = DB::select()->from('message_config')->where('type','=',$type)->and_where('isopen','=',1)->execute()->current();
        if(empty($msg_config) || empty($msg_config['content']))
        {
            return false;
        }
        $content = $msg_config['content'];

        $cfg_webname = Model_Sysconfig::get_configs(0,'cfg_webname',true);
        $addtime = !empty($info['addtime'])? date('Y-m-d H:i'):'';
        $content = str_ireplace('{#WEBNAME#}', $cfg_webname, $content);
        $content = str_ireplace('{#ADDTIME#}', $addtime,$content);
        $content = str_ireplace('{#PRODUCTNAME#}',$info['title'],$content);
        $content = str_ireplace('{#ATTR#}', $attrname,$content);

        if(empty($memberid))
        {
            $members = DB::select('mid')->from('member')->where('virtual','=',1)->execute()->as_array();
            foreach($members as $member)
            {
                $msg_model = ORM::factory('message');
                $msg_model->type = $type;
                $msg_model->memberid = $member['mid'];
                $msg_model->product_typeid = 115;
                $msg_model->content = $content;
                $msg_model->productid = $newsid;
                $msg_model->addtime = time();
                $msg_model->save();
            }
        }
        else
        {
            $msg_model = ORM::factory('message');
            $msg_model->type = $type;
            $msg_model->memberid = $memberid;
            $msg_model->product_typeid = 115;
            $msg_model->content = $content;
            $msg_model->productid = $newsid;
            $msg_model->addtime = time();
            $msg_model->save();
        }
        return true;
    }

    /**
     * @function 添加游记信息
     * @param $type
     * @param $noteid
     * @param $note_info
     * @param $commentid //仅对评论消息有效
     */
    public static function add_note_msg($type,$noteid,$note_info=null,$commentid)
    {
        if($type<100 || $type>199)
        {
            return false;
        }
        $info = empty($note_info)? DB::select()->from('notes')->where('id','=',$noteid)->execute()->current():$note_info;
        if(empty($info) || empty($info['id']) || empty($info['memberid']))
        {
            return false;
        }
        $msg_config = DB::select()->from('message_config')->where('type','=',$type)->and_where('isopen','=',1)->execute()->current();
        if(empty($msg_config) || empty($msg_config['content']))
        {
            return false;
        }

        $nickname = '匿名';
        if($type!=103)
        {
            $member_info = DB::select()->from('member')->where('mid', '=', $info['memberid'])->execute()->current();
            if (empty($member_info)) {
                return false;
            }
            $nickname = $member_info['nickname'];
            $nickname = empty($nickname) ? $member_info['mobile'] : $nickname;
            $nickname = empty($nickname) ? $member_info['email'] : $nickname;
        }
        else
        {
            $comment = DB::select()->from('comment')->where('id','=',$commentid)->execute()->current();
            if(!empty($comment['memberid']))
            {
                $member_info = DB::select()->from('member')->where('mid', '=',$comment['memberid'])->execute()->current();
                $nickname = $member_info['nickname'];
                $nickname = empty($nickname) ? $member_info['mobile'] : $nickname;
                $nickname = empty($nickname) ? $member_info['email'] : $nickname;
            }
            else
            {
                $nickname = $comment['vr_nickname'] ? $comment['vr_nickname'] : '匿名';
            }
        }





        $cfg_webname = Model_Sysconfig::get_configs(0,'cfg_webname',true);

        $content = $msg_config['content'];
        $content = str_ireplace('{#WEBNAME#}', $cfg_webname, $content);
        $content = str_ireplace('{#MEMBERNAME#}', $nickname,$content);
        $content = str_ireplace('{#PRODUCTNAME#}',$info['title'],$content);


        $msg_model = ORM::factory('message');
        $msg_model->type = $type;
        $msg_model->memberid = $info['memberid'];
        $msg_model->product_typeid = 101;
        $msg_model->content = $content;
        $msg_model->productid = $info['id'];
        $msg_model->addtime = time();
        $msg_model->save();
        return $msg_model->saved();
    }

    /**
     * @function是否有新的信息
     * @param $mid
     */
    public static function has_msg($mid)
    {
        $num = DB::query(Database::SELECT,"select count(*) as num from sline_message where status=0 and memberid='{$mid}'")->execute()->get('num');
        return empty($num)?0:$num;
    }

    /**
     * @function 数量信息
     * @param $orderinfo
     * @return array
     */
    public static function summary_price_number($orderinfo)
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
                //$totalprice = self::suit_range_price($suitid, $startdate, $leavedate);
                $result['numberDescript'] = $dingnum;
                $result['priceDescript'] = $orderinfo['price']*$dingnum;
                $result['totalPrice'] = $orderinfo['price']*$dingnum;
                $result['totalNumber'] = $dingnum;

            } else
            {

                $totalPrice = $orderinfo['price'] * $orderinfo['dingnum'] + $orderinfo['childnum'] * $orderinfo['childprice'] + $orderinfo['oldnum'] * $orderinfo['oldprice'];
                $result['totalPrice'] = $totalPrice;
                $totalNumber = $orderinfo['dingnum'] + $orderinfo['childnum'] + $orderinfo['oldnum'];
                $result['totalNumber'] = $totalNumber;

                if (!empty($orderinfo['childnum']) || !empty($orderinfo['oldnum']))
                {
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
                } else
                {
                    $result['priceDescript'] = $orderinfo['price'];
                    $result['numberDescript'] = $orderinfo['dingnum'];
                }

            }

        }
        return $result;
    }

}