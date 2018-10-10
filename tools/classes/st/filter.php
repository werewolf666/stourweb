<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 过滤处理类
 */
class St_Filter{


    /**
     * xss 过滤
     * @param $param
     * @return array|string
     */
    public static function remove_xss($param, $reject_check = true)
    {
        require_once TOOLS_Lib . 'htmlpurifier/library/HTMLPurifier.auto.php';
        $purifier = new HTMLPurifier();
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8'); //字符编码（常设）
        //如果参数是数组
        if (is_array($param))
        {
            foreach ($param as &$value)
            {
                if (is_array($value))

                {
                    $value = $purifier->purifyArray($value);
                }
                else
                {
                    $value = $purifier->purify($value);
                    if ($reject_check)
                    {
                        //self::reject_check($value);
                    }

                }
            }
            $out = $param;
        }
        else
        {
            $out = $purifier->purify($param);
            if ($reject_check)
            {
                //self::reject_check($out);
            }
        }
        return $out;
    }

    /*
    * 注入检测
    */
    public static function reject_check($param)
    {
        $arr = explode(' ', $param);
        if (count($arr) >= 1)
        {
            foreach ($arr as $ar)
            {
                $check = preg_match('/^select$|^insert$|^update$|^delete$|^and$|^or$|\'|\\*|\*|\.\.\/|\.\/|^union$|^into$|^load_file$|^outfile$/i', $ar);

                if ($check)
                {
                    exit($ar);
                }
            }

        }
    }


   

    

}