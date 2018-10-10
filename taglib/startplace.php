<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-28 下午7:35
 * Desc:出发城市调用标签
 */
class Taglib_Startplace {


    public static function city($params)
    {
        $default=array(
            'row'=>'30',
            'offset'=>0,
            'flag'=>'all'
        );
        $params=array_merge($default,$params);
        extract($params);

        $sql="SELECT *,cityname as title FROM `sline_startplace` WHERE isopen=1 AND pid!=0 ORDER BY displayorder ASC LIMIT $offset,$row";
        $ar = DB::query(1,$sql)->execute()->as_array();
        return $ar;
    }

}