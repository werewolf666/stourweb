<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Startplace extends ORM
{

    ////******************** PC端开始 ********************************///
    /**
     * @function 获取城市名称
     * @param $cityId
     * @return mixed
     */
    public static function start_city($cityId)
    {
        $city_id_list = explode(",", $cityId);
        $result = "";
        foreach ($city_id_list as $city_id)
        {
            if (empty($city_id) || preg_match('/[\x{4e00}-\x{9fa5}]+/u', $city_id))
            {
                $result .= $result . ",";
            } else
            {
                $city = DB::select('cityname')->from('startplace')->where("id ={$city_id}")->execute()->current();
                $result .= $city['cityname'] . ",";
            }
        }

        return rtrim($result, ",");
    }
    ////****************PC端结束  ************************************////

    ///******************后台开始 ***************************///

    /**
     * @function 获取出发地列表
     * @return Array
     */
    public static function getList()
    {
        $model=ORM::factory('startplace');
        $list=$model->where('pid=0')->get_all();
        foreach($list as $k=>$v)
        {
            $list[$k]['children']=$model->where("pid={$v['id']} and isopen=1")->get_all();
        }
        return $list;
    }
    ///******************后台结束 **************************///
}