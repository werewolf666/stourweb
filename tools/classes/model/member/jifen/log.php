<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 积分管理
 * Class Jifen
 */
class Model_Member_Jifen_Log extends ORM
{
    CONST JFUSE = 1;//积分使用
    CONST JFGET = 2;//积分获取

    /**
     * @function 送积分操作
     * @param $orderid
     */
    public static function refund($orderid)
    {

        $row = DB::select()->from('member_order')->where('id','=',$orderid)->execute()->current();
        if(isset($row))
        {
            $memberid = $row['memberid'];
            $jifenbook = intval($row['jifenbook']);

            $sql = "UPDATE `sline_member` SET jifen=jifen+{$jifenbook} WHERE mid='$memberid'";
            $flag = DB::query(Database::UPDATE,$sql)->execute();
            if($flag)
            {

             $content = "预订{$row['productname']}获得{$jifenbook}积分";
              self::add_jifen_log($memberid,$content,$jifenbook,self::JFGET);
            }

        }

    }

    /**
     * @function 积分日志操作函数
     * @param $memberid
     * @param $content
     * @param $jifen
     * @param $type
     * @return mixed
     */
    public static function add_jifen_log($memberid,$content,$jifen,$type)
    {
        $addtime = time();
        $insert_arr = array(
            'memberid' => $memberid,
            'content'  => $content,
            'jifen' => $jifen,
            'type'  =>$type,
            'addtime' => $addtime
        );
        return DB::insert('member_jifen_log',array_keys($insert_arr))->values(array_values($insert_arr))->execute();
    }

    ///******************** PC端开始 *****************************///
    /**
     * @function 搜索我的积分记录
     * @param $mid
     * @param $currentpage
     * @param int $pagesize
     * @return array
     */

    public static function log_list($mid,$currentpage,$pagesize=10,$jifentype=0)
    {

        $page = $currentpage ? $currentpage : 1;
        $offset = (intval($page)-1)*$pagesize;

        $sql = "SELECT a.* FROM `sline_member_jifen_log` a ";
        $sql.= "WHERE a.memberid=$mid ";
        if($jifentype)
        {
            $sql.= " AND a.type=".$jifentype.' ';
        }

        $sql.= "ORDER BY addtime desc ";


        //计算总数
        $totalSql = "SELECT count(*) as dd ".strchr($sql," FROM");
        $totalSql = str_replace(strchr($totalSql,"ORDER BY"),'', $totalSql);//去掉order by
        $totalN = DB::query(1,$totalSql)->execute()->get('dd');
        $totalNum = $totalN ? $totalN : 0;
        $sql.= "LIMIT {$offset},{$pagesize}";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as &$row)
        {
            $row['jifentype'] = $row['type'] == 1 ? '使用' : '获得';

        }

        $out = array(
            'total' => $totalNum,
            'list' => $arr
        );
        return $out;
    }

    ///************************** PC端结束 ***************************//


}

