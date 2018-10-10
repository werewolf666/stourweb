<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-26 上午10:04
 * Desc:属性调用标签
 */

class Taglib_Attr {

    /**
     * @param $params
     * @return mixed
     * @description 标签接口
     */
    public static function query($params)
    {
        $default=array(
            'row'=>'10',
            'flag'=>'',
            'offset'=>0,
            'groupid'=>0,
            'typeid'=>0,
            'limit'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        switch($flag)
        {
            case 'childitem':
                $arr = self::get_attr_bygroupid($typeid,$groupid,$offset,$row);
                break;
            case 'grouplist':
                $arr = self::grouplist($typeid,$offset,$row);
                break;
            case 'childlist':
                $arr = self::childlist($typeid,$offset,$row);
                break;

        }
        return $arr;

    }

    /**
     * 根据组名获取下级属性组
     * @param $typeid
     * @param $groupid
     * @param $offset
     * @param $limit
     * @return Array
     *
     */
    private  static function get_attr_bygroupid($typeid,$groupid,$offset,$row)
    {
        $arr = array();
        $attrtable = ORM::factory('model',$typeid)->get('attrtable');

        //排除签证模块(读取属性)
        if(!empty($attrtable) && $typeid!=8 )
        {
            $where = "pid=$groupid AND isopen=1";
            $where.= $attrtable == 'model_attr' ? " AND typeid=$typeid " : '';
            $arr = ORM::factory($attrtable)
                ->where($where)
                ->offset($offset)
                ->limit($row)
                ->get_all();

        }
        foreach($arr as &$r)
        {
            $r['title'] = $r['attrname'];
        }
        return $arr;
    }

    /**
     * @param $typeid
     * @param $offset
     * @param $row
     * @return Array
     */

    private static function grouplist($typeid,$offset,$row)
    {
        $arr = array();
        $attrtable = ORM::factory('model',$typeid)->get('attrtable');
        //排除通用模块和签证模块(读取属性)
       // if(!empty($attrtable) && $typeid!=8 && $typeid<17)
        if(!empty($attrtable) && $typeid!=8)
        {
            $where = "pid=0 AND isopen=1";
            $where.= $attrtable == 'model_attr' ? " AND typeid=$typeid " : '';
            $arr = ORM::factory($attrtable)
                ->where($where)
                ->offset($offset)
                ->limit($row)
                ->get_all();
        }
        return $arr;

    }

    /**
     * @param $typeid
     * @param $offset
     * @param $row
     * @return Array
     * 所有子集
     */
    private static function childlist($typeid,$offset,$row)
    {
        $arr = array();
        $attrtable = ORM::factory('model',$typeid)->get('attrtable');
        //排除通用模块和签证模块(读取属性)
        // if(!empty($attrtable) && $typeid!=8 && $typeid<17)
        if(!empty($attrtable) && $typeid!=8)
        {
            $where = "pid!=0 AND isopen=1";
            $where.= $attrtable == 'model_attr' ? " AND typeid=$typeid " : '';
            $arr = ORM::factory($attrtable)
                ->where($where)
                ->offset($offset)
                ->limit($row)
                ->get_all();
        }
        return $arr;

    }

}