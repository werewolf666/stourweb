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
            'row'=>'50',
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
			case 'childname':
                $arr = self::get_attr_bygroupname($typeid,$groupname,$offset,$row);
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
        $groupid = intval($groupid);
        $typeid = intval($typeid);
        $offset = intval($offset);
        $row = intval($row);
        $attrtable = DB::select('attrtable')->from('model')->where('id','=',$typeid)->execute()->get('attrtable');
        //排除签证模块(读取属性)
        if(!empty($attrtable) && $typeid!=8 )
        {
            //$where = "pid='$groupid' AND isopen=1";
            //$where.= $attrtable == 'model_attr' ? " AND typeid=$typeid " : '';
            $m = DB::select()->from($attrtable);
            $m->and_where('pid','=',$groupid);
            $m->and_where('isopen','=',1);
            if($attrtable == 'model_attr')
            {
                $m->and_where('typeid','=',$typeid);
            }
            $m->order_by('displayorder','asc');
            $m->offset($offset);
            $m->limit($row);
            $arr = $m->execute()->as_array();



        }
        foreach($arr as &$r)
        {
            $r['title'] = $r['attrname'];
        }
        return $arr;
    }
    //根据线路分类名来获取数据
    private  static function get_attr_bygroupname($typeid,$groupname,$offset,$row)
    {
        $arr = array();
        $attrtable = DB::select('attrtable')->from('model')->where('id','=',$typeid)->execute()->get('attrtable');
        $groupid = 0;
        //排除签证模块(读取属性)
        if(!empty($attrtable) && $typeid!=8 )
        {
            //$where = "attrname='$groupname' AND isopen=1";
            //$where.= $attrtable == 'model_attr' ? " AND typeid=$typeid " : '';
            $m = DB::select('id')->from($attrtable);
            $m->and_where('attrname','=',$groupname);
            $m->and_where('isopen','=',1);
            if($attrtable == 'model_attr')
            {
                $m->and_where('typeid','=',$typeid);
            }

            $groupid = $m->execute()->get('id');


        }

		if(!empty($groupid))
		{
             $arr = self::get_attr_bygroupid($typeid,$groupid,$offset,$row);
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
        $attrtable = DB::select('attrtable')->from('model')->where('id','=',$typeid)->execute()->get('attrtable');
        //排除通用模块和签证模块(读取属性)
       // if(!empty($attrtable) && $typeid!=8 && $typeid<17)
        if(!empty($attrtable) && $typeid!=8)
        {

            //$where = "pid=0 AND isopen=1";
            //$where.= $attrtable == 'model_attr' ? " AND typeid=$typeid " : '';
            $m = DB::select()->from($attrtable);
            $m->where('pid','=',0);
            $m->and_where('isopen','=',1);
            if($attrtable == 'model_attr')
            {
                $m->and_where('typeid','=',$typeid);
            }
            $m->order_by(DB::expr('ifnull(displayorder,9999)'),'asc');
            $m->offset($offset);
            $m->limit($row);
            $arr = $m->execute()->as_array();




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
        //$attrtable = ORM::factory('model',$typeid)->get('attrtable');
        $attrtable = DB::select('attrtable')->from('model')->where('id','=',$typeid)->execute()->get('attrtable');
        //排除通用模块和签证模块(读取属性)
        // if(!empty($attrtable) && $typeid!=8 && $typeid<17)
        if(!empty($attrtable) && $typeid!=8)
        {
            $m = DB::select()->from($attrtable);
            $m->where('pid','!=',0);
            $m->and_where('isopen','=',1);
            if($attrtable == 'model_attr')
            {
                $m->and_where('typeid','=',$typeid);
            }
            $m->offset($offset);
            $m->limit($row);
            $arr = $m->execute()->as_array();


        }
        return $arr;

    }

}