<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Fenxiao_Withdraw extends ORM {



    public static function get_list($mid,$params)
    {

        $start=empty($params['start'])?0:$params['start'];
        $pagesize=intval($params['pagesize']);
        $status=intval($params['status']);
        $starttime=intval($params['starttime']);
        $endtime=intval($params['endtime']);
        $limitStr=empty($pagesize)?"":"limit {$start},{$pagesize}";

        $w=" where  memberid={$mid}";
        if($status!==null)
        {
            $w.=" and status=$status";
        }
        if(!empty($starttime))
        {
            $w.=" and addtime>={$starttime}";
        }
        if(!empty($endtime))
        {
            $w.=" and addtime<={$endtime}";
        }
        $sql="select * from sline_fenxiao_withdraw $w order by addtime desc {$limitStr}";
        $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
        return $list;
    }

}