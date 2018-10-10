<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 目的地调用标签
 */
class Taglib_Dest
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
            'typeid' => 0
        );
        $params = array_merge($default, $params);
        extract($params);
        switch ($flag)
        {
            case 'top':
                $list = Model_Destinations::get_top($offset, $row);
                break;
            case 'next':
                $list = Model_Destinations::get_next($offset, $row, $pid,$typeid);
                break;
            case 'index_nav':
                $list = Model_Destinations::get_index_nav($offset, $row);
                break;
            case 'channel_nav':
                $list = Model_Destinations::get_channel_nav($offset, $row, $typeid);
                break;
            case 'hot':
                $list = Model_Destinations::get_hot_dest($typeid,$offset,$row,$destid);
                break;
            //栏目获取下级目的地,如果下级为空则返回同级
            case 'nextsame':
                $list = self::get_next_same($typeid,$offset,$row,$pid);
                break;

            case 'dest':
                $list = Model_Destinations::get_dest($pid, $typeid, $offset, $row);
                break;
            case 'order':
                $list = self::get_dest_by_order($offset,$row);
                break;

        }
        return $list;
    }

    /**
     * @param $typeid
     * @param $offset
     * @param $row
     * @param $pid
     * @desc :根据pid返回相应子目的地,如果子目的地为空则返回同级目的地
     */
    private  static function get_next_same($typeid,$offset,$row,$pid)
    {


        if(empty($typeid)) return array();

        $py = DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
        $table = 'sline_'.$py.'_kindlist';
        $typewhere =$typeid ? " AND FIND_IN_SET($typeid,opentypeids) " : "";

        $pid = empty($pid) ? 0 : $pid;
        $sql = "SELECT a.id,a.kindname,a.pinyin,a.iswebsite,a.weburl FROM `sline_destinations` a ";
        $sql.= "LEFT JOIN {$table} b ON a.id=b.kindid ";
        //主目的地开启,且栏目对应的目的地也应开启
        $sql.= "WHERE a.isopen=1 AND a.pid='$pid' {$typewhere}   ";
        $sql.= "ORDER BY IFNULL(b.displayorder,999) ASC ";
        $sql.= "LIMIT $offset,$row";

        $arr = DB::query(1,$sql)->execute()->as_array();

        if(empty($arr))
        {

            $id = ORM::factory('destinations',$pid)->get('pid');


            if(!IS_NULL($id))
            {
                $sql ="SELECT a.id,a.kindname,a.pinyin,a.iswebsite,a.weburl FROM `sline_destinations` a ";
                $sql.="LEFT JOIN {$table} b on a.id=b.kindid ";
                $sql.="WHERE a.isopen=1 and a.pid=$id {$typewhere} ";
                $sql.="ORDER BY IFNULL(b.displayorder,999) ASC ";
                $sql.= "LIMIT $offset,$row";
                $arr = DB::query(1,$sql)->execute()->as_array();

            }


        }
        return $arr;


    }

    //按顺序读取目的地.
    private static function get_dest_by_order($offset,$row)
    {
        $sql = "SELECT * FROM `sline_destinations` WHERE isopen=1 ORDER BY displayorder ASC LIMIT $offset,$row";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }




} 