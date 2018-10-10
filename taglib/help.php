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

        }
        return $arr;
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
        $where.= " AND FIND_IN_SET($typeid,type_id) ";
        $sql = "SELECT aid,title,addtime,modtime FROM `sline_help` ";
        $sql.= "WHERE webid=0 {$where} ";
        $sql.= "ORDER BY displayorder ASC,addtime DESC LIMIT {$offset},{$row}";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as &$r)
        {
            $r['url'] = Common::get_web_url(0).'/help/show_'.$r['aid'].'.html';
        }
        return $arr;

    }

} 