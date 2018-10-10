<?php

/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-28 下午7:35
 * Desc:评论标签
 */
class Taglib_Comment
{

    public static function query($params)
    {
        $default = array(
            'flag' => 'all',
            'offset' => 0,
            'row' => 5,
            'typeid' => 0,
            'articleid' => 0
        );
        $params = array_merge($default, $params);
        extract($params);
        //获取全部评论
        if ($flag == 'all' && empty($typeid))
        {
            $where = 'WHERE typeid!=4 and typeid !=6';
        }
        else if ($flag == 'line' || $typeid == 1) //线路评论
        {
            $where = 'WHERE typeid = 1';

        }
        else if ($flag == 'hotel' || $typeid == 2)
        {
            $where = 'WHERE typeid = 2';

        }
        else if ($flag == 'car' || $typeid == 3)
        {
            $where = 'WHERE typeid = 3';

        }
        else if ($flag == 'raider' || $typeid == 4)
        {
            $where = 'WHERE typeid = 4';

        }

        else if ($flag == 'spot' || $typeid == 5)
        {
            $where = 'WHERE typeid = 5';

        }
        else if ($flag == 'photo' || $typeid == 6)
        {
            $where = 'WHERE typeid = 6';

        }
        else if ($flag == 'visa' || $typeid == 8)
        {
            $where = 'WHERE typeid = 8';

        }
        else if ($flag == 'tuan' || $typeid == 13)
        {
            $where = 'WHERE typeid = 13';

        }
        else if ($flag == 'tongyong' || $typeid > 13)
        {
            $where = "WHERE typeid = $typeid";
        }

        //如果指定产品id
        if (!empty($articleid))
            $where .= ' AND articleid=' . $articleid;

        $where .= " AND isshow=1 AND level BETWEEN 0 AND 5 ";

        if ($flag == 'raider')
        {
            $order_by = 'addtime ASC';
        }
        else
        {
            $order_by = 'addtime DESC';
        }

        $sql = "SELECT * FROM `sline_comment` {$where} ORDER BY {$order_by}  LIMIT $offset,$row";


        $arr = DB::query(1, $sql)->execute()->as_array();
        foreach ($arr as &$r)
        {
            if(!empty($r['memberid']))
            {
                $awardinfo = self::get_award_info($r['orderid']);
                $memberinfo = self::get_member_info($r['memberid']);
                $r['jifentprice'] = $awardinfo['jifentprice'];
                $r['jifencomment'] = $awardinfo['jifencomment'];
                $r['jifenbook'] = $awardinfo['jifenbook'];
                $r['nickname'] = empty($memberinfo['nickname']) ? '匿名' : Common::cutstr_html($memberinfo['nickname'], 9); //昵称
                $r['litpic'] = !empty($memberinfo['litpic']) ? $memberinfo['litpic'] : Common::member_nopic();
            }
            else
            {
                $r['jifencomment'] = $r['vr_jifencomment'];
                $r['nickname'] = $r['vr_nickname'];
                $r['litpic'] = $r['vr_headpic'];
            }

            $r['nickname'] = empty($r['nickname'])?'匿名':$r['nickname'];
            $r['litpic'] = empty($r['litpic'])?Common::member_nopic():$r['litpic'];



            $r['pltime'] = Product::format_addtime($r['addtime']); //评论时间
            $r['percent'] = 20 * $r['level'] . '%';
            //判断是否有回复主题
            if (!empty($r['dockid']))
            {
                $reply_userid = DB::select('memberid')->from('comment')->where('id', '=', $r['dockid'])->execute()->get('memberid');
                $reply_userinfo = self::get_member_info($reply_userid);
                $r['replyname'] = $reply_userinfo['nickname'];
            }
            //关联产品相关信息
            $r['productname'] = $r['typeid'] != '4' && $r['typeid'] != '6' ? self::get_order_name($r['articleid'], $r['typeid'], '', $r['id']) : '';
            if ($r['productname'] == '') continue;
            $product_info = self::get_product_info($r['articleid'], $r['typeid']);
            $r['product_litpic'] = Common::img($product_info[0]['litpic']);
            if (!empty($r['orderid']))
            {
                $r['product_price'] = DB::select('price')->from('member_order')->where('id', '=', $r['orderid'])->execute()->get('price');
            }
            //图片列表
            if (!empty($r["piclist"]))
            {
                $r['piclist'] = explode(',', $r['piclist']);
            }


        }
        return $arr;

    }

    /**
     * 获取评论的统计信息
     * @param $params
     * @return array
     */
    public static function get_count($params)
    {
        $default = array(
            'typeid' => 0,
            'articleid' => 0
        );
        $params = array_merge($default, $params);
        extract($params);
        //获取全部评论
        //$model = ORM::factory("model")->where("id='{$typeid}' OR pinyin='{$flag}'")->find()->as_array();
        $model = DB::select()->from('model')
            ->or_where_open()
            ->or_where('id', '=', $typeid)
            ->or_where('pinyin', '=', $flag)
            ->or_where_close()
            ->execute()
            ->current();

        if (empty($model))
        {
            return;
        }
        $table = $model['maintable'];
        $sql_product = "SELECT * FROM sline_{$table} WHERE id='{$articleid}'";
        $product = self::execute_sql($sql_product);
        $satisfyscore = empty($product[0]['satisfyscore']) ? 0 : $product[0]['satisfyscore'];
        $count = self::_get_comment_count($typeid, $articleid, $satisfyscore);
        return $count;
    }

    /*
     * 获取奖励信息
     * */
    private static function get_award_info($orderid)
    {
        $sql = "SELECT jifenbook,jifentprice,jifencomment,price FROM `sline_member_order` WHERE id='$orderid'";
        $row = self::execute_sql($sql);
        return $row ? $row[0] : array();
    }

    /*
     * 会员信息
     * */
    private static function get_member_info($memberid)
    {
        $sql = "SELECT * FROM `sline_member` WHERE mid='$memberid'";
        $row = self::execute_sql($sql);
        return $row ? $row[0] : array();
    }

    /*
     * 产品名称
     * */
    private static function get_order_name($id, $typeid, $commentid)
    {

        $model = ORM::factory('model',$typeid);
        $table = $model->maintable;
        $pinyin = !empty($model->correct) ? $model->correct : $model->pinyin;
        if(empty($table))
        {
            return null;
        }


        $info = DB::select()->from($table)->where('id','=',$id)->execute()->current();
        if(empty($info['id']))
        {
            return null;
        }
        $aid = empty($info['aid'])?$info['id']:$info['aid'];
        $url= St_Functions::get_web_url($info['webid'])."/{$pinyin}/show_{$aid}.html";
        $str = '<a href="'.$url.'" target="_blank">'.$info['title'].'</a>';
        return $str;
    }

    /*
     * 获取产品信息
     * */

    private static function get_product_info($id, $typeid)
    {
        $channeltable = array(
            "1" => "sline_line",
            "2" => "sline_hotel",
            "3" => "sline_car",
            "5" => "sline_spot",
            "8" => "sline_visa",
            "13" => "sline_tuan"
        );
        $tablename = $typeid < 14 ? $channeltable[$typeid] : 'sline_model_archive';
        $sql = "SELECT * FROM {$tablename} WHERE id='$id'";
        $row = self::execute_sql($sql);
        return $row;
    }

    private static function get_extend_model_info($typeid)
    {
        $sql = "SELECT * FROM `sline_model` WHERE id='$typeid'";
        $row = self::execute_sql($sql);
        return $row ? $row[0] : array();

    }

    //获取评论的汇总信息
    private static function _get_comment_count($typeid, $articleid, $satisfyscore)
    {
        $where = " WHERE typeid='{$typeid}' AND articleid='{$articleid}' AND isshow=1 AND level BETWEEN 1 AND 5 ";
        //计算图片数量
        $sql_pic = "SELECT count(1) as num FROM sline_comment {$where} and LENGTH(TRIM(piclist))>0 ";
        $arr = self::execute_sql($sql_pic);
        $rtn['picnum'] = intval($arr[0]['num']);
        //计算等级
        $rtn = array_merge($rtn, St_Functions::get_satisfy($typeid, $articleid, $satisfyscore, array('isAll' => 1)));
        return $rtn;
    }

    private static function execute_sql($sql)
    {
        return DB::query(1, $sql)->execute()->as_array();

    }

}