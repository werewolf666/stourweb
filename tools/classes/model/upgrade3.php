<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 系统升级类(新版本)
 * */

class Model_Upgrade3
{
    private static $_system_config = null;

    public static function get_system_config($config_key)
    {
        if (self::$_system_config == null)
            self::$_system_config = ORM::factory('sysconfig')->getConfig(0);

        if (array_key_exists($config_key, self::$_system_config))
            return self::$_system_config[$config_key];
        else
            return null;
    }

    /*
         * $upgrade_task['system_part_code']='core/pc/25...'
         * $upgrade_task['name']='供应商门票产品管理'
         * $upgrade_task['upgrade_code']='stourwebcms'
         * $upgrade_task['app_number']='e0ed40a3-6750-496e-b1cd-e34628b169ea'
         * $upgrade_task['system_part_type']='1:system,2:app'
         */
    public static function upgrade_task_library(array $upgrade_task_list = null)
    {
        $file = BASEPATH . "/data/upgradetasklibrary.php";
        if (!is_dir(dirname($file)))
            mkdir(dirname($file), 0777, true);

        $result = true;

        $fp = null;
        if ($upgrade_task_list == null)
        {
            $fp = fopen($file, 'r');
            $json = fread($fp, filesize($file));
            $result = json_decode($json);
            if ($result != null)
                $result = Common::struct_to_array($result);
        } else
        {
            $system_upgrade_task_list = array();
            $app_upgrade_task_list = array();
            foreach ($upgrade_task_list as $upgrade_task)
            {
                if ($upgrade_task["system_part_type"] == "1")
                    $system_upgrade_task_list[] = $upgrade_task;
                else
                    $app_upgrade_task_list[] = $upgrade_task;
            }
            $upgrade_task_list = array_merge($system_upgrade_task_list, $app_upgrade_task_list);

            $fp = fopen($file, 'w');
            flock($fp, 3);
            $result = fwrite($fp, json_encode($upgrade_task_list));
        }
        fclose($fp);

        return $result;
    }

    public static function upgrade_data_library(array $upgrade_data = null)
    {
        $file = BASEPATH . "/data/upgradedatalibrary.php";
        if (!is_dir(dirname($file)))
            mkdir(dirname($file), 0777, true);

        $result = true;

        $fp = null;
        if ($upgrade_data == null)
        {
            $fp = fopen($file, 'r');
            $json = fread($fp, filesize($file));
            $result = json_decode($json);
            if ($result != null)
                $result = Common::struct_to_array($result);
        } else
        {
            $fp = fopen($file, 'w');
            flock($fp, 3);
            $result = fwrite($fp, json_encode($upgrade_data));
        }
        fclose($fp);

        return $result;
    }

    public static function check_system_part_update(array $system_part_code_list)
    {
        $result = array();

        if (is_array($system_part_code_list) && count($system_part_code_list) > 0)
        {
            $partinfo_list = self::get_system_part_list($system_part_code_list);

            $upgradeapi_model = new Model_Upgrade3Api();
            $checkresult = $upgradeapi_model->batchCheckNewestPatch($partinfo_list);
            foreach ($partinfo_list as $system_part_code => $partinfo)
            {
                if ($partinfo == null)
                {
                    $result[$system_part_code] = null;
                    continue;
                } else
                    $result[$system_part_code] = array();

                if ($checkresult['Success'] == 1)
                {
                    $checkresultdata = $checkresult['Data'];
                    foreach ($checkresultdata as $new_patch)
                    {
                        if (strtolower(trim($partinfo["pcode"])) == strtolower(trim($new_patch["ProductCode"])))
                            $result[$system_part_code][] = $new_patch;
                    }
                }
            }

        }

        return $result;
    }

    public static function get_system_part_list(array $system_part_code_list)
    {
        $result = array();

        if (is_array($system_part_code_list) && count($system_part_code_list) > 0)
        {
            $all_system_part = Model_SystemParts::getSystemParts();
            foreach ($system_part_code_list as $system_part_code)
            {
                $result[$system_part_code] = null;
                if (array_key_exists($system_part_code, $all_system_part))
                {
                    $system_part_id = self::get_system_config("cfg_{$system_part_code}_version");
                    $partinfo = Model_SystemParts::getSystemPart($system_part_code, $system_part_id);
                    if ($system_part_id == null)
                        $result[$system_part_code] = $partinfo[0];
                    else
                        $result[$system_part_code] = $partinfo;
                } else
                    $result[$system_part_code] = Model_SystemParts::getAppPart($system_part_code, "");
            }
        }

        return $result;
    }

    public static function get_core_system_part()
    {
        $corePart = Model_Upgrade3::get_system_part_list(array(Model_SystemParts::$coreSystemPartCode));
        if (!is_array($corePart) || count($corePart) <= 0)
        {
            return null;
        } else
        {
            return $corePart[Model_SystemParts::$coreSystemPartCode];
        }
    }

    /*
     * 升级页面使用
     * 检测升级包(获取包括已升级和未升级的包的列表)
     *
     * */
    public static function get_new_patch_list(array $system_part_info, $is_betaupgrade)
    {
        if ($system_part_info == null)
        {
            return "不正确的系统组件信息";
        }

        $model = new Model_Upgrade3Api();
        $model->initialise($system_part_info);
        $info = $model->getNewVersion();
        if ($info['Success'] != 1)
        {
            return $info["Message"];
        }

        $patch_list = array();
        foreach ($info['Data'] as $patchdata)
        {
            if (!$is_betaupgrade && $patchdata["Status"] != "1")
                continue;

            $patch_list[] = array_merge($patchdata, self::generate_patch_info($patchdata));
        }

        return array_reverse($patch_list);
    }

    public static function get_patch_list(array $system_part_info, $days_before)
    {
        if ($system_part_info == null)
        {
            return "不正确的系统组件信息";
        }

        $model = new Model_Upgrade3Api();
        $model->initialise($system_part_info);
        $info = $model->searchPatch(array(
            'releasedate' => date('Y-m-d', strtotime("-{$days_before} day")),
            'pageno'=>1,
            'pagesize'=>100
        ));
        if ($info['Success'] != 1)
        {
            return $info["Message"];
        }

        $patch_list = array();
        $current_version_no = str_ireplace(".", "", $system_part_info['cVersion']);
        foreach ($info['Data'] as $patchdata)
        {
            if ($current_version_no >= str_ireplace(".", "", $patchdata["Version"]))
                $patchdata['upgrade_status'] = "已更新";
            else
                $patchdata['upgrade_status'] = "未更新";

            $patch_list[] = array_merge($patchdata, self::generate_patch_info($patchdata, "list"));
        }

        return $patch_list;
    }

    private static function generate_patch_info($patchdata, $desc_mode = "line")
    {
        if ($patchdata['Status'] == 3)
            $status_name = '(内测)';
        elseif ($patchdata['Status'] == 2)
            $status_name = '(公测)';
        else
            $status_name = '';

        $ar = array(
            'desc' => ($desc_mode == "line" ? Model_Upgrade3Api::gen_line_Desc($patchdata['Description']) : Model_Upgrade3Api::gen_list_Desc($patchdata['Description'])),
            'pubdate' => Common::myDate('Y-m-d', strtotime($patchdata['ReleaseDate'])),
            'version' => $patchdata['Version'] . $status_name,
            'filesize' => Model_Upgrade3Api::format_bytes($patchdata['FileSize'])
        );
        return $ar;
    }

    public static function exists_noupgrade_in_coresystem($patchstatus)
    {
        $corepart = self::get_core_system_part();
        if ($corepart == null)
            return true;

        $core_new_patch_list = self::get_new_patch_list($corepart, true);
        if (!is_array($core_new_patch_list))
            return true;

        foreach ($core_new_patch_list as $core_new_patch)
        {
            if ($core_new_patch['Status'] <= $patchstatus)
                return true;
        }
        return false;
    }

    public static function clear_upgrade_tmp_dir($unzippath)
    {
        Common::rrmdir(dirname($unzippath));
    }

    public static function backup_original_file($unzippath)
    {
        $backupOriginalDir = $unzippath . '_backupdriginal';

        if (!is_dir($backupOriginalDir) && !mkdir($backupOriginalDir, 0777, true))
        {
            return array('success' => false, 'errormsg' => "创建备份目录 $backupOriginalDir 失败");
        }

        $fileManifest = $unzippath . '/FileManifest.txt';
        if (!file_exists($fileManifest))
            return array('success' => true, 'backupdir' => $backupOriginalDir);

        $fp = fopen($fileManifest, 'r');
        if (!$fp)
        {
            return array('success' => false, 'errormsg' => "打开补丁清单文件 $fileManifest 失败");
        }

        $result = array('success' => true, 'backupdir' => $backupOriginalDir);
        while (!feof($fp))
        {
            $filename = trim(fgets($fp));
            if (empty($filename) || self::is_install_config_file($filename))
                continue;

            $filename = str_ireplace('newtravel', $GLOBALS['cfg_backdir'], $filename);
            $fromfile = BASEPATH . '/' . $filename;
            $tofile = $backupOriginalDir . '/' . $filename;
            if (!is_file($fromfile))
                continue;

            if (!is_writable($fromfile))
            {
                $result = array('success' => false, 'errormsg' => "文件 $fromfile 不可写");
                break;
            }
            $todir = dirname($tofile);
            if (!is_dir($todir) && !mkdir($todir, 0777, true))
            {
                $result = array('success' => false, 'errormsg' => "创建目录 $todir 失败");
                break;
            }
            if (!copy($fromfile, $tofile))
            {
                $result = array('success' => false, 'errormsg' => "拷贝文件 $fromfile 到 $tofile 失败");
                break;
            }
        }

        fclose($fp);
        return $result;
    }

    //后台目录替换
    public static function replace_background_dir($unzippath)
    {
        $result = array('success' => true, 'errormsg' => "");
        $backdir = $GLOBALS['cfg_backdir'] ? $GLOBALS['cfg_backdir'] : 'newtravel';

        if (strtoupper($backdir) != strtoupper('newtravel') && is_dir($unzippath))
        {
            $dh = opendir($unzippath);
            if (!$dh)
            {
                $result['success'] = false;
                $result['errormsg'] = "打开目录{$unzippath}失败";
                return $result;
            }

            while ($object = readdir($dh))
            {
                if ($object != "." && $object != "..")
                {
                    $fullname = $unzippath . "/" . $object;
                    if (is_dir($fullname))
                    {
                        if (strtoupper($object) == strtoupper('newtravel'))
                        {
                            if (!rename($fullname, $unzippath . "/" . $backdir))
                            {
                                $result['success'] = false;
                                $result['errormsg'] = "重命名目录{$fullname}失败";
                                break;
                            }

                        }
                    }
                }
            }

            closedir($dh);
        }
        return $result;
    }

    public static function get_app_install_path($system_part_code)
    {
        return BASEPATH . "/data/appinstall/{$system_part_code}";
    }

    public static $INSTALL_CONFIG_FILES = array('FileManifest.txt', 'installer.ini', 'sql.php', 'unsql.php', 'handle.php');

    public static $SPECIAL_APP_NUMBERS = array('system_templet', 'custom_templet', 'system_app', 'custom_app');

    public static function is_install_config_file($filename)
    {
        return in_array(trim($filename), self::$INSTALL_CONFIG_FILES);
    }

    /**
     * 移动文件
     * @param $unzippath
     * @return bool|string
     */
    public static function pick_install_config_files($unzippath)
    {
        $files = self::$INSTALL_CONFIG_FILES;
        $dir = $unzippath . '_install';
        if (!is_dir($dir) && !mkdir($dir, 0777, true))
        {
            return false;
        }
        foreach ($files as $file)
        {
            $org = $unzippath . "/{$file}";
            $dist = $dir . "/{$file}";
            if (file_exists($org))
            {
                if (!rename($org, $dist))
                {
                    return false;
                }
            }
        }
        return array('distDir' => $dir, 'sql' => $dir . '/sql.php');
    }

    /**
     * 移动文件
     * @param $unzippath
     * @param $distDir
     * @param string $version
     * @return bool
     */
    public static function setup_install_config_files($unzippath, $app_install_dir, $version = '0.0.0.0')
    {
        if (!file_exists($app_install_dir))
        {
            if (!mkdir($app_install_dir, 0777, true))
            {
                return false;
            }
        }

        $files = self::$INSTALL_CONFIG_FILES;
        foreach ($files as $file)
        {
            $orgFile = $unzippath . '_install' . "/{$file}";
            $distFile = $app_install_dir . "/{$file}";
            if (!file_exists($orgFile))
            {
                continue;
            }

            if(strtolower($file) == 'sql.php')
            {
                continue;
            }

            if (strtolower($file) == 'filemanifest.txt' || strtolower($file) == 'unsql.php')
            {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $distFile = $app_install_dir . "/{$version}.{$ext}";
            }

            if (!rename($orgFile, $distFile))
            {
                return false;
            }
        }
        return true;
    }

    public static function download_and_unzip($download_url)
    {
        $name = time();
        $savepath = APPPATH . "/data/patch/"; //更新包存储路径
        $filename = $savepath . $name . '.zip';
        $unzippath = BASEPATH . '/data/upgradetmp/' . $name; //解压路径
        self::clear_upgrade_tmp_dir($unzippath);

        $ok = Common::downloadFile($download_url, $filename);
        if ($ok) //下载文件成功
        {
            include(PUBLICPATH . '/vendor/zipfolder.php');
            $archive = new ZipFolder();

            $archive->setLoadPath(dirname($filename) . '/');
            $archive->setFile(basename($filename));
            $archive->setSavePath(dirname($unzippath) . '/');

            $extractResult = $archive->openZip();

            if (!$extractResult || Common::isEmptyDir($unzippath))
            {
                $out['status'] = 0;
                $out['msg'] = '升级文件解压失败,升级文件损坏或网站目录及子目录无写权限'; //目录无写权限
                return $out;
            } else
            {
                $archive->eraseZip();

                $out['status'] = 1;
                $out['unzippath'] = $unzippath;
                return $out;
            }

        } else
        {
            $out['status'] = 0;
            $out['msg'] = '下载升级包失败';
            return $out;
        }
    }

    /**
     * 获取目录
     * @param $dir
     * @return array
     */
    public static function get_parent_dir($dir)
    {
        $dir = str_ireplace("\\", "/", $dir);
        $dir = rtrim($dir, "/");
        $web_root_dir = str_ireplace("\\", "/", BASEPATH . '/');
        $dir = str_ireplace($web_root_dir, "", $dir);

        $dirs = array();

        $count = substr_count($dir, '/');
        $dir = $web_root_dir . $dir;
        $dirs[] = $dir;

        for ($i = 0; $i < $count; $i++)
        {
            $dirs[] = $dir = dirname($dir);
        }

        return $dirs;
    }

    public static function is_valid_system_part($system_part_code, $system_part_list)
    {
        if (!is_array($system_part_list) || count($system_part_list) <= 0 || !array_key_exists($system_part_code, $system_part_list) || empty($system_part_list[$system_part_code]))
            return false;
        else
            return true;
    }

    public static function load_templet_handle_file($system_part_code)
    {
        $result = array();

        $handle_file = self::get_app_install_path($system_part_code) . '/handle.php';
        if (file_exists($handle_file))
        {
            $handle_file_content = file_get_contents($handle_file);
            if (!empty($handle_file_content))
            {
                $matches = array();
                if (preg_match('/\$templet_name\s*?=\s*?[\'"].+?[\'"];/s', $handle_file_content, $matches))
                {
                    eval($matches[0]);
                    $result['templet_name'] = $templet_name;
                }
                $matches = array();
                if (preg_match('/\$advertise_templet_id\s*?=\s*?[\'"].+?[\'"];/s', $handle_file_content, $matches))
                {
                    eval($matches[0]);
                    $result['advertise_templet_id'] = $advertise_templet_id;
                }
                $matches = array();
                if (preg_match('/\$templet_page_info_list\s*?=\s*?.+?\);/s', $handle_file_content, $matches))
                {
                    $code = str_ireplace("TEMPLET_RUN_PLATFORM_SUBSITE", "'sub_site'", $matches[0]);
                    $code = str_ireplace("TEMPLET_RUN_PLATFORM_WAP", "'wap'", $code);
                    $code = str_ireplace("TEMPLET_RUN_PLATFORM_PC", "'pc'", $code);
                    eval($code);
                    $result['templet_page_info_list'] = $templet_page_info_list;
                }
            }
        }

        return $result;
    }

    public static function load_app_handle_file($system_part_code)
    {
        $result = array();

        $handle_file = self::get_app_install_path($system_part_code) . '/handle.php';
        if (file_exists($handle_file))
        {
            require_once($handle_file);
            $result = $app_setup_config;
        }

        return $result;
    }

}