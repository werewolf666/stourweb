<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Sysconfig extends ORM
{

    ///********************* PC端开始  ***********************///

    /**
     * @function 获取所有配置数组
     * @param int $webid
     * @return mixed
     */
    public static function config($webid = 0)
    {
        $sql = "select varname,value from sline_sysconfig where webid={$webid}";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    ///********************* PC端结束 ***************************///


    ///********************* 手机端开始  ***********************///

    /**
     * @function 获取所有配置数组
     * @param int $webid
     * @return mixed
     */
    public function get_config($webid)
    {
        $arr = $this->where('webid', '=', $webid)->get_all();
        $out = array();
        foreach ($arr as $row) {
            $out[$row['varname']] = $row['value'];
        }
        return $out;
    }
    ///********************* 手机端结束 ***************************///

    ///********************* 后台开始  ***********************///

    /**
     * @function 获取所有配置数组
     * @param $webid
     * @return array
     */
    public function getConfig($webid)
    {
        $arr = $this->where('webid', '=', $webid)->get_all();
        $out = array();
        foreach ($arr as $row) {
            $out[$row['varname']] = $row['value'];
        }
        return $out;
    }

    /**
     * @function 保存配置信息
     * @param $arr
     * @return bool
     * @throws Kohana_Exception
     */
    public function saveConfig($arr)
    {

        $webid = ARR::get($arr, 'webid');

        foreach ($arr as $k => $v) {
            /* if(!get_magic_quotes_gpc())
             {
                 $v = addslashes($v);
             }
             else
             {
                 $v = $v;
             }*/
            if ($k !== 'webid') {
                $row = $this->where('webid', '=', $webid)->and_where('varname', '=', $k)->find();
                if ($k == 'cfg_logourl'&& $v && !preg_match('~https?://~', $v)) {
                    $v = (Request::initial()->secure() ? 'https://' . $v : 'http://').$v;
                }
                if (isset($row->id)) //如果存在则修改,如果不存在则创建
                {

                    //$v = $k=='cfg_tongjicode' ? addslashes($v) : $v;
                    $row->value = $v;
                    $row->update();
                }
                else {
                    $row->varname = $k;
                    $row->value = $v;
                    $row->webid = $webid;
                    $row->create();
                }

                $row->clear();
            }

        }
        //  $this->writeConfig($webid);
        self::write_config($webid);
        return true;
    }

    /**
     * @function 写配置缓存文件
     * @param $webid
     */
    public function writeConfig($webid)
    {

        $configfile = $this->getConfigFilePath($webid);
        $fp = fopen($configfile, 'wb');
        flock($fp, 3);
        fwrite($fp, "<" . "?php\r\n");
        $arr = ORM::factory('sysconfig')->where('webid', '=', $webid)->get_all();
        // if($webid==0)//主站配置信息
        // {
        foreach ($arr as $row) {

            if ($row['varname'] == 'cfg_tongjicode') {
                fwrite($fp, "\${$row['varname']} = '" . addslashes($row['value']) . "';\r\n");
            }
            else {
                fwrite($fp, "\${$row['varname']} = '" . str_replace("'", '', $row['value']) . "';\r\n");
            }

        }
        fwrite($fp, "?" . ">");
        fclose($fp);

    }


    /**
     * @function 获取配置文件信息
     * @param $webid
     * @return string
     */
    private function getConfigFilePath($webid)
    {
        if ($webid == 0) {
            $file = SLINEDATA . '/config.cache.inc.php';
        }
        else {
            $path = SLINEDATA . '/config';
            if (!is_dir($path)) mkdir($path, 0777, true);
            $webinfo = Common::getWebInfo($webid);
            $file = SLINEDATA . '/config/config.' . $webinfo['webprefix'] . '.php';
        }
        return $file;
    }

    /**
     * @function 获取系统版本
     * @param null $platfrom
     * @return array
     */
    public static function system_version($platfrom = NULL)
    {
        $version = array();
        $rs = DB::select('value', 'varname')->from('sysconfig')->where('varname', 'in', array('cfg_pc_version', 'cfg_mobile_version'))->execute()->as_array();
        foreach ($rs as $v) {
            $version[$v['varname']] = $v['value'];
        }
        if ($platfrom) {
            return $version[$platfrom];
        }
        return $version;
    }

    /**
     * @function 获取系统配置
     * @param int $webid
     * @param null $fields 要获取的字段数组或字符
     * @param $isvalue 是否直接返回值
     * @return array
     */
    public static function get_configs($webid = 0, $fields = null, $isvalue = null)
    {
        $query = DB::select('value', 'varname')->from('sysconfig')->where('webid', '=', $webid);
        if (is_array($fields))
            $query->and_where('varname', 'in', $fields);
        if (is_string($fields))
            $query->and_where('varname', '=', $fields);
        $list = $query->execute()->as_array();
        $out = array();
        foreach ($list as $row) {
            if ($isvalue)
                return $row['value'];
            $out[$row['varname']] = $row['value'];
        }
        return $out;
    }

    ///********************* 后台结束  ***********************///

    /**
     * @function 获取系统配置
     * @param $field
     * @param $varname
     * @param int $webid
     * @return mixed
     */
    public static function get_sys_conf($field, $varname, $webid = 0)
    {
        $result = DB::select($field)->from('sysconfig')->where('varname', '=', $varname)->and_where('webid', '=', $webid)->execute()->current();
        return $result[$field];
    }

    /**
     * @function 保存配置
     * @param $arr (包括至少一个键webid和cfg开头的配置项)
     * @return bool
     * @throws Kohana_Exception
     */
    public static function save_config($arr)
    {
        $webid = $arr['webid'];
        foreach ($arr as $k => $v) {
            if ($k !== 'webid') {
                $row = ORM::factory('sysconfig')->where('webid', '=', $webid)->and_where('varname', '=', $k)->find();

                if (isset($row->id)) //如果存在则修改,如果不存在则创建
                {

                    //$v = $k=='cfg_tongjicode' ? addslashes($v) : $v;
                    $row->value = $v;
                    $row->update();
                }
                else {
                    $row->varname = $k;
                    $row->value = $v;
                    $row->webid = $webid;
                    $row->create();
                }
                $row->clear();
            }

        }
        self::write_config($webid);
        return true;
    }

    //支付

    /**
     * 根据站点获取配置
     * @param int $webid
     * @return array
     */
    public static function payment_config($webid = 0)
    {
        $sql = "select varname,value from sline_sysconfig where webid={$webid}";
        $arr = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $out = array();
        foreach ($arr as $row) {
            $out[$row['varname']] = $row['value'];
        }
        return $out;
    }

    public static function write_config($webid)
    {

        $sys_prefix = '';
        if ($webid == 0) {
            $sys_prefix = St_Functions::get_main_prefix();
        }
        else {
            $sub_prefix = DB::select('webprefix')->from('weblist')->where('id', '=', $webid)->execute()->get('webprefix');
            $sys_prefix = empty($sub_prefix) ? $sys_prefix : $sub_prefix;
        }
        $file = CACHE_DIR . 'v5/config.' . $sys_prefix . '.php';


        //缓存文件不存在
        // if (!file_exists($file))
        //{
        $data = self::config($webid);
        $config = array();
        foreach ($data as $v) {
            $config[$v['varname']] = trim($v['value']);
        }
        if (!isset($config['cfg_m_img_url'])) {
            $config['cfg_m_img_url'] = $config['cfg_m_main_url'];
        }
        if ($webid > 0) {
            $main = self::config(0);
            //子站只有一个页面效果
            $config['cfg_index_templet'] = '';
            foreach ($main as $v) {
                if (!isset($config[$v['varname']])) {
                    $config[$v['varname']] = trim($v['value']);
                }
            }
        }
        file_put_contents($file, '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');' . PHP_EOL . 'return ' . var_export($config, true) . ';');
        // }
        $data = require_once($file);
        return $data;
    }

}