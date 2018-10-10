<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 站点管理类
 * */

class Model_Web
{

    /**
     *  新建站点初始化数据
     *
     * @access    public
     * @return
     */
    public static function initData($webid)
    {


        $file = APPPATH . 'data/init/childsiteinit.txt';
        $file_handle = fopen($file, "r");
        $query = '';
        while (!feof($file_handle))
        {
            $line = fgets($file_handle, 4096);

            if (preg_match("#;#", $line))
            {
                $query .= $line;
                $query = str_replace('{webid}', $webid, $query);
                $query = str_replace('{fenhao}', ';', $query);

                DB::query(2, $query)->execute();
                $query = '';
            }
            else
            {
                $query .= $line;
            }


        }
        fclose($file_handle);

    }

    /*
     * 清除导航
     * */
    public static function delNav($siteid)
    {
        $sql = "delete from sline_nav where webid='$siteid'";
        DB::query(Database::DELETE, $sql)->execute();
    }

    /*
     * 清除右侧模块
     * */
    public static function delRightModule($siteid)
    {
        $sql = "delete from sline_module_config where webid='$siteid'";
        DB::query(Database::DELETE, $sql)->execute();
    }

    /*
   * 生成站点列表(前台使用)
   * */
    public static function genWeblist()
    {
        $out = array();
        $sql = "select webprefix,id,weburl,kindname from sline_destinations where iswebsite=1 ";
        $arr = DB::query(1, $sql)->execute()->as_array();
        foreach ($arr as $row)
        {
            $out[$row['webprefix']] = array(
                'webprefix' => $row['webprefix'],
                'weburl' => $row['weburl'],
                'kindname' => $row['kindname'],
                'webid' => $row['id']
            );
        }
        $weblist = "<?php \$weblist= " . var_export($out, true) . ";";
        $webfile = CACHE_DIR . 'v5/weblist.php';
        $fp = fopen($webfile, 'wb');
        flock($fp, 3);
        fwrite($fp, $weblist);
        fclose($fp);

    }

    /*
     * 生成配置文件
     * */
    public static function createDefaultConfig($siteid)
    {
        $m = new Model_Sysconfig();
        $m->writeConfig($siteid);
    }


    //初始化版本htaccess

    public static function init_version_param()
    {
        $configinfo = ORM::factory('sysconfig')->getConfig(0);
        //如果4.2版本不存在,则不存在切换版本的必要
        if (!file_exists(BASEPATH . '/include'))
        {
            return false;
        }
        //4.X版本
        if ($configinfo['cfg_pc_version'] == 0)
        {
            $file = APPPATH . 'data/init/htaccess4.1.txt';
            $indexfile = APPPATH . 'data/init/index4.1.txt';
            //检测4.x版本是否存在,如果不存在,则退出.
            if (!file_exists(BASEPATH . '/include'))
            {
                return false;
            }
        }
        //5.X版本
        elseif ($configinfo['cfg_pc_version'] == 5)
        {
            $file = APPPATH . 'data/init/htaccess5.1.txt';
            $indexfile = APPPATH . 'data/init/index5.1.txt';
            //检测5.x版本是否存在,如果不存在,则退出.
            if (!file_exists(BASEPATH . '/v5'))
            {
                return;
            }
        }
        //网站入口文件 index.php
        $file_index_handle = fopen($indexfile, "r");
        $index = "";
        while (!feof($file_index_handle))
        {
            $index .= fgets($file_index_handle, 1024);
        }
        $indexpath = BASEPATH . '/index.php';
        if (!empty($index))
        {
            Common::saveToFile($indexpath, $index);
        }
        //伪静态处理 确定当前版本及目标版本
        $htFile = BASEPATH . '/.htaccess';
        $htFileBak = APPPATH . 'data/init/htaccess.bak.txt';
        $currentHtaccess = file_get_contents($htFile);
        if (strpos($currentHtaccess, 'destination/main.php') === false && strpos($currentHtaccess, 'payment') !== false)
        {
            $currentVersion = 5;
        }
        else
        {
            $currentVersion = 0;
        }
        if ($configinfo['cfg_pc_version'] != $currentVersion)
        {
            if (!file_exists($htFileBak))
            {
                self::write_mobile_htaccess(file_get_contents($file), $htFileBak);
            }
            //写入htaccess
            file_put_contents($htFile, file_get_contents($htFileBak));
            //备份版本
            file_put_contents($htFileBak, $currentHtaccess);
        }
    }

    /**
     * 写入手机域名配置
     * @param $content
     * @param $savePath
     */
    public static function write_mobile_htaccess($content, $savePath)
    {
        $sysconfiginfo = ORM::factory('sysconfig')->getConfig(0);
        $config = self::mobile_config($sysconfiginfo);
        if ($sysconfiginfo['cfg_mobile_version'] < 1 || $config['domain']['mobile'] == $config['domain']['main'])
        {
            $config['domain']['mobile'] = $config['domain']['main'];
            $content = preg_replace("`({$config['delimiterLeft']}).*({$config['delimiterRight']})`is", '$1' . "\r\n" . '$2', $content);
        }
        else
        {
            $replace = $config['delimiterLeft'];
            $replace .= str_replace(array('{PHP_EOL}', '{host}', '{path}'), array("\r\n", parse_url($config['domain']['mobile'], PHP_URL_HOST), rtrim($config['version'][$sysconfiginfo['cfg_mobile_version']]['path'], '/')), $config['rules']);
            $replace .= $config['delimiterRight'];
            if (preg_match("~" . $config['delimiterLeft'] . '.*' . $config['delimiterRight'] . '~is', $content))
            {
                $content = preg_replace("~" . $config['delimiterLeft'] . '.*' . $config['delimiterRight'] . '~is', str_replace('$1', '\$1', $replace), $content);
            }
            else
            {
                $replace = 'RewriteBase /' . "\r\n" . $replace . "\r\n";
                $content = str_replace('RewriteBase /', $replace, $content);
            }
        }
        file_put_contents($savePath, $content);
    }

    /**
     * 获取手机站点配置
     * @param $sysconfiginfo
     * @return mixed
     */
    public static function mobile_config($sysconfiginfo)
    {
        $file = BASEPATH . '/data/mobile.php';
        $config = include($file);
        //如果配置不存在,则使用下面默认值
        if (empty($config['delimiterLeft']) || empty($config['version']))
        {
            $config['delimiterLeft'] = '#mobile start';
            $config['delimiterRight'] = '#mobile end';
            $config['rules'] = '{PHP_EOL}RewriteCond %{HTTP_HOST} ^{host}${PHP_EOL}RewriteCond %{REQUEST_URI} !^/uploads/ {PHP_EOL} RewriteRule (.*) {path}/$1 [L]{PHP_EOL}';
            $config['rulesReplace'] = false;
            $config['version'] = array(
                0 =>
                    array(
                        'no' => '4.1',
                        'path' => '/shouji/',
                    ),
                1 =>
                    array(
                        'no' => '5.0',
                        'path' => '/phone/',
                    )
            );
        }
        $config['domain']['mobile'] = $sysconfiginfo['cfg_m_main_url'];
        $config['domain']['main'] = St_Functions::get_http_prefix() . $_SERVER['HTTP_HOST'];
        file_put_contents($file, '<?php ' . "\r\n" . 'return ' . var_export($config, true) . ';');
        return $config;
    }

}