<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Model_Attr extends ORM {

    ///********************后台开始  ************************///
    /**
     * @function 获取多个属性名称字符串
     * @param $attrid_str
     * @param $typeid
     * @param string $separator
     * @return null|string
     */
    public static function getAttrnameList($attrid_str,$typeid,$separator=',')
    {
        $attrid_arr=explode(',',$attrid_str);
        if(empty($attrid_arr))
            return null;
        $name_arr=DB::select('attrname')->from('model_attr')->where('id','in',$attrid_arr)->and_where('typeid','=',$typeid)->execute()->as_array();
        $attr_str='';
        foreach($name_arr as $v)
        {
            $attr_str.=$v['attrname'].$separator;
        }
        $attr_str=trim($attr_str,$separator);
        return $attr_str;

    }
    ///********************后台结束  ************************///


    ///********************PC端开始  ************************///
    /**
     * @function 根据属性id返回属性数组.
     * @param $attrid
     * @return mixed
     */
    public static function get_attr_list($attrid,$typeid)
    {
        if(empty($attrid))return array();
        $sql = "SELECT id,attrname FROM `sline_model_attr` WHERE id IN($attrid) AND isopen=1 AND pid!=0 AND typeid=$typeid ORDER BY displayorder ASC";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }

    /**
     * @function 获取多个属性名称字符串
     * @param $attrid_str
     * @param string $separator
     * @param $typeid
     * @return string
     */
    public static function get_attrname_list($attrid_str,$separator=',',$typeid)
    {
        $attrid_arr=explode('_',$attrid_str);

        $arr = DB::select('attrname')->from('model_attr')->where('id','in',$attrid_arr)->and_where('typeid','=',$typeid)->execute()->as_array();
        $out = array();
        foreach($arr as $v)
        {
            $out[] = $v['attrname'];
        }
        $attr_str=implode($out,$separator);
        return $attr_str;

    }
    ///*************** PC端结束  ********************************///


}