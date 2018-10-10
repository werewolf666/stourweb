<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 校验处理类
 */
class St_Validate{

    /**
     * 判断email格式是否正确
     * @param $email
     */
    public static function is_email($email)
    {
        return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
    }

    /**
     * @function 验证是否是身份证号码
     * @param $value
     * @param string $pattern
     * @return bool|int
     */
    public static function is_idcard($value,$pattern='/^d{6}((1[89])|(2d))d{2}((0d)|(1[0-2]))((3[01])|([0-2]d))d{3}(d|X)$/i')
    {
        if(!$value) return false;
        else if(strlen($value)>18) return false;
        return preg_match($pattern,$value);
    }

    /**
     * @function 验证是否是手机号码
     * @param $value
     * @param string $pattern
     * @return bool|int
     */
    static function is_mobile($value,$pattern='/^(0)?1([3|4|5|8])+([0-9]){9,10}$/')
    {
        //支持国际版：([0-9]{1,5}|0)?1([3|4|5|8])+([0-9]){9,10}
        if(!$value) return false;
        return preg_match($pattern,$value);
    }

    /**
     * @function 验证是否是字母
     * @param $str
     * @return int
     */
    static function is_letter($str)
    {
        $pattern ='/^[a-zA-Z]+$/';
        return preg_match($pattern,$str);
    }


   


   

    

}