<?php defined('SYSPATH') or die('No direct script access.');

/**
 * php数组处理类
 */
class St_Array{


    /**
     * @function 清除数组空值
     * @param $arr
     * @return array
     */
    public static function remove_empty($arr)
    {
        $new_arr = array_diff($arr, array(null, 'null', '', ' '));
        return $new_arr;
    }
    /**
     * @function对查询结果集进行排序
     * @param array $list 查询结果
     * @param string $field 排序的字段名
     * @param string $sortby 排序类型 （asc正向排序 desc逆向排序 nat自然排序）
     * @return array
     */
   static function list_sort_by($list, $field, $sortby = 'asc')
    {
        if (is_array($list))
        {
            $refer = $result = array();
            foreach ($list as $i => $data)
            {
                $refer[$i] = $data[$field];
            }
            switch ($sortby)
            {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val)
            {
                $result[] = $list[$key];
            }
            return $result;
        }
        return false;
    }


   

    

}