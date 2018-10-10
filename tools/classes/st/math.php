<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 数学公式,数字相关处理类
 */
class St_Math{


    /**
     * @function 生成随机数
     * @param int $length
     * @return int
     */
    public static function get_random_number($length = 4)
    {
        $min = pow(10, ($length - 1));
        $max = pow(10, $length) - 1;
        return mt_rand($min, $max);
    }


   

    

}