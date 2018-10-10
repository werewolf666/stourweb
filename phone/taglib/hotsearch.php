<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-28 下午7:35
 * Desc:会员标签
 */
class Taglib_Hotsearch {

    /*
     * 获取热搜词
     * @param 参数
     * @return array

   */
    public static function hot($params)
    {
        $default=array('row'=>8);
        $params=array_merge($default,$params);
        extract($params);
        $sql ="SELECT keyword as title FROM `sline_search_keyword` ";
        $sql.="WHERE isopen = 1 ORDER BY keynumber DESC LIMIT 0,{$row}";
        $ar = DB::query(1,$sql)->execute()->as_array();
        foreach($ar as &$r)
        {
            $r['url'] = URL::site().'search/cloudsearch?keyword='.urlencode($r['title']);
        }

        return $ar;
    }

}