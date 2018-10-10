<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 通用函数类
 * Class Functions
 */
class St_Functions
{
    /**
     * 单一查询
     * @param $table
     * @param int $where
     * @param string $fields
     * @param bool|false $getResult
     * @return bool
     */
    static function st_query($table, $where = 1, $fields = '*', $getResult = false)
    {
        $bool = false;
        $rs = DB::select($fields)->from($table)->where($where)->execute()->current();
        if (!empty($rs))
        {
            $bool = !$getResult ? true : $rs;
        }
        return $bool;
    }

    /**
     * 更新数据
     * @param $table
     * @param $update
     * @param $where
     * @return int|object
     */
    static function st_update($table, $update, $where)
    {
        $bool = 0;
        $row = DB::update($table)->set($update)->where($where)->execute();
        if ($row > 0)
        {
            $bool = $row;
        }
        return $bool;
    }

    /**
     * 添加数据
     * @param $table
     * @param $data
     * @return object
     */
    static function st_insert($table, $data)
    {
        $rs = DB::insert($table, array_keys($data))->values(array_values($data))->execute();
        return $rs;
    }

    /**
     * 获取主站地址
     * @return bool
     */
    static function get_main_host()
    {
        $rs = self::st_query('weblist', 'webid=0', 'weburl', true);
        if (isset($rs['weburl']))
        {
            $rs = $rs['weburl'];
        }
        return $rs;
    }

    /**
     * COOKIE 域名
     * @return string
     */
    static function cookie_domain()
    {
        $host = $_SERVER['HTTP_HOST'];
        $rs = self::st_query('weblist', 'webid=0', '*', true);
        if (!empty($rs))
        {
            $host = str_replace($rs['webprefix'] . '.', '', parse_url($rs['weburl'], PHP_URL_HOST));
        }
        return $host;
    }

    /**
     * 用户登陆后信息处理
     * @param $user
     * @param bool|false $isadd 是否是新增用户
     */
    static function write_login_info($user, $isadd = false)
    {
        $time = time();
        if ($isadd)
        {
            //增加积分记录
            $jifen = Model_Jifen::reward_jifen('sys_member_register', $user['mid']);
            if (!empty($jifen))
            {
                St_Product::add_jifen_log($user['mid'], "注册赠送积分{$jifen}", $jifen, 2);
            }
        }
        else
        {
            $jifen = Model_Jifen::reward_jifen('sys_member_login', $user['mid']);
            if ($jifen)
            {
                $content = "登陆获得{$jifen}积分";
                St_Product::add_jifen_log($user['mid'], $content, $jifen, 2);
            }
        }
        //积分日志

        //登陆信息
        $update['logintime'] = $time;
        $update['loginip'] = $user['loginip'] ? $user['loginip'] : self::get_ip();
        //如是获取不了IP,则直接赋值为空
        $update['loginip'] = $update['loginip'] ? $update['loginip'] : '';
        //$update['jifen'] = DB::expr("jifen + {$integral}");


        $userinfo = self::st_query('member', "mid={$user['mid']}", '*', true);
        $update['pwd'] = empty($userinfo['pwd']) ? md5(rand(0, 999)) : $userinfo['pwd'];
        self::st_update('member', $update, "mid={$user['mid']}");
        //写入session
        //pc_5.0 cookie
        Cookie::set('st_username', $user['nickname'], 7600);
        Cookie::set('st_userid', $user['mid'], 7600);
        Cookie::set('st_secret', self::authcode($user['mid'] . '||' . $update['pwd'], ''), 7600);
        //mobile_5.0
        $session = array('mid' => $userinfo['mid'], 'nickname' => $userinfo['nickname'], 'litpic' => $userinfo['litpic']);
        Session::instance()->set('member', $session);
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

    /**
     * 检测平台信息并写入cookie
     */
    static function st_platform()
    {

        $version = Cookie::get('_version', null);
        if (is_null($version))
        {
            $version = 'pc_5.0';
            Cookie::set('_version', $version);
        }
        return $version;
    }

    /**
     * 获取公用头、底部
     * @return string
     */
    static function head_bottom()
    {
        $version = strtolower(self::st_platform());
        switch ($version)
        {
            case 'pc_5.0':
                $url = Common::get_main_host() . '/pub/pay';
                break;
            case 'mobile_5.0':
                $url = Common::get_main_host() . '/phone/pub/commonhd';
                break;
        }
        Cookie::delete('_version');
        return file_get_contents($url);
    }

    /**
     * 第三方登陆账号绑定
     * @param $post
     * @return mixed|string
     */
    static function third_bind($post)
    {


        $supplyThird = array(
            'qq' => array(
                'alias' => 'qq',
                'name' => '腾讯QQ'
            ),
            'weixin' => array(
                'alias' => 'wx',
                'name' => '微信'
            ),
            'weibo' => array(
                'alias' => 'wb',
                'name' => '新浪微博'
            )
        );
        $rs['bool'] = false;
        $third = self::remove_xss($post['third']);
        $data['nickname'] = $third['nickname'];
        $data['loginip'] = $_SERVER['REMOTE_ADDR'];
        $data['litpic'] = $third['litpic'];
        $third_data['from'] = $third['from'];
        $third_data['openid'] = $third['openid'];
        $third_data['nickname'] = $third['nickname'];
        $member = isset($post['member']) ? self::remove_xss($post['member']) : null;
        if (!is_null($member) && isset($member['user']) && isset($member['pwd']))
        {
            //已有账号绑定
            $type = strpos($member['user'], '@') ? 'email' : 'mobile';
            $pwd = $member['pwd_coded'] ? $member['pwd'] : md5($member['pwd']);
            $memberInfo = Common::st_query('member', "{$type}='{$member['user']}' and pwd='{$pwd}'", '*', true);
            if (!empty($memberInfo))
            {
                $rs['bool'] = true;
                $data['mid'] = $memberInfo['mid'];
            }
            else
            {
                $rs['msg'] = '账号或密码不正确';
            }
            //是否绑定对应的第三方账号
            if ($rs['bool'])
            {
                $isThird = Common::st_query('member_third', "`from`='{$third['from']}' and mid={$data['mid']}", '*', true);
                if (!empty($isThird))
                {
                    $rs['bool'] = false;
                    $rs['msg'] = "已绑定到{$supplyThird[$third['from']]['name']}@{$isThird['nickname']}";
                }
            }
        }
        else
        {
            //查询openid是否存在
            $third['openid'] = preg_replace('~(.*?)\.[0-9]+$~', '$1', $third['openid']);
            $isThird = Common::st_query('member_third', "`from`='{$third['from']}' and openid='{$third['openid']}'", '*', true);
            if (empty($isThird))
            {
                $data['jointime'] = time();

                $result = self::st_insert('member', $data);
                if ($result[1] > 0)
                {
                    $rs['bool'] = true;
                    $data['mid'] = $result[0];
                    Session::instance()->set('third_mid', $result[0]);
                }
            }
            else
            {
                $rs['msg'] = '该账号已绑定';
            }
        }
        //登陆成功
        if ($rs['bool'])
        {
            $rs['bool'] = false;
            $third_data['mid'] = $data['mid'];
            $result = Common::st_insert('member_third', $third_data);
            if ($result[1] > 0)
            {
                $rs['bool'] = true;
                self::write_login_info($data, true);
            }
        }
        $rs['url'] = Cookie::get('_refer');
        return json_encode($rs);
    }

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
                        self::reject_check($value);
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
                self::reject_check($out);
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
        if (count($arr) > 1)
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

    /**
     * 登陆状态
     */
    public static function islogin()
    {
        //v5
        $bool = array();
        $version = Common::st_platform();
        if ($version == 'pc_5.0')
        {
            $mid = Cookie::get('st_userid');
            if (!empty($mid))
            {
                $bool = DB::select()->from('member')->where("mid={$mid}")->execute()->current();
            }
        }
        //v5 mobile
        if ($version == 'mobile_5.0')
        {
            $menber = Session::instance()->get('member');
            if ($menber)
            {
                $bool = DB::select()->from('member')->where("mid={$menber['mid']}")->execute()->current();
            }
        }
        return $bool;
    }

    /**
     * 登陆状态下,第三方绑定
     * @param $data
     */
    public static function third_login_bind($data)
    {
        $supplyThird = array(
            'qq' => array(
                'alias' => 'qq',
                'name' => '腾讯QQ'
            ),
            'weixin' => array(
                'alias' => 'wx',
                'name' => '微信'
            ),
            'weibo' => array(
                'alias' => 'wb',
                'name' => '新浪微博'
            )
        );
        $rs = DB::select()->from('member_third')->where("`from`= '{$data['from']}' and openid='{$data['openid']}'")->execute()->current();
        $refer = Cookie::get('_refer');
        if (empty($rs))
        {
            $third['mid'] = $data['mid'];
            $third['openid'] = preg_replace('~(.*?)\.[0-9]+$~', '$1', $data['openid']);
            $third['from'] = $data['from'];
            $third['nickname'] = $data['nickname'];
            Common::st_insert('member_third', $third);

            $jifen_label = '';
            $jifen_str = '';
            switch ($data['from'])
            {
                case 'qq':
                    $jifen_label = 'sys_member_bind_qq';
                    break;
                case 'weixin':
                    $jifen_label = 'sys_member_bind_weixin';
                    break;
                case 'weibo':
                    $jifen_label = 'sys_member_bind_sina_weibo';
                    break;
            }
            $jifen = Model_Jifen::reward_jifen($jifen_label, $data['mid']);
            if (!empty($jifen))
            {
                St_Product::add_jifen_log($data['mid'], "绑定{$supplyThird[$rs['from']]['name']}送积分{$jifen}", $jifen, 2);
            }
        }
        else
        {
            Session::instance()->set("thirdBindMsg", "已绑定到{$supplyThird[$rs['from']]['name']}@{$rs['nickname']}");
        }
        header("location:" . $refer);
        exit;
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
                $tfile = BASEPATH . $GLOBALS['cfg_res_url'] . "/js/" . $file;

                $file = $GLOBALS['cfg_res_url'] . '/js/' . $file;
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
                $tfile = BASEPATH . $GLOBALS['cfg_res_url'] . "/css/" . $file;
                $file = $GLOBALS['cfg_res_url'] . '/css/' . $file;
            }
            else
            {
                $tfile = BASEPATH . $file;


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

    /**
     * @param $varname
     * @param int $webid
     * @return mixed
     * 获取配置值.
     */
    public static function get_sys_para($varname, $webid = 0)
    {
        $result = self::st_query('sysconfig', "varname='$varname' AND webid=$webid", 'value', true);

        return $result['value'];
    }

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

        require(BASEPATH . '/res/vendor/slineeditor/ueditor.php');
        $UEditor = new UEditor();
        $UEditor->basePath = '/res/vendor/slineeditor/';
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
     * 获取扩展字段相关信息
     * @param $typeid
     * @param $extendinfo
     * @return array
     */
    public static function get_extend_content($typeid, $extendinfo)
    {
        $content_table = array(
            1 => 'line_content',
            2 => 'hotel_content',
            3 => 'car_content',
            4 => '',
            5 => 'spot_content',
            6 => '',
            8 => 'visa_content',
            13 => 'tuan_content',
            102 => 'farm_content'
        );
        $table = $content_table[$typeid];
        $isTongyong = false;
        if (empty($table))
        {
            $isTongyong = true;
            $table = 'model_content';
        }

        if ($isTongyong)
        {

            $content_field_list = DB::select("*")
                ->from($table)
                ->where("isopen=1 AND typeid='$typeid' AND columnname like 'e_%'")
                ->execute()
                ->as_array();


        }
        else
        {
            $content_field_list = DB::select()
                ->from($table)
                ->where("isopen=1 AND columnname like 'e_%'")
                ->execute()
                ->as_array();
        }

        $fields = array();
        foreach ($content_field_list as $v)
        {
            $fields[] = $v['columnname'];
        }

        $arr = DB::select()
            ->from('extend_field')
            ->where("typeid='$typeid' AND isopen=1")
            ->execute()
            ->as_array();
        $contentHtml = '';
        $extendHtml = '';
        foreach ($arr as $row)
        {
            $default = !empty($extendinfo[$row['fieldname']]) ? $extendinfo[$row['fieldname']] : '';
            if (in_array($row['fieldname'], $fields))
            {
                $contentHtml .= '<div id="content_' . $row['fieldname'] . '"  data-id="' . $row['fieldname'] . '" class="product-add-div content-hide"><div class="add-class">';
                $contentHtml .= '
                                <div>' . self::get_editor($row['fieldname'], $default, 700, 300, 'Sline', '0', '0') . '</div>
                            ';
                $contentHtml .= '</div></div>';
                continue;
            }
            if ($row['fieldtype'] == 'editor')
            {
                $head = '<div class="add-class">';
                $head .= '<dl>
                            <dt>' . $row['description'] . '：</dt>
                            <dd>
                                <div>' . self::get_editor($row['fieldname'], $default, 700, 300, 'Sline', '0', '0') . '</div>
                            </dd>
                        </dl>';
                $head .= '</div>';
                $extendHtml .= $head;
            }
            else if ($row['fieldtype'] == 'text')
            {
                $head = '<div class="add-class">';
                $head .= '<dl>
                            <dt>' . $row['description'] . '：</dt>
                            <dd>
                                <input type="text" name="' . $row['fieldname'] . '"  value="' . $default . '" class="set-text-xh text_300 mt-2">
                            </dd>
                        </dl>';
                $head .= '</div>';
                $extendHtml .= $head;
            }
        }
        return array('contentHtml' => $contentHtml, 'extendHtml' => $extendHtml);
    }

    /*
    * 获取扩展表
    * */
    public static function get_extend_table($typeid)
    {
        $row = ORM::factory('model', $typeid)->as_array();
        return 'sline_' . $row['addtable'];
    }

    /*
      //扩展字段信息保存
     * */
    public static function save_extend_data($typeid, $productid, $info)
    {

        $table = self::get_extend_table($typeid);
        $extendinfo = array();
        $columns = array('productid');
        $values = array($productid);
        foreach ($info as $k => $v)
        {
            if (preg_match('/^e_/', $k)) //找出所有扩展字段设置
            {
                $extendinfo[$k] = $v;
                $columns[] = $k;
                $values[] = $v;
            }
        }
        if (count($extendinfo) > 0)
        {
            $sql = "select count(*) as num from $table where productid='$productid'";
            $row = DB::query(1, $sql)->execute()->as_array();
            //optable
            $optable = str_replace('sline_', '', $table);
            if ($row[0]['num'] > 0)//已存在则更新
            {
                DB::update($optable)->set($extendinfo)->where("productid='$productid'")->execute();
            }
            else
            {
                DB::insert($optable)->columns($columns)->values($values)->execute();
            }
        }
    }

    /*
   * 获取aid
   * @param string table
   * @param int webid
   * @return lastaid
   * */
    public static function get_last_aid($tablename, $webid = 0)
    {
        $aid = 1;//初始值
        $sql = "select max(aid) as aid from {$tablename} where webid=$webid order by id desc";
        $row = DB::query(1, $sql)->execute()->as_array();
        if (is_array($row))
        {
            $aid = $row[0]['aid'] + 1;
        }
        return $aid;
    }

    /*
    * 清空数组里的空值
    * */
    public static function remove_empty($arr)
    {
        $newarr = array_diff($arr, array(null, 'null', '', ' '));
        return $newarr;
    }

    /*
   * 获取子站列表
   * return array
   * */
    public static function get_web_list()
    {
        $arr = DB::select_array(array('id', 'kindname', 'weburl', 'webroot', 'webprefix'))->from('destinations')->where("iswebsite=1 and isopen=1")->order_by("displayorder", 'asc')->execute()->as_array();
        foreach ($arr as $key => $value)
        {
            $arr[$key]['webid'] = $value['id'];
            $arr[$key]['webname'] = $value['kindname'];
        }

        return $arr;
    }

    /*
    * 根据,分隔的属性字符串获取相应的属性数组(修改页面用)
    */
    public static function get_selected_attr($typeid, $attr_str)
    {
        $productattr_arr = array(1 => 'line_attr', 2 => 'hotel_attr', 3 => 'car_attr', 4 => 'article_attr', 5 => 'spot_attr', 6 => 'photo_attr', 13 => 'tuan_attr');
        $attrtable = $typeid < 14 ? $productattr_arr[$typeid] : 'model_attr';
        $attrid_arr = explode(',', $attr_str);
        $attr_arr = array();
        foreach ($attrid_arr as $k => $v)
        {
            if ($typeid < 14)
            {
                $attr = ORM::factory($attrtable)->where("pid!=0 and id='$v'")->find();
            }
            else
            {
                $attr = ORM::factory($attrtable)->where("pid!=0 and id='$v' and typeid='$typeid'")->find();
            }
            if ($attr->id)
            {
                $attr_arr[] = $attr->as_array();
            }
        }
        return $attr_arr;
    }

    /*
     * 根据,分隔的字符串获取图标数组(修改页面用)
     * */
    public static function get_selected_icon($iconlist)
    {
        $iconid_arr = explode(',', $iconlist);
        $iconarr = array();
        foreach ($iconid_arr as $k => $v)
        {
            $icon = ORM::factory('icon', $v);
            if ($icon->id)
            {
                $iconarr[] = $icon->as_array();
            }
        }
        return $iconarr;
    }


    /*
     * 根据,分隔字符串获取上传的图片数组(修改页面用)
     * */
    public static function get_upload_picture($piclist)
    {
        $out = array();
        $arr = self::remove_empty(explode(',', $piclist));
        foreach ($arr as $row)
        {
            $picinfo = explode('||', $row);
            $out[] = array('litpic' => $picinfo[0], 'desc' => isset($picinfo[1]) ? $picinfo[1] : '');
        }
        return $out;
    }

    /*
    * 后台获取搜索词
    *
    */
    public static function get_keyword($keyword)
    {
        $keyword = str_replace(' ', '', trim($keyword));
        $num = substr($keyword, 1, strlen($keyword));
        $out = '';
        if (intval($num))
        {
            $out = intval($num);
        }
        else
        {
            $out = $keyword;
        }

        return $out;
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
            $row = self::st_query('destinations', "id=$webid", 'webprefix', true);
            $prefix = $row['webprefix'];
        }
        else
        {
            $prefix = self::get_main_prefix();
        }
        $url = St_Functions::get_http_prefix() . $prefix . $domain;
        return $url;


    }
    /*
     * 获取编号
     * */
    //获取编号,共6位,不足6位前面被0
    public static function get_series($id, $prefix)
    {
        $ar = array(
            '01' => 'A',
            '02' => 'B',
            '05' => 'C',
            '03' => 'D',
            '08' => 'E',
            '13' => 'G',
            '14' => 'H',
            '15' => 'I',
            '16' => 'J',
            '17' => 'K',
            '18' => 'L',
            '19' => 'M',
            '20' => 'N',
            '21' => 'O',
            '22' => 'P',
            '23' => 'Q',
            '24' => 'R',
            '25' => 'S',
            '26' => 'T'
        );
        $prefix = $ar[$prefix];
        $len = strlen($id);
        $needlen = 4 - $len;
        if ($needlen == 3)
        {
            $s = '000';
        }
        else if ($needlen == 2)
        {
            $s = '00';
        }
        else if ($needlen == 1)
        {
            $s = '0';
        }
        $out = $prefix . $s . "{$id}";
        return $out;
    }

    /**
     * @function Html内容截取
     * @param $string
     * @param $sublen
     * @return mixed|string
     */
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

    /**
     * @function 根据尺寸生成图片地址
     * @param $src
     * @param string $width
     * @param string $height
     * @param $watermark 水印
     * @return mixed|string
     */
    public static function img($src, $width = '', $height = '', $watermark)
    {
        if (stripos($src, 'res/images/') !== false)
        {
            return $src;
        }

        if (!preg_match('~_\d+x\d+\.(jpg|jpeg|png|gif)~', $src))
        {
            $thumb_param = array('is_make' => false, 'type' => 'default');

            if (preg_match('~^https?://~', $src))
            {
                //判断是否使用了图片域名的全称地址.
                if(strpos($src,$GLOBALS['cfg_m_img_url'])===false)
                {
                    if (strpos($src, $GLOBALS['cfg_m_main_url']) === 0)
                    {
                        $src = $GLOBALS['cfg_m_img_url'] . str_replace(rtrim($GLOBALS['cfg_m_main_url'], '/'), '', $src);
                    }
                    else if ($GLOBALS['cfg_remote_image_domain'] && strpos($src, $GLOBALS['cfg_remote_image_domain']) === 0)
                    {
                        $thumb_param['type'] = $GLOBALS['cfg_remote_image_type'];
                    }
                    else
                    {
                        return $src;
                    }
                }


            }
            else
            {
                $src = $GLOBALS['cfg_m_img_url'] . $src;
            }
            if ($width != '' || $height != '')
            {
                $width = (int)$width;
                $height = (int)$height;
                switch ($thumb_param['type'])
                {
                    case 'qiniu':
                        $src = $src . "?imageView2/5/w/{$width}/h/{$height}";
                        break;
                    case 'default':
                        $src = preg_replace('/(\.(?:jpg|jpeg|png|gif))$/is', ($watermark ? "_1" : '') . "_{$width}x{$height}$1", $src);
                        break;
                }
            }
        }
        return $src;
    }

    /**
     * @function 获取默认图片
     * @return string
     */
    public static function nopic()
    {
        return $GLOBALS['cfg_df_img'] ? $GLOBALS['cfg_df_img'] : $GLOBALS['cfg_public_url'] . 'images/nopicture.jpg';
    }

    /**
     * @function 获取客户端IP地址
     * @return string
     */
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

    /**
     * @function 生成时间范围
     * @param $type 1:今日 2:昨日 3:本周 4:上周 5:本月 6:上月
     * @return array
     */
    public static function back_get_time_range($type)
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
                $starttime = strtotime("-7 day");
                $endtime = time();
                break;
            case 4:
                $starttime = strtotime(date('Y-m-d 00:00:00', strtotime('last Sunday')));
                $endtime = strtotime(date('Y-m-d H:i:s', strtotime('last Sunday') + 7 * 24 * 3600 - 1));
                break;
            case 5:
                $starttime = strtotime("-30 day");
                $endtime = time();
                break;
            case 6:
                $starttime = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                $endtime = strtotime(date('Y-m-31 23:59:00', strtotime('-1 month')));
                break;
            case 7:
                $starttime = strtotime(date('Y-m-01 00:00:00'));
                $last_day = date("t");
                $endtime = strtotime(date("Y-m-{$last_day} 23:59:00"));
                break;
        }
        $out = array(
            $starttime,
            $endtime
        );
        return $out;
    }

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

    /**
     * @function 检测应用是否安装
     * @param $typeid
     * @return int
     *
     */
    public static function is_system_app_install($typeid)
    {
        $flag = 0;
        $pinyin = DB::select('pinyin')->from('model')->where('id', '=', $typeid)->execute()->get('pinyin');
        if ($pinyin)
        {
            $product_code = 'stourwebcms_app_system_' . $pinyin;
            $product = DB::select('status')->from('app')->where('productcode', '=', $product_code)->execute()->current();
            $flag = $product['status'] == 1 ? 1 : 0;
        }
        return $flag;

    }

    /**
     * @function 判断数据表是否存在
     * @param $tablename 表名
     * @return bool
     */
    public static function is_table_exist($tablename)
    {
        $result = DB::query(Database::SELECT, "show tables like '%{$tablename}'")->execute()->current();
        if (empty($result))
        {
            return false;
        }
        return true;

    }

    /**
     * @function 获取满意度
     * @param $typeid
     * @param $articleid
     * @param $satisfyscore
     * @param array $param
     * @return string
     */
    public static function get_satisfy($typeid, $articleid, $satisfyscore, $param = array())
    {
        $rtn = array('total' => 0);
        $level = array(0, 0, 0, 0, 0);
        $satisfyscore = intval($satisfyscore);
        $where = " WHERE typeid='{$typeid}' AND articleid='{$articleid}' AND isshow=1 AND level BETWEEN 1 AND 5 ";
        $sql = "SELECT level, count(1) as num FROM sline_comment {$where} GROUP BY level ORDER BY level ASC ";
        $arr = DB::query(1, $sql)->execute()->as_array();
        foreach ($arr as $v)
        {
            $rtn['total'] += $v['num'];
            $level[$v['level'] - 1] = $v['num'];
        }
        $rtn['wellnum'] = $level[3] + $level[4];
        $rtn['midnum'] = $level[1] + $level[2];
        $rtn['badnum'] = $level[0];
        //$number = ($level[0] * 20 + $level[1] * 40 + $level[2] * 60 + $level[3] * 80 + $level[4] * 100 + $satisfyscore) / ($rtn['total'] * 100 + $satisfyscore);
        /**
         * 总分100分,一星:20分,二星40分,三星60分,四星80分,五星100分
         * 满意度 = 用户分数/总评论数*100
         */
        $number = ($level[0] * 20 + $level[1] * 40 + $level[2] * 60 + $level[3] * 80 + $level[4] * 100) / ($rtn['total'] * 100);
        if (!isset($param['suffix']))
        {
            $param['suffix'] = '%';
        }
        //如果用户设置了满意度则直接取用户设置的
        if ($satisfyscore)
        {
            $number = $satisfyscore > 100 ? 100 : $satisfyscore;
            $rtn['satisfyscore'] = $number . $param['suffix'];
        }
        else
        {
            $rtn['satisfyscore'] = number_format($number, 2) * 100 . $param['suffix'];
        }

        if (isset($param['isAll']))
        {
            return $rtn;
        }
        return $rtn['satisfyscore'];
    }

    /**
     * @function 检测model是否存在
     * @param $typeid
     * @return int
     */
    public static function is_model_exist($typeid)
    {
        $model_id = DB::select('id')->from('model')->where('id', '=', $typeid)->execute()->get('id');
        return $model_id ? $model_id : 0;
    }

    public static function is_normal_app_install($pinyin)
    {
        $flag = 0;
        if ($pinyin)
        {
            $product_code = 'stourwebcms_app_' . $pinyin;
            $product = DB::select('status')->from('app')->where('productcode', '=', $product_code)->execute()->current();
            $flag = $product['status'] == 1 ? 1 : 0;
        }
        return $flag;

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
        while ($day <= $lastday)
        {
            $cday = $year . '-' . $month . '-' . $day;

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
            $numstr = '<b style="font-weight:normal">余位&nbsp;' . $number . '</b>';

            //定义单元格样式，高，宽
            $tdStyle = "height='80'";
            //判断当前的日期是否小于今天
            $tdcontent = '<span class="num">' . $day . '</span>';
            if ($defaultmktime >= $currentmktime)
            {


                if ($dayPrice)
                {

                    $dayPriceStrs = Currency_Tool::symbol() . $dayPrice . '<br>';
                    $balanceStr = '';

                    $tdcontent .= '<b class="price">' . $dayPriceStrs . '</b>' . $balanceStr;
                    if ($numstr)
                    {
                        $tdcontent .= $numstr;
                    }
                    if ($number === 0)
                    {
                        $onclick = '';
                    }
                    else
                    {
                        $onclick = 'onclick="choose_day(\'' . $cday . '\',\'' . $contain . '\')"';
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

    /**
     * 页面显示状态
     * @param $data
     * @throws Kohana_Exception
     */
    static function status($data)
    {
        $dbInfo = Kohana::$config->load('database.default');
        $suffix = implode(',', array_values($dbInfo['connection']));
        foreach ($data as $k => $v)
        {
            $suffix .= "{$k}=$v";
        }
        $data['_status_token_'] = md5($suffix);
        $url = self::get_main_host() . '/pub/status';
        $html = "<form action='{$url}' style='display:none;' method='post' id='status'>";
        foreach ($data as $name => $v)
        {
            $html .= "<textarea name='{$name}'>" . $v . '</textarea>';
        }
        $html .= '</form>';
        $html .= "<script>document.forms['status'].submit();</script>";
        header("Content-type: text/html;charset=utf-8");
        exit($html);
    }

    /*
    * 检测链接是否是SSL连接
    * @return bool
    */
    public static function is_SSL()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        {
            return TRUE;
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        {
            return TRUE;
        }
        elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
        {
            return TRUE;
        }
        elseif ($_SERVER['SERVER_PORT'] == 443)
        {
            return TRUE;
        }
        elseif (strtolower($_SERVER['HTTP_X_CLIENT_SCHEME']) == 'https')
        {
            return TRUE;
        }

        return FALSE;
    }

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
        if ($_lenth <= $lenth)
        {
            return $str; // 传入的字符串长度小于截取长度，原样返回
        }
        $strlen_var = strlen($str); // 统计字符串长度（UTF8编码下-中文算3个字符，英文算一个字符）
        if (strpos($str, '<') === false)
        {
            return mb_substr($str, 0, $lenth); // 不包含 html 标签 ，直接截取
        }
        if ($e = strpos($str, $anchor))
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
        for ($i = 0; $i < $strlen_var; ++$i)
        {
            if (!$lenth)
            {
                break;
            } // 遍历完之后跳出
            $current_var = substr($str, $i, 1); // 当前字符
            if ($current_var == '<')
            { // html 代码开始
                $html_tag = 1;
                $html_array_str = '';
            }
            else
            {
                if ($html_tag == 1)
                { // 一段 html 代码结束
                    if ($current_var == '>')
                    {
                        $html_array_str = trim($html_array_str); //去除首尾空格，如 <br / > < img src="" / > 等可能出现首尾空格
                        if (substr($html_array_str, -1) != '/')
                        { //判断最后一个字符是否为 /，若是，则标签已闭合，不记录
                            // 判断第一个字符是否 /，若是，则放在 right 单元
                            $f = substr($html_array_str, 0, 1);
                            if ($f == '/')
                            {
                                $html_array['right'][] = str_replace('/', '', $html_array_str); // 去掉 '/'
                            }
                            else
                            {
                                if ($f != '?')
                                { // 若是?，则为 PHP 代码，跳过
                                    // 若有半角空格，以空格分割，第一个单元为 html 标签。如：<h2 class="a"> <p class="a">
                                    if (strpos($html_array_str, ' ') !== false)
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
        if ($html_array['left'])
        { //比对左右 html 标签，不足则补全
            $html_array['left'] = array_reverse($html_array['left']); //翻转left数组，补充的顺序应与 html 出现的顺序相反
            foreach ($html_array['left'] as $index => $tag)
            {
                $key = array_search($tag, $html_array['right']); // 判断该标签是否出现在 right 中
                if ($key !== false)
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
     * @function 检测上传的是否是有效的图片
     * @param $image 上传的图片对象
     * @return bool|int
     */
    public static function is_valid_image($image)
    {
        if (!Upload::valid($image) OR !Upload::not_empty($image) OR !Upload::type($image, array('jpg', 'jpeg', 'png', 'gif', 'bmp')) OR !Upload::image($image))
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    /**
     * @function 检测是否开启具体产品佣金
     * @param $typeId
     * @return bool
     */
    public static function open_single_finance_set($typeId)
    {
        $model = DB::select()->from('model')->where('id', '=', $typeId)->execute()->current();
        $model = "Model_{$model['maintable']}";
        if (class_exists($model))
        {
            //类存在
            $obj = new $model();
            if (property_exists($obj, 'open_single_finance') && !$obj->open_single_finance)
            {
                //类属性存在并且值为false
                return false;
            }
            else
            {
                //类型属性不存在或值为true
                return true;
            }
        }
        else
        {
            //类不存在
            return true;
        }

    }


    /**
     * @function  判断当前的目的地是否是子站，如果是跳转到子站域名
     * @param $destid 目的地id
     * @param $correct 当前模块的拼音标识
     */
    public static function is_website($destid, $correct, $destpinyin)
    {
        if ($destid && $correct)
        {
            $destinfo = DB::select('iswebsite', 'weburl', 'pid', 'pinyin')->from('destinations')->where('id', '=', $destid)->execute()->current();
            if ($destinfo['iswebsite'] == 1)
            {
                if ($GLOBALS['cfg_basehost'] != $destinfo['weburl'])
                {
                    $url = $destinfo['weburl'] . '/' . $correct;
                    Header('Location:' . $url, 301);
                    exit;
                }

            }

        }
    }

    /**
     * @function 获取产品列表页目的地关联模板
     * @param $typeid
     * @param $destid
     */
    public static function get_list_dest_template_pc($typeid, $destid)
    {
        if (empty($typeid) || empty($destid))
        {
            return null;
        }
        $pinyin = DB::select('pinyin')->from('model')->where('id', '=', $typeid)->execute()->get('pinyin');
        $kindlist_table = $pinyin . '_kindlist';
        if (!St_Database::is_table_exists('sline_' . $kindlist_table))
        {
            return null;
        }

        $template = DB::select('templetpath')->from($kindlist_table)->where('kindid', '=', $destid)->execute()->get('templetpath');
        if (empty($template))
        {
            return null;
        }

        $file_path = BASEPATH . 'usertpl/' . $template;
        if (!file_exists($file_path))
        {
            return null;
        }
        $full_path = 'usertpl/' . $template . '/index';
        return $full_path;
    }


    /**
     * @function  获取请求协议前缀
     * @return string
     */
    public static function get_http_prefix()
    {
        if (self::is_SSL())
        {
            return 'https://';
        }
        return 'http://';

    }


    /**
     * @function 获取移动端导航默认图片
     */
    public static function get_menu_default_ico()
    {

        return array(
            '1' => 'menu_ico02.png',
            '2' => 'menu_ico01.png',
            '3' => 'menu_ico05.png',
            '4' => 'menu_ico09.png',
            '5' => 'menu_ico03.png',
            '6' => 'menu_ico10.png',
            '8' => 'menu_ico04.png',
            '10' => 'menu_ico12.png',
            '11' => 'menu_ico07.png',
            '12' => 'menu_ico11.png',
            '13' => 'menu_ico06.png',
            '14' => 'menu_ico08.png',
            '101' => 'menu_ico101.png',
            '104' => 'menu_ico104.png',
            '105' => 'menu_ico105.png',
            '114' => 'menu_ico114.png'
        );
    }

    /**
     * @function PC详情页图片裁剪
     * @param $content
     * @param int $width
     * @param int $height
     * @param bool $watermark
     * @return mixed
     */
    public static function content_image_width($content, $width = 0, $height = 0,$watermark=false)
    {
        if ($width || $height)
        {
            self::_content_image_width_callback('', $width, $height,$watermark);
            $content = preg_replace_callback('~(<img[^>]*?src=("|\'))(.*?)\\2~', array('self', '_content_image_width_callback'), $content);
        }
        return $content;
    }

    /**
     * @function content_image_width函数回调
     * @param $match
     * @param null $width
     * @param null $height
     * @param null $watermark
     * @return string
     */
    private static function _content_image_width_callback($match, $width = null, $height = null,$watermark=null)
    {
        static $_width;
        static $_height;
        static $_watermark;
        if (is_null($_width) || !is_null($width))
        {
            $_width = $width;
        }
        if (is_null($_height) || !is_null($height))
        {
            $_height = $height;
        }
        if (is_null($_watermark) || !is_null($watermark))
        {
            $_watermark = $watermark;
        }
        return $match ? $match[1] . Common::img($match[3], $_width, $_height,true) . $match[2] : '';
    }


}
