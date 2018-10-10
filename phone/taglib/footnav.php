<?php
/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 底部导航获取标签
 */

class Taglib_Footnav {

    /*
     * 获取广告
     * @param 参数
     * @return array

   */
    public static function query($params)
    {
        $default=array('name'=>'');
        $params=array_merge($default,$params);
        extract($params);
        $sql="SELECT * FROM `sline_serverlist` WHERE mobileshow=1 ORDER BY displayorder ASC";
        $ar = DB::query(1,$sql)->execute()->as_array();
        foreach($ar as &$row)
        {
            $row['title'] = $row['servername'];
            $row['url'] = Common::get_web_url(0)."/servers/index_{$row['aid']}.html";
        }

        return $ar;
    }

    public static function pc($params)
    {
        $default=array(
            'row'=>5,
            'offset'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        $sql="SELECT * FROM `sline_serverlist` WHERE isdisplay=1 ORDER BY displayorder ASC LIMIT {$offset},{$row}";
        $ar = DB::query(1,$sql)->execute()->as_array();
        foreach($ar as &$row)
        {
            $row['title'] = $row['servername'];
            $row['url'] = Common::get_web_url(0)."/servers/index_{$row['aid']}.html";
        }

        return $ar;
    }

} 