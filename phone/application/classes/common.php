<?php

/**
 * 公共静态类模块
 * User: Netman
 * Date: 15-09-12
 * Time: 下午14:06
 */
class Common
{


    /**
     *  获取编辑器
     *
     * @access    public
     * @param     string $fname 表单名称
     * @param     string $fvalue 表单值
     * @param     string $nheight 内容高度
     * @param     string $etype 编辑器类型
     * @param     string $gtype 获取值类型
     * @param     string $isfullpage 是否全屏
     * @return    string
     */
    public static function get_editor($fname, $fvalue, $nwidth = "700", $nheight = "350", $etype = "Sline", $ptype = '', $gtype = "print", $jsEditor = false)
    {

        require(APPPATH . '/vendor/slineeditor/ueditor.php');

        $UEditor = new UEditor();
        $UEditor->basePath = $GLOBALS['cfg_cmspath'] . '/application/vendor/slineeditor/';
        $nheight = $nheight == 400 ? 300 : $nheight;
        $config = $events = array();
        $GLOBALS['tools'] = empty($toolbar[$etype]) ? $GLOBALS['tools'] : $toolbar[$etype];
        $config['toolbars'] = $GLOBALS['tools'];
        $config['minFrameHeight'] = $nheight;
        $config['initialFrameHeight'] = $nheight;
        $config['initialFrameWidth'] = $nwidth;
        $config['autoHeightEnabled'] = false;
        if (!$jsEditor)
        {
            $code = $UEditor->editor($fname, $fvalue, $config, $events);
        }
        else
        {
            $code = $UEditor->jseditor($fname, $fvalue, $config, $events);
        }

        if ($gtype == "print")
        {
            echo $code;
        }
        else
        {
            return $code;
        }

    }

    /**
     * @param $filelist 要加载的js文件列表
     * @param bool $minjs 是否合并js文件
     * @param bool $default 是否从默认目录加载
     * @return string
     * @desc,加载js文件
     */
    public static function js($filelist, $minjs = false, $default = true)
    {
        $filearr = explode(',', $filelist);
        $jsArr = array();
        $out = $v = '';
        foreach ($filearr as $file)
        {
            if ($default == true)
            {
                if (strpos($GLOBALS['cfg_default_templet'], 'default') === false)
                {
                    $tfile = DOCROOT . '/public/' . trim($GLOBALS['cfg_default_templet'], '\../') . '/js/' . $file;
                    if (file_exists($tfile))
                    {
                        $file = "/public/" . trim($GLOBALS['cfg_default_templet'], '\../') . "/js/{$file}";
                    }
                    else
                    {
                        $tfile = DOCROOT . "/public/js/" . $file;
                        $file = "/public/js/{$file}";
                    }
                }
                else
                {
                    $tfile = DOCROOT . "/public/js/" . $file;
                    $file = '/public/js/' . $file;
                }

            }
            else
            {
                $tfile = DOCROOT . $file;

            }

            if (file_exists($tfile))
            {
                // $out .= HTML::script($file);
                $jsArr[] = $file;
            }

        }
        if ($jsArr)
        {
            //如果开启自动合并js
            if ($minjs)
            {
                $f = implode(',', $jsArr);
                $jsUrl = URL::site('pub/js?file=' . $f);
                $out = '<script type="text/javascript"src="' . $jsUrl . '"></script>' . "\r\n";
            }
            else
            {
                foreach ($jsArr as $js)
                {
                    $out .= HTML::script($js) . "\r\n";
                }
            }

        }
        return $out;

    }


    /**
     * @param $filelist  加载的css文件列表
     * @param bool $mincss 是否合并生成.
     * @param bool $default 是否从默认css目录加载,如果值为false,则直接从根目录加载相应文件,即DOCROOT+文件名
     * @return
     * @desc 加载css文件.
     */
    public static function css($filelist, $mincss = false, $default = true)
    {
        $filearr = explode(',', $filelist);
        $filelist = array();
        $out = '';

        foreach ($filearr as $file)
        {
            if ($default == true)
            {

                if (strpos($GLOBALS['cfg_default_templet'], 'default') === false)
                {
                    $tfile = DOCROOT . "/public/" . trim($GLOBALS['cfg_default_templet'], '\../') . "/css/" . $file;
                    if (file_exists($tfile))
                    {
                        $file = "/public/" . trim($GLOBALS['cfg_default_templet'], '\../') . "/css/{$file}";
                    }
                    else
                    {
                        $tfile = DOCROOT . "/public/css/" . $file;
                        $file = "/public/css/{$file}";
                    }
                }
                else
                {
                    $tfile = DOCROOT . "/public/css/" . $file;
                    $file = "/public/css/{$file}";
                }
            }
            else
            {
                $tfile = DOCROOT . $file;
            }
            if (file_exists($tfile))
            {
                $filelist[] = $file;
            }
        }
        if (!empty($filelist))
        {
            //如果开启css合并,此项是默认开启的.
            if ($mincss)
            {
                $f = implode(',', $filelist);
                $cssUrl = URL::site('pub/css?file=' . $f);
                $out = '<link type="text/css" href="' . $cssUrl . '" rel="stylesheet"  />' . "\r\n";
            }
            else
            {
                foreach ($filelist as $css)
                {
                    $out .= HTML::style($css) . "\r\n";
                }
            }

        }
        return $out;
    }

    /**
     * @param $filelist  加载的css文件列表
     * @param bool $mincss 是否合并生成.
     * @param bool $default 是否从默认css目录加载,如果值为false,则直接从根目录加载相应文件,即DOCROOT+文件名
     * @return
     * @desc 加载css文件.
     */
    public static function css_plugin($filelist, $pinyin, $mincss = true, $default = true)
    {

        $filearr = explode(',', $filelist);
        $filelist = array();
        $out = '';
        $plugin_res_url = $GLOBALS['cfg_plugin_' . $pinyin . '_public_url'];

        foreach ($filearr as $file)
        {
            if ($default == true)
            {
                $tfile = BASEPATH . '/' . ltrim($plugin_res_url, '/\\') . "mobile/css/" . $file;
                $file = ltrim($plugin_res_url, '/\\') . "mobile/css/{$file}";
            }
            else
            {
                $tfile = BASEPATH . '/' . $file;
            }


            if (file_exists($tfile))
            {
                $filelist[] = $file;
            }
        }

        if (!empty($filelist))
        {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $full_host = $http_type . $GLOBALS['main_host'];

            foreach ($filelist as $css)
            {
                $out .= '<link type="text/css" href="' . $full_host . '/' . $css . '" rel="stylesheet"/>' . "\r\n";
            }
        }


        return $out;
    }

    //加载独立模块的js
    public static function js_plugin($filelist, $pinyin, $minjs = true, $default = true)
    {
        $filearr = explode(',', $filelist);
        $jsArr = array();
        $out = $v = '';
        $plugin_res_url = $GLOBALS['cfg_plugin_' . $pinyin . '_public_url'];
        foreach ($filearr as $file)
        {
            if ($default == true)
            {
                $tfile = BASEPATH . '/' . ltrim($plugin_res_url, '/\\') . "mobile/js/" . $file;

                $file = ltrim($plugin_res_url, '/\\') . 'mobile/js/' . $file;
            }
            else
            {
                $tfile = BASEPATH . '/' . $file;

            }
            if (file_exists($tfile))
            {
                // $out .= HTML::script($file);
                $jsArr[] = $file;
            }

        }
        if ($jsArr)
        {
            foreach ($jsArr as $js)
            {
                $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                $full_host = $http_type . $GLOBALS['main_host'];
                $out .= '<script type="text/javascript" src="' . $full_host . '/' . $js . '"></script>' . "\r\n";
            }
        }
        return $out;

    }

    /*
     * 获取配置文件值
     * */
    public static function get_config($group)
    {
        return Kohana::$config->load($group);
    }

    /*
     * 清空数组里的空值
     * */

    public static function remove_arr_empty($arr)
    {

        $newarr = array_diff($arr, array(null, 'null', '', ' '));
        return $newarr;

    }

    /*
     * 生成缩略图
     *
     * */
    public static function thumb($srcfile, $savepath, $w, $h)
    {
        Image::factory($srcfile)->resize($w, $h, Image::WIDTH)->save($savepath);
        return $savepath;
    }

    /*
     * 时间转换函数
     * */
    public static function mydate($format, $timest)
    {
        $addtime = 8 * 3600;
        if (empty($format))
        {
            $format = 'Y-m-d H:i:s';
        }
        return gmdate($format, $timest + $addtime);
    }


    /*
    * 获取文件扩展名
    * */
    public static function get_extension($file)
    {
        return end(explode('.', $file));
    }

    /*
     * 级联删除文件夹
     */
    public static function rrmdir($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir . "/" . $object) == "dir")
                    {
                        self::rrmdir($dir . "/" . $object);
                    }
                    else
                    {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }

    }

    /*
     * 保存文件
     * */
    public static function save_file($file, $content)
    {

        $fp = fopen($file, "wb");
        flock($fp, 3);
        //@flock($this->open,3);
        $result = fwrite($fp, $content);
        fclose($fp);
        return $result;
    }

    //检查一个串是否存在在某个串中
    public static function check_instr($str, $substr)
    {

        $tmparray = explode($substr, $str);
        if (count($tmparray) > 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * curl http访问
     * */
    public static function http($url, $method = 'get', $postfields = '')
    {

        $ci = curl_init();

        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);

        if ($method == 'POST')
        {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if ($postfields != '')
            {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
            }
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;

    }

    /*
     * 对象转数组
     * */

    public static function object_to_array($array)
    {
        if (is_object($array))
        {
            $array = (array)$array;
        }
        if (is_array($array))
        {
            foreach ($array as $key => $value)
            {
                $array[$key] = self::object_to_array($value);
            }
        }
        return $array;
    }

    /**
     *  获取拼音信息
     *
     * @access    public
     * @param     string $str 字符串
     * @param     int $ishead 是否为首字母
     * @param     int $isclose 解析后是否释放资源
     * @return    string
     */
    public static function get_pinyin($str, $ishead = 0, $isclose = 1)
    {
        $str = iconv('utf-8', 'gbk//ignore', $str);
        $restr = '';
        $str = trim($str);
        $slen = strlen($str);
        if ($slen < 2)
        {
            return $str;
        }

        if (count(self::$pinyin) == 0)
        {
            $fp = fopen(APPPATH . '/vendor/pinyin/pinyin.dat', 'r');
            while (!feof($fp))
            {
                $line = trim(fgets($fp));
                self::$pinyin[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
            }
            fclose($fp);
        }
        for ($i = 0; $i < $slen; $i++)
        {
            if (ord($str[$i]) > 0x80)
            {
                $c = $str[$i] . $str[$i + 1];
                $i++;
                if (isset(self::$pinyin[$c]))
                {
                    if ($ishead == 0)
                    {
                        $restr .= self::$pinyin[$c];
                    }
                    else
                    {
                        $restr .= self::$pinyin[$c][0];
                    }
                }
                else
                {
                    $restr .= "_";
                }
            }
            else if (preg_match("/[a-z0-9]/i", $str[$i]))
            {
                $restr .= $str[$i];
            }
            else
            {
                $restr .= "_";
            }
        }
        if ($isclose == 0)
        {
            unset(self::$pinyin);
        }
        $sheng = "/.*sheng.*/";
        $shi = "/.*shi.*/";
        $qu = "/.*qu.*/";
        if (preg_match($sheng, $restr, $matches))
        {
            $restr = str_replace('sheng', '', $matches[0]);
        }
        if (preg_match($shi, $restr, $matches))
        {
            $restr = str_replace('shi', '', $matches[0]);
        }
        if (preg_match($qu, $restr, $matches))
        {
            $restr = str_replace('qu', '', $matches[0]);
        }
        return $restr;
    }

    /*
     * decode加密/解密算法
     * */

    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;

        $key = md5($key ? $key : 'stourweb');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++)
        {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++)
        {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++)
        {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE')
        {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16))
            {
                return substr($result, 26);
            }
            else
            {
                return '';
            }
        }
        else
        {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }


    //表字段操作(添加)
    public static function add_field($table, $fieldname, $fieldtype, $isunique, $comment)
    {
        $fieldname = 'e_' . $fieldname;
        $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$fieldname}` {$fieldtype} NULL DEFAULT NULL COMMENT '$comment'";
        $sql .= $isunique == 1 ? ",ADD unique('{$fieldname}');" : '';
        return DB::query(1, $sql)->execute();
    }

    /*
     * 表字段操作(删除)
     * */
    public static function del_field($table, $fieldname)
    {
        $sql = "ALTER TABLE `{$table}` DROP COLUMN `{$fieldname}`";
        DB::query(1, $sql)->execute();
    }

    //获取时间范围
    /*
     * 1:今日
     * 2:昨日
     * 3:本周
     * 4:上周
     * 5:本月
     * 6:上月
     * */
    public function get_timerange($type)
    {
        switch ($type)
        {
            case 1:
                $starttime = strtotime(date('Y-m-d 00:00:00'));
                $endtime = strtotime(date('Y-m-d 23:59:59'));
                break;
            case 2:
                $starttime = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
                $endtime = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
                break;
            case 3:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));;
                $endtime = time();
                break;
            case 4:
                $starttime = strtotime(date('Y-m-d 00:00:00', strtotime('last Sunday')));
                $endtime = strtotime(date('Y-m-d H:i:s', strtotime('last Sunday') + 7 * 24 * 3600 - 1));
                break;
            case 5:
                $starttime = strtotime(date('Y-m-01 00:00:00', time()));
                $endtime = time();
                break;
            case 6:
                $starttime = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                $endtime = strtotime(date('Y-m-31 23:59:00', strtotime('-1 month')));
                break;


        }
        $out = array($starttime, $endtime);
        return $out;

    }

    /*
     * xml转数组
     * */
    public static function xml_to_array($xml)
    {
        $array = (array)(simplexml_load_string($xml));
        foreach ($array as $key => $item)
        {
            $array[$key] = self::struct_to_array((array)$item);
        }
        return $array;
    }

    /*
     * 结构转数组
     * */
    public static function struct_to_array($item)
    {
        if (!is_string($item))
        {
            $item = (array)$item;
            foreach ($item as $key => $val)
            {
                $item[$key] = self::struct_to_array($val);
            }
        }
        return $item;
    }

    /*
     * 去除xss全局函数,所有输入参数都要调用这个参数.
     * */
    public static function remove_xss($param, $reject_check = true)
    {
        return St_Filter::remove_xss($param, $reject_check);
    }

    /*
   * 注入检测
   */
    public static function reject_check($param)
    {
        $arr = explode(' ', $param);
        if (count($arr) > 1)
        {
            foreach ($arr as $ar)
            {
                $check = preg_match('/select|insert|update|delete|and|or|\'|\\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i', $ar);
                if ($check)
                {
                    exit($ar);
                }
            }

        }
    }

    // 写入系统缓存
    public static function cache_config()
    {
        $file = CACHE_DIR . 'phone/config.php';
        //缓存文件不存在
        if (!file_exists($file))
        {
            $data = Model_Sysconfig::config();
            $config = array();
            foreach ($data as $v)
            {
                $config[$v['varname']] = trim($v['value']);
            }
            //如果图片域名没有设置,则取PC域名作为图片域名
            if (!$config['cfg_m_img_url'])
            {
                $config['cfg_m_img_url'] = St_Functions::get_web_url(0);
            }
            $config['cfg_m_logo'] = preg_match('~^https?://~', $config['cfg_m_logo']) ? $config['cfg_m_logo'] : $config['cfg_m_img_url'] . $config['cfg_m_logo'];
            file_put_contents($file, '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');' . PHP_EOL . 'return ' . var_export($config, true) . ';');
        }
        $data = require_once($file);
        return $data;
    }

    /*
     * 生成站点列表
     * */
    public static function cache_web_list()
    {
        Model_Destinations::gen_web_list();
    }

    // 发送邮件
    public static function send_email($maillto, $title, $content)
    {
        require_once TOOLS_COMMON . 'email/emailservice.php';
        $status = EmailService::send_email($maillto, $title, $content);
        return $status;
    }

    //session 管理
    public static function session($k, $v = '')
    {
        $session = Session::instance();
        if (empty($v))
        {
            $session = is_null($v) ? $session->delete($k) : $session->get($k);
        }
        else
        {
            $session->set($k, $v);
        }
        return $session;
    }

    //提示信息
    public static function message($msg)
    {
        if (empty($msg['jumpUrl']))
        {
            Request::current()->redirect('/');
        }

        $javascript = Common::js('jquery.min.js,layer/layer.m.js');
        echo <<<EOT
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>信息提示</title>
            {$javascript}
        </head>
        <body>  
        </body>
        <script type="text/javascript">
         layer.open({
                content: '{$msg["message"]}',
                time: 2,
                end:function(){
                   window.location.href='{$msg["jumpUrl"]}';
                }
            });
        </script>
        </html>
EOT;

        exit;
    }

    /**
     * @return string
     * @return 无图设置
     */
    public static function nopic()
    {
        return $GLOBALS['cfg_df_img'] ? $GLOBALS['cfg_df_img'] : '/uploads/nopicture.jpg';
    }

    public static function member_nopic()
    {
        return '/uploads/member_nopic.png';
    }

    public static function menu_nopic()
    {
        return '/uploads/menu_no_ico.png';
    }

    /*
     * 缩略图url
     */
    public static function img($src, $width = '', $height = '', $watermark = false)
    {
        if (!$src)
        {
            $src = self::nopic();
        }

        return St_Functions::img($src, $width, $height, $watermark);
    }

    /**
     * @function 移动端详情页图片裁剪
     * @param $content
     * @param int $width
     * @param int $height
     * @param bool $watermark
     * @return mixed
     */
    public static function content_image_width($content, $width = 0, $height = 0, $watermark = true)
    {
        return St_Functions::content_image_width($content, $width, $height, $watermark);
    }

    /*
    * 获取某个配置值
    * */

    public static function get_sys_conf($field, $varname, $webid = 0)
    {
        $result = DB::query(Database::SELECT, "select $field from sline_sysconfig where varname='$varname' and webid=$webid")->execute()->as_array();
        return $result[0][$field];
    }

    public static function get_sys_para($varname)
    {
        return self::get_sys_conf('value', $varname, 0);
    }


    //分析当前域名,返回主域名

    /**
     * @return string
     * @desc 如www.lvyou.com 返回 lvyou.com
     */
    public static function get_domain()
    {
        $url = $GLOBALS['cfg_basehost'];

        $uarr = explode('.', $url);
        $k = 0;
        $out = '';
        foreach ($uarr as $value)
        {
            $out .= $k != 0 ? $value : '';
            $out .= '.';
            $k++;
        }
        $out = substr($out, 0, strlen($out) - 1);
        return $out;

    }

    /*
    * 获取主站prefix
    * */
    public static function get_main_prefix()
    {

        $sql = "SELECT webprefix FROM `sline_weblist` WHERE webid=0";
        $row = DB::query(1, $sql)->execute()->as_array();
        return $row[0]['webprefix'] ? $row[0]['webprefix'] : 'www';
    }

    /**
     * @param $webid
     * @return string
     * @desc 根据webid获取产品的url绝对地址
     */
    public static function get_web_url($webid)
    {
        //子站后缀
        $suffix = '';
        $domain = self::get_domain();//顶级域名
        //如果webid不为0,则读取站点的信息
        if ($webid != 0)
        {
            $prefix = ORM::factory('destinations', $webid)->get('webprefix');
            $suffix = "/{$prefix}";
        }
        else
        {
            $prefix = self::get_main_prefix();
        }
        $url = isset($GLOBALS['cfg_phone_cmspath']{1}) ? St_Functions::get_http_prefix() . $prefix . $domain . $GLOBALS['cfg_phone_cmspath'] : $GLOBALS['cfg_basehost'] . $suffix;
        return $url;
    }

    public static function cutstr_html($string, $sublen)
    {

        $string = strip_tags($string);

        $string = preg_replace('/\n/is', '', $string);

        $string = preg_replace('/ |　/is', '', $string);

        $string = preg_replace('/&nbsp;/is', '', $string);

        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $t_string);

        if (count($t_string[0]) - 0 > $sublen)
        {
            $string = join('', array_slice($t_string[0], 0, $sublen)) . "…";
        }

        else
        {
            $string = join('', array_slice($t_string[0], 0, $sublen));
        }

        return $string;

    }

    public static function GetIP()
    {
        static $realip = NULL;
        if ($realip !== NULL)
        {
            return $realip;
        }
        if (isset($_SERVER))
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第x个非unknown的有效IP字符? */
                foreach ($arr as $ip)
                {
                    $ip = trim($ip);
                    if ($ip != 'unknown')
                    {
                        $realip = $ip;
                        break;
                    }
                }
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP']))
            {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                if (isset($_SERVER['REMOTE_ADDR']))
                {
                    $realip = $_SERVER['REMOTE_ADDR'];
                }
                else
                {
                    $realip = '0.0.0.0';
                }
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP'))
            {
                $realip = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        return $realip;
    }

    //跳转404页面
    public static function head404()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        //header("Location: ".$GLOBALS['cfg_basehost']."/404.php");
        echo "<script>window.location.href='/404.php'</script>";
        exit;

    }

//跳转301
    public static function head301($url)
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
        exit();

    }

    /**
     * 主站域名
     * @return string
     */
    static function get_main_host()
    {
        $host = '';
        $sql = "select weburl from sline_weblist where webid=0";
        $arr = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($arr))
        {
            $host = $arr['weburl'];
        }
        return $host;
    }

    /**
     * COOKIE 域名
     * @return string
     */
    static function cookie_domain()
    {
        $host = $_SERVER['HTTP_HOST'];
        $sql = "select * from sline_weblist where webid=0";
        $arr = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($arr))
        {
            $host = str_replace($arr['webprefix'] . '.', '', parse_url($arr['weburl'], PHP_URL_HOST));
        }
        return $host;
    }

    /**
     * 支付表单数据提交
     * @param $data
     * @return string
     */
    static function payment_from($data)
    {
        if (!is_array($data) && empty($data) && !isset($data['ordersn']))
        {
            return false;
        }
        $url = self::get_main_host() . '/payment/';
        $html = "<form action='{$url}' style='display:none;' method='post' id='payment'>";
        foreach ($data as $name => $v)
        {
            $html .= "<input type='text' name='{$name}' value='{$v}'>";
        }
        $html .= '</form>';
        $html .= "<script>document.forms['payment'].submit();</script>";
        return $html;
    }

    /**
     * @param $text
     * 生成二维码
     */
    public static function qrcode($text)
    {
        if (!class_exists('QRcode'))
        {
            include Kohana::find_file('vendor', "phpqrcode/phpqrcode");
        }
        $errorCorrectionLevel = "L";
        $matrixPointSize = 8;
        QRcode::png($text, false, $errorCorrectionLevel, $matrixPointSize);
    }

    /**
     * @return string
     * 获取当前网址
     */
    public static function get_current_url()
    {
        return St_Functions::get_http_prefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 预处理
     */
    public static function before_header($controller = null, $action = null)
    {
        self::wx_client_login($controller, $action);
    }

    /**
     * 微信客户端登陆
     */
    public static function wx_client_login($controller = null, $action = null)
    {
        $file = BASEPATH . '/plugins/login_wx_client/index.php';
        $current_url = self::get_current_url();
        $fast_login = !is_null($_SERVER['HTTP_REFERER']);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false || !file_exists($file) || $fast_login)
        {
            return;
        }
        if (in_array($controller, array('login', 'register', 'pub', 'error', 'dz', 'thirdpay')))
        {
            return;
        }
        $is_login = self::session('member');
        if (empty($is_login))
        {
            $agent = rtrim(self::get_main_host(), '/') . '/phone/pub/thirdlogin/?type=wx_client&refer=' . urlencode($current_url);
            header('Location:' . $agent);
            exit;
        }
    }

    /**
     * 会员等级
     * @param $memberId
     * @param array $param
     * @return array|int|string
     */
    static function member_rank($memberId, $param = array())
    {
        $grade = array('current' => 1);
        //$member = DB::select('jifen')->from('member')->where("mid={$memberId}")->execute()->current();
        $member = DB::query(1, 'select sum(jifen) as jifen from sline_member_jifen_log where memberid=' . $memberId)->execute()->current();
        $rank = self::member_grade();
        foreach ($rank as $k => $v)
        {
            $grade['current'] = ++$k;
            if (!$member['jifen'] or $member['jifen'] >= $v['begin'] && $member['jifen'] <= $v['end'])
            {
                break;
            }
        }
        //组合返回数据
        if (isset($param['return']))
        {
            switch ($param['return'])
            {
                case 'current':
                    $data = 'Lv.' . $grade['current'];
                    break;
                default:
                    $data = array('grade' => $rank, 'jifen' => $member['jifen'], 'current' => $grade['current'], 'total' => count($rank));
            }
        }
        return $data;
    }

    /**
     * 将会员等级以升序保存
     * @return mixed
     */
    static function member_grade()
    {
        static $grade = null;
        if (is_null($grade))
        {
            $grade = DB::select()->from('member_grade')->order_by('begin', 'asc')->execute()->as_array();
        }
        return $grade;
    }

    /**
     * @param $type
     * @param $key
     * @param string $value
     * @return bool|mixed
     * 缓存设置与获取
     */
    public static function cache($type, $key, $value = '')
    {
        if (!$GLOBALS['cfg_cache_open'])
        {
            return false;
        }
        $cache_dir = CACHE_DIR . 'phone/html';
        if (!file_exists($cache_dir))
        {
            mkdir($cache_dir, 0777, true);
        }
        $cache = Cache::instance('default');
        //获取缓存
        if ($type == 'get')
        {
            return $cache->get($key, '');
        }
        //设置缓存
        else if ($type == 'set' && mb_stripos($value, '没有找到符合条件的产品') === false)
        {
            return $cache->set($key, $value, 3600);
        }

    }

    /**
     * @function 引用用户模板css
     * @param $cssfile
     * @param $version
     * @param string $tpl
     * @return string
     */
    public static function get_user_css($cssfile, $version, $tpl = '')
    {
        $filelist = explode(',', $cssfile);
        $css = '';

        $version = !empty($version) ? '?v=' . $version : '';
        if ($filelist)
        {
            foreach ($filelist as &$file)
            {
                $file = empty($tpl) ? '/phone/usertpl/' . $file : '/phone/usertpl/' . $tpl . '/' . $file;
            }
            $f = implode(',', $filelist);
            if ($GLOBALS['cfg_compress_open'])
            {
                $cssUrl = '/min/?f=' . $f;
                $css = '<link type="text/css" href="' . $cssUrl . '" rel="stylesheet"  />' . "\r\n";
            }
            else
            {
                foreach ($filelist as $c)
                {
                    $css .= "<link href=\"" . $c . $version .
                        "\" rel=\"stylesheet\" media=\"screen\" type=\"text/css\" />\r\n";
                }
            }
        }
        return $css;

    }

    /**
     * @function 调用用户模板css
     * @param $jsfile
     * @param $version
     * @param string $tpl
     * @return string
     */
    public static function get_user_js($jsfile, $version, $tpl = '')
    {
        $filelist = explode(',', $jsfile);
        $version = !empty($version) ? '?v=' . $version : '';
        $script = '';

        if ($filelist)
        {
            foreach ($filelist as &$file)
            {
                $file = empty($tpl) ? '/phone/usertpl/' . $file : '/phone/usertpl/' . $tpl . '/' . $file;
            }
            $f = implode(',', $filelist);
            if ($GLOBALS['cfg_compress_open'])
            {
                $jsUrl = '/min/?f=' . $f;
                $script = '<script type="text/javascript"src="' . $jsUrl . '"></script>' . "\r\n";
            }
            else
            {
                foreach ($filelist as $js)
                {
                    $script .= "<script type=\"text/javascript\" language=\"javascript\" src=\"" . $js . $version .
                        "\"></script>\r\n";
                }
            }
        }
        return $script;
    }


    /**
     * @function 引用php文件到模板
     * @param $phpfile 需要引入的php文件
     * @param string $tpl 模板名称
     */
    public static function get_user_func($phpfile, $tpl = '')
    {

        $filelist = explode(',', $phpfile);
        foreach ($filelist as $file)
        {

            $funcfile = empty($tpl) ? DOCROOT . 'usertpl/' . $file : DOCROOT . 'usertpl/' . $tpl . '/' . $file;

            if (file_exists($funcfile))
            {
                include_once($funcfile);
            }
        }

    }

    //判断是否是微信浏览器
    public static function is_weixin_browser()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)
        {
            return true;
        }
        return false;
    }


    /**
     *  * 求两个日期之间相差的天数
     *  * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     *  * @param string $day1
     *  * @param string $day2
     *  * @return number
     *  */
    public static function diff_between_twodays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2)
        {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return intval(($second1 - $second2) / 86400);
    }

    /**
     * @function 订单状态
     */
    public static function order_status()
    {
        $request=Request::initial();
        $booking = Cookie::get('__token__', null) ? 1 : 0;
        $redirect = $booking ? $_SERVER['HTTP_REFERER'] : Url::site('/member#&myOrder');
        $request->redirect(sprintf('/pub/order_status?booking=%d&_token=%s&redirect=%s', $booking, md5($redirect . Cookie::$salt), urlencode($redirect)));
        exit;
    }
}
