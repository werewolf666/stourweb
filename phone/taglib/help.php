<?php
/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 广告获取标签
 */

class Taglib_Help {

    /*
     * 获取帮助分类
     * @param 参数
     * @return array

   */
    public static function kind($params)
    {
        $default = array(
            'row' => 5,
            'offset' => 0

        );
        $params = array_merge($default, $params);
        extract($params);
        $sql="SELECT id,kindname,litpic FROM `sline_help_kind` WHERE webid=0 AND isopen=1 ORDER BY displayorder ASC LIMIT {$offset},{$row}";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as &$r)
        {
            $r['url'] = Common::get_web_url(0)."/help/index_{$r['id']}.html";
            $r['title'] = $r['kindname'];
            $r['number'] = self::get_total($r['id'],$typeid);
        }
        return $arr;
    }

    public static function get_total($kindid,$typeid)
    {
        $typeid = !empty($typeid) ? $typeid : 0;
        $where = 'WHERE webid=0 ';
        $where .= !empty($kindid) ? " AND kindid=$kindid " : '';
        $where .= !empty($typeid) ? " AND FIND_IN_SET($typeid,type_id) " : '';
        $sql = "select count(*) as num from sline_help $where";
        $num = DB::query(Database::SELECT,$sql)->execute()->get('num');
        return $num;
    }

    /*
     * 获取帮助
     * */
    public static function article($params)
    {
        $default = array(
            'row' => 6,
            'offset' => 0,
            'kindid' => 0,
            'typeid' => 0
        );
        $params = array_merge($default, $params);
        extract($params);
        $typeid = !empty($typeid) ? $typeid : 0;
        $where = !empty($kindid) ? " AND kindid=$kindid " : '';
        $where.= !empty($typeid) ? " AND FIND_IN_SET($typeid,type_id) " : '';
        $sql = "SELECT aid,title,addtime,modtime FROM `sline_help` ";
        $sql.= "WHERE webid=0 {$where} ";
        $sql.= "ORDER BY displayorder ASC,addtime DESC LIMIT 0,{$row}";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as &$r)
        {
            $r['url'] = URL::site().'help/show_'.$r['aid'].'.html';
        }
        return $arr;

    }

} 