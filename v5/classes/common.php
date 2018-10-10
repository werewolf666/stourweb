<?php

/**
 * 公共静态类模块
 * User: Netman
 * Date: 15-09-12
 * Time: 下午14:06
 */
class Common
{

    public static $pinyins = array();

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

        require(DOCROOT . '/res/vendor/slineeditor/ueditor.php');

        $UEditor = new UEditor();
        $UEditor->basePath = $GLOBALS['cfg_cmspath'] . $GLOBALS['cfg_public_url'] . 'vendor/slineeditor/';
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
    public static function js($filelist, $minjs = true, $default = true)
    {
        $filearr = explode(',', $filelist);
        $jsArr = array();
        $out = $v = '';
        foreach ($filearr as $file)
        {
            if ($default == true)
            {
                $tfile = DOCROOT . $GLOBALS['cfg_public_url'] . "js/" . $file;

                $file = $GLOBALS['cfg_public_url'] . 'js/' . $file;
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
            if ($GLOBALS['cfg_compress_open'] && $minjs)
            {
                $f = implode(',', $jsArr);
                //$jsUrl = URL::site('pub/js?file=' . $f);
                $jsUrl = '/min/?f=' . $f;
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
    public static function css($filelist, $mincss = true, $default = true)
    {
        $filearr = explode(',', $filelist);
        $filelist = array();
        $out = '';
        foreach ($filearr as $file)
        {
            if ($default == true)
            {
                $tfile = DOCROOT . $GLOBALS['cfg_public_url'] . "css/" . $file;
                $file = $GLOBALS['cfg_public_url'] . "css/{$file}";
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
            if ($GLOBALS['cfg_compress_open'])
            {
                $f = implode(',', $filelist);
                $cssUrl = '/min/?f=' . $f;
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
     * 检测属性是否存在属性列表里.
     * */
    public static function check_in_attr($attrid, $id, $explode = '_')
    {
        $arr = explode($explode, $attrid);
        if (in_array($id, $arr))
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

        if (count(self::$pinyins) == 0)
        {
            $fp = fopen(APPPATH . '/vendor/pinyin/pinyin.dat', 'r');
            while (!feof($fp))
            {
                $line = trim(fgets($fp));
                self::$pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
            }
            fclose($fp);
        }
        for ($i = 0; $i < $slen; $i++)
        {
            if (ord($str[$i]) > 0x80)
            {
                $c = $str[$i] . $str[$i + 1];
                $i++;
                if (isset(self::$pinyins[$c]))
                {
                    if ($ishead == 0)
                    {
                        $restr .= self::$pinyins[$c];
                    }
                    else
                    {
                        $restr .= self::$pinyins[$c][0];
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
            unset(self::$pinyins);
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
     * $reject_check 是否需要注入检测
     * */
    public static function remove_xss($param, $reject_check = true)
    {
        return St_Filter::remove_xss($param, $reject_check);
    }

    // 写入系统缓存
    public static function cache_config($sys_prefix, $webid = 0)
    {
        $file = CACHE_DIR . 'v5/config.' . $sys_prefix . '.php';
        //缓存文件不存在
        if (!file_exists($file))
        {
            $data = Model_Sysconfig::config($webid);
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
            if ($webid > 0)
            {
                $default_config = array('cfg_logo', 'cfg_logourl', 'cfg_logotitle', 'cfg_gonggao', 'cfg_tongjicode', 'cfg_footer');
                foreach ($default_config as $_item)
                {
                    if ($config[$_item] === '')
                    {
                        unset($config[$_item]);
                    }
                }
                self::_main_config($config);
            }
            file_put_contents($file, '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');' . PHP_EOL . 'return ' . var_export($config, true) . ';');
        }
        $data = require_once($file);
        return $data;
    }

    /**
     * 子站点配置
     * @param $data
     */
    private static function _main_config(&$data)
    {
        $main = Model_Sysconfig::config(0);
        //子站只有一个页面效果
        $data['cfg_index_templet'] = '';
        foreach ($main as $v)
        {
            if (!isset($data[$v['varname']]))
            {
                $data[$v['varname']] = trim($v['value']);
            }
        }
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
            $session->delete($k);
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
        $javascript = Common::js('jquery.min.js,layer/layer.js', 0);

        $ico = $msg['status'] ? "icon:6," : "icon:5,";

        echo <<<EOT
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>信息提示</title>
          {$javascript}
        </head>
        <body>
          <span style="display:none">{$msg["msg"]}</span>
        </body>
        <script type="text/javascript">
        layer.msg(
           '{$msg["msg"]}', {
            {$ico}
            time: 2000 //2秒关闭（如果不配置，默认是3秒）
        }, function(){
           window.location.href='{$msg['jumpUrl']}'
        });

        </script>
        </html>
EOT;
    }

    /**
     * @return string
     * @return 无图设置
     */
    public static function nopic()
    {
        return $GLOBALS['cfg_df_img'] ? $GLOBALS['cfg_df_img'] : '/uploads/nopicture.jpg';
    }

    /**
     * @function 根据尺寸生成图片地址
     * @param $src
     * @param string $width
     * @param string $height
     * @param bool $watermark
     * @return mixed|string
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
     * 裁切内容中包含的图片
     * @param $content
     * @param string $width
     * @param string $height
     * @return mixed
     */
    public static function img_content_cut($content, $width = '', $height = '')
    {
        $content = preg_replace('~(?:([\'"]/uploads/.*?)\.(jpg|jpeg|png|gif))~is', "\\1_{$width}x{$height}.\\2", $content);
        return $content;
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
    private static function get_domain()
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

        $domain = self::get_domain();//顶级域名
        //如果webid不为0,则读取站点的信息
        if ($webid != 0)
        {
            $prefix = ORM::factory('destinations', $webid)->get('webprefix');
        }
        else
        {
            $prefix = self::get_main_prefix();
        }
        $url = St_Functions::get_http_prefix() . $prefix . $domain;
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

    public static function get_ip()
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

        //header("Location: ".$GLOBALS['cfg_basehost']."/404.php");
        $url = $GLOBALS['cfg_basehost'] . '/error/404';
        echo "<script>window.location.href='" . $url . "'</script>";
        exit;

    }

    //跳转301
    public static function head301($url)
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
        exit();

    }

    /*
     * 数字转大写
     * */
    public static function daxie($number)
    {
        $number = substr($number, 0, 2);
        $arr = array("零", "一", "二", "三", "四", "五", "六", "七", "八", "九");
        if (strlen($number) == 1)
        {
            $result = $arr[$number];
        }
        else
        {
            if ($number == 10)
            {
                $result = "十";
            }
            else
            {
                if ($number < 20)
                {
                    $result = "十";
                }
                else
                {
                    $result = $arr[substr($number, 0, 1)] . "十";
                }
                if (substr($number, 1, 1) != "0")
                {
                    $result .= $arr[substr($number, 1, 1)];
                }
            }
        }
        return $result;
    }

    /**
     * @param $str
     * @return string
     * @desc 解密js通过escape的文字
     */
    public static function js_unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++)
        {
            if ($str[$i] == '%' && $str[$i + 1] == 'u')
            {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                {
                    $ret .= chr($val);
                }
                else if ($val < 0x800)
                {
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                }
                else
                {
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                }
                $i += 5;
            }
            else if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            }
            else
            {
                $ret .= $str[$i];
            }
        }
        return $ret;
    }

    /*
     * 日历显示
     */
    public static function calender($year = '', $month = '', $priceArr = NULL, $suitid, $contain = '')
    {

        date_default_timezone_set('Asia/Shanghai');
        $year = abs(intval($year));
        $month = abs(intval($month));
        $tmonth = $month < 10 ? "0" . $month : $month;
        $defaultYM = $year . '-' . $tmonth;
        $nowDate = new DateTime();
        if ($year <= 0)
        {
            $year = $nowDate->format('Y');
        }
        if ($month <= 0 or $month > 12)
        {
            $month = $nowDate->format('m');
        }
        //上一年
        $prevYear = $year - 1;
        //上一月
        $mpYear = $year;
        $preMonth = $month - 1;
        if ($preMonth <= 0)
        {
            $preMonth = 12;
            $mpYear = $prevYear;
        }
        $preMonth = $preMonth < 10 ? '0' . $preMonth : $preMonth;
        //下一年
        $nextYear = $year + 1;
        //下一月
        $mnYear = $year;
        $nextMonth = $month + 1;
        if ($nextMonth > 12)
        {
            $nextMonth = 1;
            $mnYear = $nextYear;
        }
        $nextMonth = $nextMonth < 10 ? '0' . $nextMonth : $nextMonth;
        //日历头
        $html = '<div id="calendar_tab">
<table width="100%" border="1" style="border-collapse: collapse;">

  <tr align="center" >
    <td class="top_title"><a id="premonth" data-year="' . $mpYear . '" class="prevmonth" data-suitid="' . $suitid . '"  data-month="' . $preMonth . '" href="javascript:;" data-contain="' . $contain . '">上一月</a></td>
    <td colspan="3" class="top_title" style="height:50px;">' . $year . '年' . $month . '月</td>
    <td class="top_title"><a id="nextmonth"  data-year="' . $mnYear . '" class="nextmonth" data-suitid="' . $suitid . '" data-month="' . $nextMonth . '" href="javascript:;" data-contain="' . $contain . '">下一月</a></td>

  </tr>
  <tr>
  	<td colspan="5">
		<table width="100%" border="1" >
			<tr align="center">
				<td style="height:25px;">一</td>
				<td style="height:25px;">二</td>
				<td style="height:25px;">三</td>
				<td style="height:25px;">四</td>
				<td style="height:25px;">五</td>
				<td style="height:25px;">六</td>
				<td style="height:25px;">日</td>
			</tr>
';

        $currentDay = $nowDate->format('Y-m-j');

        //当月最后一天
        $creatDate = new DateTime("$year-$nextMonth-0");
        $lastday = $creatDate->format('j');
        $creatDate = NULL;

        //循环输出天数
        $day = 1;
        $line = '';
        $prev_day_enable_select = false;
        while ($day <= $lastday)
        {

            $month_str = $month < 10 ? '0' . $month : $month;
            $day_str = $day < 10 ? '0' . $day : $day;
            $cday = $year . '-' . $month_str . '-' . $day_str;
            //当前星期几
            $creatDate = new DateTime("$year-$month-$day");
            $nowWeek = $creatDate->format('N');
            $creatDate = NULL;

            if ($day == 1)
            {
                $line = '<tr align="center">';
                $line .= str_repeat('<td>&nbsp;</td>', $nowWeek - 1);
            }
            if ($cday == $currentDay)
            {
                $style = 'style="font-size:16px; font-family:微软雅黑,Arial,Helvetica,sans-serif;color:#FF6600;line-height:22px;"';
            }
            else
            {
                $style = 'style=" font-size:16px; font-family:微软雅黑,Arial,Helvetica,sans-serif;line-height:22px;"';
            }
            //判断当前的日期是否小于今天
            $defaultmktime = mktime(1, 1, 1, $month, $day, $year);

            $currentmktime = mktime(1, 1, 1, date("m"), date("j"), date("Y"));
            //echo '<hr>';
            $tday = ($day < 10) ? '0' . $day : $day;
            $cdaydate = $defaultYM . '-' . $tday;
            $cdayme = strtotime($cdaydate);
            //单价
            $dayPrice = $priceArr[$cdayme]['price'];

            $dayPrice = empty($dayPrice) ? $priceArr[$cdayme]['child_price'] : $dayPrice;

            $dayPrice = empty($dayPrice) ? $priceArr[$cdayme]['old_price'] : $dayPrice;


            //库存
            $priceArr[$cdayme]['number'] = $priceArr[$cdayme]['number'] < -1 ? 0 : $priceArr[$cdayme]['number'];
            $number = $priceArr[$cdayme]['number'] != -1 ? $priceArr[$cdayme]['number'] : '充足';
            $numstr = '<b style="font-size: 1rem;font-weight:normal">余位&nbsp;' . $number . '</b>';

            //定义单元格样式，高，宽
            $tdStyle = "height='80'";
            //判断当前的日期是否小于今天
            $tdcontent = '<span class="num">' . $day . '</span>';
            if ($defaultmktime >= $currentmktime)
            {
                if ($dayPrice || $contain == "leavedate")//当选择结束日期时，可以选择报价日期后一天没有报价的日期
                {

                    if ($number !== 0 && $dayPrice)
                    {
                        $dayPriceStrs = Currency_Tool::symbol() . $dayPrice . '<br>';
                        $tdcontent .= '<b class="price">' . $dayPriceStrs . '</b>' . $numstr;

                        $onclick = 'onclick="choose_day(\'' . $cday . '\',\'' . $contain . '\')"';
                        $prev_day_enable_select = true;
                    }
                    else
                    {
                        $tdcontent .= '<b class="no_yd"></b>' . '<b class="roombalance_b"></b>';
                        if ($contain == "leavedate" && $prev_day_enable_select)
                        {
                            $onclick = 'onclick="choose_day(\'' . $cday . '\',\'' . $contain . '\')"';
                        }
                        else
                        {
                            $onclick = '';
                        }
                        $prev_day_enable_select = false;

                    }

                }
                else
                {
                    $dayPriceStrs = '';
                    $tdcontent .= '<b class="no_yd"></b>' . '<b class="roombalance_b"></b>';
                    $onclick = '';
                    $numberinfo = "<span class='kucun'></span>";

                }
                if ($onclick == '')
                {

                    $line .= "<td $tdStyle class='nouseable' >" . $tdcontent . "</td>";
                }
                else
                {
                    $line .= "<td $tdStyle $onclick style='cursor:pointer;' class='useable' >" . $tdcontent . "</td>";
                }
            }
            else
            {
                $dayPriceStrs = '&nbsp;&nbsp;';
                $tdcontent .= '<b class="no_yd"></b>';
                $line .= "<td $tdStyle class='nouseable' >" . $tdcontent . "</td>";
            }


            //$line .= "<td $style>$day <div>不可订</div></td>";

            //一周结束
            if ($nowWeek == 7)
            {
                $line .= '</tr>';
                $html .= $line;
                $line = '<tr align="center">';
            }

            //全月结束
            if ($day == $lastday)
            {
                if ($nowWeek != 7)
                {
                    $line .= str_repeat('<td>&nbsp;</td>', 7 - $nowWeek);
                }
                $line .= '</tr>';
                $html .= $line;

                break;
            }

            $day++;
        }

        $html .= '
		</table>
	</td>
  </tr>
</table>
</div>
';
        return $html;

    }

    //生成随机数
    public static function get_rand_code($num)
    {
        $out = '';
        for ($i = 1; $i <= $num; $i++)
        {
            $out .= mt_rand(0, 9);
        }
        self::session('msgcode', $out);
        return $out;

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
     * @function 调用用户模板css
     * @param $jsfile
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
                $file = empty($tpl) ? '/usertpl/' . $file : '/usertpl/' . $tpl . '/' . $file;
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
     * @function 引用用户模板css
     * @param $cssfile
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
                $file = empty($tpl) ? '/usertpl/' . $file : '/usertpl/' . $tpl . '/' . $file;
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
    public static function get_user_func($phpfile, $tpl)
    {
        $filelist = explode(',', $phpfile);
        foreach ($filelist as $file)
        {
            //$funcfile = BASEPATH . '/usertpl/' . $file;
            $funcfile = empty($tpl) ? BASEPATH . 'usertpl/' . $file : BASEPATH . 'usertpl/' . $tpl . '/' . $file;
            if (file_exists($funcfile))
            {
                include_once($funcfile);
            }
        }

    }

    /**
     * @return string
     * 会员无图
     */
    public static function member_nopic()
    {
        return '/uploads/member_nopic.png';
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
            return;
        }
        $cache_dir = CACHE_DIR . 'v5/html';
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
     * @return string
     * 获取当前网址
     */
    public static function get_current_url()
    {
        return St_Functions::get_http_prefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * COOKIE 域名
     * @return string
     */
    public static function cookie_domain()
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
     * 判断当前浏览器是否是手机版本
     * @return bool
     */
    public static function is_mobile()
    {
        if (strpos($_SERVER['REQUEST_URI'], '/plugins') === 0 || strpos($_SERVER['REQUEST_URI'], '\plugins') === 0)
        {
            return false;
        }
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = Array("240x320", "acer", "acoon", "acs-", "abacho", "ahong", "airness", "alcatel", "amoi", "android", "anywhereyougo.com", "applewebkit/525", "applewebkit/532", "asus", "audio", "au-mic", "avantogo", "becker", "benq", "bilbo", "bird", "blackberry", "blazer", "bleu", "cdm-", "compal", "coolpad", "danger", "dbtel", "dopod", "elaine", "eric", "etouch", "fly ", "fly_", "fly-", "go.web", "goodaccess", "gradiente", "grundig", "haier", "hedy", "hitachi", "htc", "huawei", "hutchison", "inno", "ipad", "ipaq", "ipod", "jbrowser", "kddi", "kgt", "kwc", "lenovo", "lg ", "lg2", "lg3", "lg4", "lg5", "lg7", "lg8", "lg9", "lg-", "lge-", "lge9", "longcos", "maemo", "mercator", "meridian", "micromax", "midp", "mini", "mitsu", "mmm", "mmp", "mobi", "mot-", "moto", "nec-", "netfront", "newgen", "nexian", "nf-browser", "nintendo", "nitro", "nokia", "nook", "novarra", "obigo", "palm", "panasonic", "pantech", "philips", "phone", "pg-", "playstation", "pocket", "pt-", "qc-", "qtek", "rover", "sagem", "sama", "samu", "sanyo", "samsung", "sch-", "scooter", "sec-", "sendo", "sgh-", "sharp", "siemens", "sie-", "softbank", "sony", "spice", "sprint", "spv", "symbian", "tablet", "talkabout", "tcl-", "teleca", "telit", "tianyu", "tim-", "toshiba", "tsm", "up.browser", "utec", "utstar", "verykool", "virgin", "vk-", "voda", "voxtel", "vx", "wap", "wellco", "wig browser", "wii", "windows ce", "wireless", "xda", "xde", "zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device)
        {
            if (stristr($user_agent, $device))
            {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
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
            $host = rtrim($arr['weburl'], '/');
        }
        return $host;
    }

    /**
     * 会员等级
     * @param $memberId
     * @param array $param
     * @return array|int|string
     */
    static function member_rank($memberId, $param = array())
    {
        $rank = self::member_grade();
        if (!empty($memberId))
        {
            $k = 0;
            //$member = DB::select('jifen')->from('member')->where("mid='{$memberId}'")->execute()->current();
            $member = DB::query(1, 'select sum(jifen) as jifen from sline_member_jifen_log where memberid=' . $memberId)->execute()->current();
            $range = array();
            foreach ($rank as $k => $v)
            {
                $range[] = $v['begin'];
            }
            $rangeLevel = count($range);
            if ($member['jifen'] < $range[0])
            {
                $k = 0;
            }
            else if ($member['jifen'] > $range[$rangeLevel - 1])
            {
                $k = $rangeLevel - 1;
            }
            else
            {
                foreach ($range as $k => $v)
                {
                    if ($member['jifen'] < $v)
                    {
                        --$k;
                        break;
                    }
                }
            }
            $grade = $rank[$k];
            $grade['current'] = ++$k;
        }
        else
        {
            $grade_id = $param['vr_grade'];
            foreach ($rank as $key => $g)
            {
                if ($grade_id == $g['id'])
                {
                    $grade = $g;
                    $grade['current'] = $key + 1;
                    break;
                }
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
                case 'rankname':
                    $data = $grade['name'];
                    break;
                default:
                    $data = array('grade' => $rank, 'jifen' => $member['jifen'], 'current' => $grade['current'], 'range' => $range, 'total' => count($rank));
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
     * 写入订单检测
     * @param array $params
     * @return bool
     * @throws Kohana_Exception
     */
    static function before_order_check($params = array())
    {
        $bool = false;
        $table = DB::select()->from('model')->where('pinyin', '=', $params['model'])->execute()->current();
        if ($table['id'])
        {
            if ($table['issystem'])
            {
                switch ($table['pinyin'])
                {
                    case 'hotel':
                        $data_obj = DB::select()->from('hotel_room_price')->where('hotelid', '=', $params['productid'])->and_where('suitid', '=', $params['suitid'])->and_where('day', '=', $params['day']);
                        break;
                    case 'spot':
                        $data_obj = DB::select()->from('spot_ticket_price')->where('spotid', '=', $params['productid'])->and_where('ticketid', '=', $params['suitid'])->and_where('day', '=', $params['day']);
                        break;
                    case 'tuan':
                        $data_obj = DB::select()->from('tuan')->where('id', '=', $params['productid'])->and_where('starttime', '<=', $params['day'])->and_where('endtime', '>=', $params['day']);
                        break;
                    case 'visa':
                        $data_obj = DB::select()->from('visa')->where('id', '=', $params['productid']);
                        break;
                    default:
                        $data_obj = DB::select()->from("{$table['pinyin']}_suit_price")->where("{$table['pinyin']}id", '=', $params['productid'])->and_where('suitid', '=', $params['suitid'])->and_where('day', '=', $params['day']);
                }
            }
            else
            {
                $data_obj = DB::select()->from('model_suit_price')->where('productid', '=', $params['productid'])->and_where('suitid', '=', $params['suitid'])->and_where('day', '=', $params['day']);
            }
        }
        else
        {
            throw new Kohana_Exception('在sline_model中未找到对应的model_id');
        }
        $result = $data_obj->execute()->current();
        if ($result)
        {
            $bool = $result;
        }
        return $bool;
    }

    /**
     * @param $str
     * @return mixed
     * 过滤前台用户提交的富文本
     */
    static function text_filter($str)
    {
        //$str=preg_replace("/\s+/", " ", $str); //过滤多余回车
        $str = preg_replace("/<[ ]+/si", "<", $str); //过滤<__("<"号后面带空格)
        $str = preg_replace("/<\!–.*?–>/si", "", $str); //注释
        $str = preg_replace("/<(\!.*?)>/si", "", $str); //过滤DOCTYPE
        $str = preg_replace("/<(\/?html.*?)>/si", "", $str); //过滤html标签
        $str = preg_replace("/<(\/?head.*?)>/si", "", $str); //过滤head标签
        $str = preg_replace("/<(\/?meta.*?)>/si", "", $str); //过滤meta标签
        $str = preg_replace("/<(\/?body.*?)>/si", "", $str); //过滤body标签
        $str = preg_replace("/<(\/?link.*?)>/si", "", $str); //过滤link标签
        $str = preg_replace("/<(\/?form.*?)>/si", "", $str); //过滤form标签
        $str = preg_replace("/cookie/si", "COOKIE", $str); //过滤COOKIE标签
        $str = preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si", "", $str); //过滤applet标签
        $str = preg_replace("/<(\/?applet.*?)>/si", "", $str); //过滤applet标签
        $str = preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si", "", $str); //过滤style标签
        $str = preg_replace("/<(\/?style.*?)>/si", "", $str); //过滤style标签
        $str = preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si", "", $str); //过滤title标签
        $str = preg_replace("/<(\/?title.*?)>/si", "", $str); //过滤title标签
        $str = preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si", "", $str); //过滤object标签
        $str = preg_replace("/<(\/?objec.*?)>/si", "", $str); //过滤object标签
        $str = preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si", "", $str); //过滤noframes标签
        $str = preg_replace("/<(\/?noframes.*?)>/si", "", $str); //过滤noframes标签
        $str = preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si", "", $str); //过滤frame标签
        $str = preg_replace("/<(\/?i?frame.*?)>/si", "", $str); //过滤frame标签
        $str = preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si", "", $str); //过滤script标签
        $str = preg_replace("/<(\/?script.*?)>/si", "", $str); //过滤script标签
        $str = preg_replace("/javascript/si", "Javascript", $str); //过滤script标签
        $str = preg_replace("/vbscript/si", "Vbscript", $str); //过滤script标签
        $str = preg_replace("/on([a-z]+)\s*=/si", "On\\1=", $str); //过滤script标签
        $str = preg_replace("/&#/si", "&＃", $str); //过滤script标签
        return $str;
    }

    /**
     * @param $filelist 文件名，多个文件用逗号隔开
     * @param $pinyin  模块目录，例如ship
     * @param bool $mincss
     * @param bool $default
     * @return string
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
                $tfile = DOCROOT . ltrim($plugin_res_url, '/\\') . "css/" . $file;
                $file = ltrim($plugin_res_url, '/\\') . "css/{$file}";
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
            if ($GLOBALS['cfg_compress_open'])
            {
                $f = implode(',', $filelist);
                $cssUrl = '/min/?f=' . $f;
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
                $tfile = DOCROOT . ltrim($plugin_res_url, '/\\') . "js/" . $file;

                $file = ltrim($plugin_res_url, '/\\') . 'js/' . $file;
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
            if ($GLOBALS['cfg_compress_open'] && $minjs)
            {
                $f = implode(',', $jsArr);
                //$jsUrl = URL::site('pub/js?file=' . $f);
                $jsUrl = '/min/?f=' . $f;
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
     * @function 载入皮肤
     * @return string
     */
    public static function load_skin()
    {
        $skin_path = BASEPATH . '/res/css/skin.css';
        if (file_exists($skin_path) && $GLOBALS['cfg_skin_id'])
        {
            return '<link type="text/css" href="/res/css/skin.css" rel="stylesheet"  />' . "\r\n";

        }
    }


    /**
     * @function 判断目的地是否属于子站，如果不是则跳转到主站
     * @param $destid 目的地id
     * @param $url 目标地址
     * @return bool
     */
    public static function check_is_sub_web($destid, $url)
    {

        $pdestinfo = DB::select('pid', 'iswebsite')->from('destinations')->where('id', '=', $destid)->execute()->current();
        $pid = $pdestinfo['pid'];
        $iswebsite = $pdestinfo['iswebsite'];
        if ($iswebsite == 1)
        {
            return true;
        }
        $destinfo = DB::select('id', 'iswebsite', 'pid')->from('destinations')->where('id', '=', $pid)->execute()->current();

        if ($destinfo['iswebsite'] == 1)
        {
            return true;
        }
        else
        {
            if ($destinfo['pid'] == 0)
            {
                $webid = $GLOBALS['sys_webid'];
                if ($webid != 0)
                {
                    $baseurl = Common::get_web_url(0);
                    $url = $baseurl . '/' . $url;
                    self::head301($url);
                }
            }
            else
            {
                return self::check_is_sub_web($destinfo['id'], $url);
            }
        }
    }

    /**
     * @function PC详情页图片裁剪
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


    /**
     * @function 获取header 导航栏的图标
     * @param $kind_id 图标id
     * @return mixed 图片src
     */
    static function get_nav_icon($kind_id)
    {
        if ($kind_id)
        {
            $row = DB::select()->from('nav_icon')->where('id', '=', $kind_id)->execute()->current();
            if ($row['litpic'])
            {
                return $row['litpic'];
            }
        }
    }
}
