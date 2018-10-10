<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 字符串处理类
 */
class St_String{

    /*
    * @function 截取含有 html标签的字符串
    * @param (string) $str   待截取字符串
    * @param (int)  $lenth  截取长度
    * @param (string) $repalce 超出的内容用$repalce替换之（该参数可以为带有html标签的字符串）
    * @param (string) $anchor 截取锚点，如果截取过程中遇到这个标记锚点就截至该锚点处
    * @return (string) $result 返回值
    * @demo  $res = cut_html_str($str, 256, '...'); //截取256个长度，其余部分用'...'替换
    */
    public static function cut_html_str($str, $lenth, $replace = '', $anchor = '<!-- break -->')
    {
        $_lenth = mb_strlen($str, "utf-8"); // 统计字符串长度（中、英文都算一个字符）
        if($_lenth <= $lenth)
        {
            return $str; // 传入的字符串长度小于截取长度，原样返回
        }
        $strlen_var = strlen($str); // 统计字符串长度（UTF8编码下-中文算3个字符，英文算一个字符）
        if(strpos($str, '<') === false)
        {
            return mb_substr($str, 0, $lenth).$replace; // 不包含 html 标签 ，直接截取
        }
        if($e = strpos($str, $anchor))
        {
            return mb_substr($str, 0, $e); // 包含截断标志，优先
        }
        $html_tag = 0; // html 代码标记
        $result = ''; // 摘要字符串
        $html_array = array('left' => array(), 'right' => array()); //记录截取后字符串内出现的 html 标签，开始=>left,结束=>right
        /*
        * 如字符串为：<h3><p><b>a</b></h3>，假设p未闭合，数组则为：array('left'=>array('h3','p','b'), 'right'=>'b','h3');
        * 仅补全 html 标签，<? <% 等其它语言标记，会产生不可预知结果
        */
        for($i = 0; $i < $strlen_var; ++$i)
        {
            if(!$lenth) break; // 遍历完之后跳出
            $current_var = substr($str, $i, 1); // 当前字符
            if($current_var == '<')
            { // html 代码开始
                $html_tag = 1;
                $html_array_str = '';
            }
            else
            {
                if($html_tag == 1)
                { // 一段 html 代码结束
                    if($current_var == '>')
                    {
                        $html_array_str = trim($html_array_str); //去除首尾空格，如 <br / > < img src="" / > 等可能出现首尾空格
                        if(substr($html_array_str, -1) != '/')
                        { //判断最后一个字符是否为 /，若是，则标签已闭合，不记录
                            // 判断第一个字符是否 /，若是，则放在 right 单元
                            $f = substr($html_array_str, 0, 1);
                            if($f == '/')
                            {
                                $html_array['right'][] = str_replace('/', '', $html_array_str); // 去掉 '/'
                            }
                            else
                            {
                                if($f != '?')
                                { // 若是?，则为 PHP 代码，跳过
                                    // 若有半角空格，以空格分割，第一个单元为 html 标签。如：<h2 class="a"> <p class="a">
                                    if(strpos($html_array_str, ' ') !== false)
                                    {
                                        // 分割成2个单元，可能有多个空格，如：<h2 class="" id="">
                                        $html_array['left'][] = strtolower(current(explode(' ', $html_array_str, 2)));
                                    }
                                    else
                                    {
                                        //若没有空格，整个字符串为 html 标签，如：<b> <p> 等，统一转换为小写
                                        $html_array['left'][] = strtolower($html_array_str);
                                    }
                                }
                            }
                        }
                        $html_array_str = ''; // 字符串重置
                        $html_tag = 0;
                    }
                    else
                    {
                        $html_array_str .= $current_var; //将< >之间的字符组成一个字符串,用于提取 html 标签
                    }
                }
                else
                {
                    --$lenth; // 非 html 代码才记数
                }
            }
            $ord_var_c = ord($str{$i});
            switch (true)
            {
                case (($ord_var_c & 0xE0) == 0xC0): // 2 字节
                    $result .= substr($str, $i, 2);
                    $i += 1;
                    break;
                case (($ord_var_c & 0xF0) == 0xE0): // 3 字节
                    $result .= substr($str, $i, 3);
                    $i += 2;
                    break;
                case (($ord_var_c & 0xF8) == 0xF0): // 4 字节
                    $result .= substr($str, $i, 4);
                    $i += 3;
                    break;
                case (($ord_var_c & 0xFC) == 0xF8): // 5 字节
                    $result .= substr($str, $i, 5);
                    $i += 4;
                    break;
                case (($ord_var_c & 0xFE) == 0xFC): // 6 字节
                    $result .= substr($str, $i, 6);
                    $i += 5;
                    break;
                default: // 1 字节
                    $result .= $current_var;
            }
        }
        if($html_array['left'])
        { //比对左右 html 标签，不足则补全
            $html_array['left'] = array_reverse($html_array['left']); //翻转left数组，补充的顺序应与 html 出现的顺序相反
            foreach($html_array['left'] as $index => $tag)
            {
                $key = array_search($tag, $html_array['right']); // 判断该标签是否出现在 right 中
                if($key !== false)
                { // 出现，从 right 中删除该单元
                    unset($html_array['right'][$key]);
                }
                else
                { // 没有出现，需要补全
                    $result .= '</' . $tag . '>';
                }
            }
        }
        return $result . $replace;
    }

    /**
     * @function 去除字符串所有的标点符号
     * @param $str
     * @return mixed|string
     */
    public static function filter_mark($str)
    {
        $text=preg_replace("/[[:punct:]\s]/u",'',$str);
        $text=urlencode($text);
        $text=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/",'',$text);
        $text=urldecode($text);
        return $text;
    }

    /**
     * @function 去掉字符串里的英文引号
     * @param $str
     * @param string $replace
     * @return mixed
     */
    public static function filter_quotes($str,$replace='')
    {
        return preg_replace("/\\'|\\\"/",$replace,$str);
    }


    /**
     * @function 分词方法
     * @param $keywrod  关键词
     * @return mixed
     */
    public static function split_keyword($keywrod)
    {
        $file = CACHE_DIR . 'v5/split_keyword.php';
        if (file_exists($file))
        {
            $data = include $file;
        }
        $keywrod = self::filter_mark($keywrod);
        if($keywrod&&$data[$keywrod])
        {
            return $data[$keywrod];
        }

        $api = "http://www.stourweb.com/api/tools/split?keyword=$keywrod";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result,true);
        if(!$result['data'][0])
        {
            $out =  array($keywrod);

        }
        else
        {
            $out =  $result['data'];
        }
        self::add_split_keyword_cache($keywrod,$out);
        return $out;
    }


    /**
     * @function 增加分词的缓存
     */
    private static function add_split_keyword_cache($keyword,$result)
    {
        $file = CACHE_DIR . 'v5/split_keyword.php';
        if (file_exists($file))
        {
            $data = include $file;
        }
        if(!is_array($data))
        {
            $data = array();
        }
        if($data[$keyword])
        {
            return false;
        }

        $data[$keyword] = $result;

        file_put_contents($file, "<?php \r\n return " . var_export($data, true) . ';');

    }
   

    

}