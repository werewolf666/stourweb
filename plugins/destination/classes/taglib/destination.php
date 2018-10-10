<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 目的地调用标签
 */
class Taglib_Destination
{

    /*
     * 获取广告
     * @param 参数
     * @return array
   */
    public static function query($params)
    {
        $default = array(
            'flag' => '',
            'row' => 8,
            'offset' => 0,
            'pid' => 0,
            'typeid' => 0,
            'isindex' => 0,//是否是首页
        );
        $params = array_merge($default, $params);
        extract($params);
        switch ($flag) {
            case 'top':
                $list = self::get_top($offset, $row,$isindex);
                break;
            case 'next':
                $list = self::get_next($offset, $row, $pid, $typeid,$isindex);
                break;
            case 'hot':
                $list = self::get_hot_dest($typeid, $offset, $row, $destid,$isindex);
                break;
            //栏目获取下级目的地,如果下级为空则返回同级
            case 'nextsame':
                $list = self::get_next_same($typeid, $offset, $row, $pid,$isindex);
                break;
            case 'same':
                $list = self::get_same($offset,$row,$pid,$isindex);
        }
        return $list;
    }


    /**
     * @function 获取下级目的地列表
     * @param $offset 偏移量
     * @param $row  条数
     * @param $pid  父级目的地id
     * @return mixed 下级目的地列表
     */
    public static function get_next($offset, $row, $pid, $typeid,$isindex)
    {

        $pid = empty($pid) ? 0 : $pid;
        $m = DB::select('id', 'kindname', 'pinyin', 'litpic')->from('destinations');
        $m->where('isopen', '=', 1);
        $m->and_where(DB::expr("find_in_set({$typeid},opentypeids)"), '>', 0);
        $m->and_where('pid', '=', $pid);
        //栏目首页开关判断
        if($isindex)
        {
            $m->and_where('isnav','=',1);
        }
        $m->order_by(DB::expr('ifnull(displayorder,9999)'), 'asc');
        $m->offset($offset);
        $m->limit($row);
        $arr = $m->execute()->as_array();
        return $arr;
    }


    /**
     * @function 按栏目读取热门目的地
     * @param int $typeid
     * @param int $offset
     * @param int $row
     * @param $destid
     * @return array
     */
    private static function get_hot_dest($typeid, $offset = 0, $row = 30, $destid)
    {

        $m = DB::select('id', 'kindname', 'pinyin', 'litpic')->from('destinations');
        $m->where('isopen', '=', 1);
        $m->and_where('ishot', '=', 1);
        $m->and_where(DB::expr("find_in_set({$typeid},opentypeids)"), '>', 0);
        if ($destid)
        {
            $m->and_where('pid', '=', $destid);
        }
        $m->order_by(DB::expr('ifnull(displayorder,9999)'), 'asc');
        $m->offset($offset);
        $m->limit($row);
        $arr = $m->execute()->as_array();
        return $arr;
    }

    /**
     * @function 获取顶级目的地
     * @param $offset
     * @param $row
     * @return mixed 顶级目的地列表
     */
    private static function get_top($offset, $row,$isindex)
    {
        $query = DB::select()->from('destinations')
            ->where('pid', '=', 0)
            ->and_where('isopen', '=', 1)
            ->and_where('isnav', '=', 1)
            ->and_where(DB::expr("find_in_set(12,opentypeids)"), '>', 0);
        //栏目首页开关判断
        if($isindex)
        {
            $query->and_where('isnav','=',1);
        }

        $query->order_by(DB::expr('ifnull(displayorder,9999)'), 'asc')
            ->offset($offset)
            ->limit($row);
        $arr = $query->execute()->as_array();


        return $arr;

    }

    public static function get_same($offset, $row, $pid,$isindex)
    {

        $pid = empty($pid) ? 0 : $pid;
        $m = DB::select('id', 'kindname', 'pinyin', 'litpic')->from('destinations');
        $m->where('isopen', '=', 1);
        $m->and_where('pid', '=', $pid);
        $m->and_where(DB::expr("find_in_set(12,opentypeids)"), '>', 0);
        //栏目首页开关判断
        if($isindex)
        {
            $m->and_where('isnav','=',1);
        }
        $m->order_by(DB::expr('ifnull(displayorder,9999)'), 'asc');
        $m->offset($offset);
        $m->limit($row);
        $arr = $m->execute()->as_array();
        return $arr;
    }


}