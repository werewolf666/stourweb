<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 评论管理
 * Class Comment
 */
class Model_Comment extends ORM
{
    /**
     * @function 获取满意度
     * @param $id
     * @param $typeid
     * @param int $satisfyscore
     * @param int $commentnum
     * @return string
     */
    public static function get_score($id, $typeid, $satisfyscore = 100, $commentnum = 0)
    {

        return St_Functions::get_satisfy($typeid,$id, $satisfyscore,array('suffix'=>''));
    }


    /**
     * @function 获取评论列表
     * @param $typeid
     * @param $articleid
     * @param $flag
     * @param $pageno
     * @param string $pagesize
     * @return array
     */
    public static function search_result($typeid, $articleid, $flag, $pageno, $pagesize = '5')
    {
        $where = " WHERE isshow=1 AND typeid='{$typeid}' AND articleid='{$articleid}'";
        if ($flag == 'pic')
        {
            $where .= " AND LENGTH(TRIM(piclist))>0 ";
        }
        elseif ($flag == 'well')
        {
            $where .= " AND level in (4,5) ";
        }
        elseif ($flag == 'mid')
        {
            $where .= " AND level in (2,3) ";
        }
        elseif ($flag == 'bad')
        {
            $where .= " AND level in (1) ";
        }
        $order_by = " ORDER BY addtime DESC ";
        $sql = " SELECT * FROM sline_comment " . $where . $order_by;
        //计算总数
        $totalSql = "SELECT count(*) as dd " . strchr($sql, " FROM");
        $totalSql = str_replace(strchr($totalSql, "ORDER BY"), '', $totalSql);
        $totalN = DB::query(1, $totalSql)->execute()->as_array();
        $totalNum = $totalN[0]['dd'] ? $totalN[0]['dd'] : 0;
        $offset = ($pageno - 1) * $pagesize;
        $sql .= "LIMIT {$offset},{$pagesize}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        foreach ($arr as &$r)
        {
            if(!empty($r['memberid']))
            {
                $awardinfo = Model_Member_Order::get_award_info($r['orderid']);
                $memberinfo = Model_Member::get_member_info($r['memberid']);
                $r['jifentprice'] = $awardinfo['jifentprice'];
                $r['jifencomment'] = $awardinfo['jifencomment'];
                $r['jifenbook'] = $awardinfo['jifenbook'];
                $r['nickname'] = empty($memberinfo['nickname']) ? '匿名' : St_Functions::cutstr_html($memberinfo['nickname'], 9);
                $r['litpic'] = !empty($memberinfo['litpic']) ? $memberinfo['litpic'] : Model_Member::member_nopic();
            }
            else
            {
                $r['jifencomment'] = $r['vr_jifencomment'];
                $r['nickname'] = $r['vr_nickname'] ? $r['vr_nickname'] : '匿名';
                $r['litpic'] = $r['vr_headpic'] ? $r['vr_headpic'] : Model_Member::member_nopic();
            }
            $r['litpic'] = empty($r['litpic']) ? Model_Member::member_nopic() : $r['litpic'];

            $r['pltime'] = self::_set_addtime();
            $r['percent'] = 20 * $r['level'] . '%';

            $r['level'] = Model_Member::member_rank($r['memberid'], array('return' => 'current','vr_grade'=>$r['vr_grade']));

            $r['addtime'] = date("Y-m-d H:i:s", $r['addtime']);
            //图片列表
            if (!empty($r["piclist"]))
            {
                $r['piclist'] = explode(',', $r['piclist']);
                $picthumb = array();
                foreach ($r['piclist'] as $pic)
                {
                    $picthumb[] =  St_Functions::img($pic, 86, 86);
                }
                $r["picthumb"] = $picthumb;
            }

            if(!empty($r['dockid']))
            {
                $p_info = DB::select('content','memberid')->from('comment')->where('id','=',$r['dockid'])->execute()->current();
                $reply = array();
                if($p_info['memberid'])
                {
                    $memberinfo = Model_Member::get_member_info($p_info['memberid']);
                    $reply['litpic'] = !empty($memberinfo['litpic']) ? $memberinfo['litpic'] : Model_Member::member_nopic();
                    $reply['nickname'] = empty($memberinfo['nickname']) ? '匿名' : St_Functions::cutstr_html($memberinfo['nickname'], 10);
                }
                else
                {
                    $reply['nickname'] = '匿名';
                    $reply['litpic'] = Model_Member::member_nopic();
                }
                $reply['content'] = $p_info['content'];
                $r['reply'] = $reply;


            }
        }

        $out = array(
            'total' => $totalNum,
            'list' => $arr
        );
        return $out;
    }

    /**
     * @function 随机设置评论时间
     * @return string
     */
    private static function _set_addtime()
    {
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
    //获取评论的汇总信息
    public  static function get_comment_count($typeid, $articleid, $satisfyscore)
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

    ////********************** PC端开始 ************************///

    /**
     * @function 获取某产品评论总数
     * @param $id
     * @param $typeid
     * @return int
     */
    public static function get_comment_num($id, $typeid)
    {

        $sql = "SELECT count(*) as num FROM `sline_comment` WHERE articleid='$id' AND typeid='$typeid'  AND isshow=1";
        $ar = DB::query(1,$sql)->execute()->current();
        return $ar['num'] ? $ar['num'] : 0;
    }

    /**
     * @function 获取指定评论
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
     * @function 获取奖励信息
     * @param $orderid
     * @return array
     */
    private static function get_award_info($orderid)
    {
        $sql = "SELECT jifenbook,jifentprice,jifencomment,price FROM `sline_member_order` WHERE id='$orderid'";
        $row = self::execute_sql($sql);
        return $row ? $row[0] : array();
    }

    /**
     * @function 获取会员信息
     * @param $memberid
     * @return array
     */
    private static function get_member_info($memberid)
    {
        $sql = "SELECT * FROM `sline_member` WHERE mid='$memberid'";
        $row = self::execute_sql($sql);
        return $row ? $row[0] : array();
    }

    /**
     * @function 执行sql
     * @param $sql
     * @return mixed
     */
    private static function execute_sql($sql)
    {
        return DB::query(1,$sql)->execute()->as_array();
    }

    /////********************* PC端结束 *************************///

    /////****************** 后台开始 ********************///

    /**
     * @function 获取会员名称
     * @param $memberid
     * @return string
     */
    public static function getMemberName($memberid)
    {
        $rs=DB::select('nickname')->from('member')->where('mid','=',$memberid)->execute()->current();
        return $rs ? $rs['nickname'] : '匿名';
    }

    /**
     * @function 获取模块名称 (弃用)
     * @param $typeid
     * @return mixed
     */
    public static function getPinlunModule($typeid)
    {

        $model = ORM::factory('model',$typeid);
        return $model->modulename;
    }


    /**
     * @function 获取某类产品的评论总数
     * @param $typeid
     * @return mixed
     */
    public static function get_comment_num_bytypeid($typeid)
    {

        $arr = DB::select(array(DB::expr('COUNT(`id`)'), 'total_num'))->from('comment')->where('typeid','=',$typeid)->execute();
        return $arr[0]['total_num'];
    }

    /**
     * @function 未审核评论数
     * @param $typeid
     * @return mixed
     */
    public static function get_comment_uncheck_num($typeid)
    {
        $arr = DB::select(array(DB::expr('COUNT(`id`)'), 'total_num'))
            ->from('comment')
            ->where('typeid','=',$typeid)
            ->and_where('isshow','=',0)
            ->execute();
        return $arr[0]['total_num'];
    }

    /////*****************  后台结束  *******************///

    //////****************手机端开始 ********************/////


    /////*****************手机端结束 ********************//////


}