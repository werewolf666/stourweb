<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Icon extends ORM {

    //重写delete方法
    public function delete()
    {
        Common::deleteRelativeImage($this->picurl);
        parent::delete();
    }

    public static function getIconName($iconlist)
    {
        $icon_arr = explode(',',$iconlist);
        if(empty($icon_arr))
            return null;
        $icon_arr=DB::select('kind')->from('icon')->where('id','in',$icon_arr)->execute()->as_array();
        $arr=array();
        foreach($icon_arr as $v)
        {
            array_push($arr,$v['kind']);
        }
        return $arr;

    }
    
}