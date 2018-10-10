<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-28 下午7:35
 * Desc:usernav标签
 */
class Taglib_Usernav {


    public static function topkind($params)
    {
        $default=array(
            'offset'=>0,
            'row'=>10
        );
        $params=array_merge($default,$params);
        extract($params);
        $offset = intval($offset);
        $row = intval($row);
        $sql="SELECT id,linkurl as url,kindname,litpic,remark,color FROM `sline_plugin_leftnav` ";
        $sql.="WHERE pid=0 AND isopen=1  ORDER BY displayorder ASC LIMIT {$offset},{$row}";
        $ar = DB::query(1,$sql)->execute()->as_array();
        $ar = self::handle_data($ar);
        return $ar;

    }

    public static function childnav($params)
    {
        $default=array(
            'offset'=>0,
            'row'=>10,
            'parentid'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        $sql="SELECT id,linkurl as url,kindname,litpic,remark,color FROM `sline_plugin_leftnav` ";
        $sql.="WHERE isopen=1 AND pid='$parentid' ";
        $sql.="ORDER BY displayorder ASC LIMIT {$offset},{$row}";
        $ar = DB::query(1,$sql)->execute()->as_array();
        $ar = self::handle_data($ar);
        return $ar;

    }

    private static function handle_data($ar)
    {
        foreach($ar as &$r)
        {
            $r['kindname'] = !empty($r['color']) ? "<span style='color:".$r['color']."'>{$r['kindname']}</span>" : $r['kindname'];
            $r['litpic'] = !empty($r['litpic']) ? $r['litpic'] : $GLOBALS['cfg_basehost'].'/res/images/usernav.png';
        }
        return $ar;
    }

}