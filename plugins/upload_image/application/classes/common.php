<?php

/**
 * 公共静态类模块
 * User: Netman
 * Date: 15-09-12
 * Time: 下午14:06
 */
require TOOLS_COMMON . 'functions.php';
class Common extends Functions
{

    // 写入系统缓存
    public static function cache_config()
    {
        $file = APPPATH . '/cache/config.php';
        //缓存文件不存在
        if (!file_exists($file))
        {
            $data = Model_Sysconfig::config();
            $config = array();
            foreach ($data as $v)
            {
                $config[$v['varname']] = trim($v['value']);
            }
            if (!isset($config['cfg_m_img_url']))
            {
                $config['cfg_m_img_url'] = $config['cfg_m_main_url'];
            }
            $config['cfg_m_logo'] = $config['cfg_m_img_url'] . $config['cfg_m_logo'];
            file_put_contents($file, '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');' . PHP_EOL . 'return ' . var_export($config, true) . ';');
        }
        $data = require_once($file);
        return $data;
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

    public static function getConfig($group)
    {
        return Kohana::$config->load($group);
    }


}
