<?php defined('SYSPATH') or die('No direct script access.');

/*
 * author:netman
 * description:upgrade
 * date:2015-02-13
 * qq:87482723
 * */

class Controller_Upgrade extends Stourweb_Controller
{
    /*
     * 增值应用
     * */
    public function before()
    {
        parent::before();
    }

    //设置应用升级状态
    public function action_ajax_ready_upgrade()
    {
        $data['status'] = 0;

        $upgrade_task_list = Arr::get($_POST, 'upgrade_task_list');
        if (!is_array($upgrade_task_list) || count($upgrade_task_list) <= 0)
        {
            $data['msg'] = "准备执行失败，没有可执行的任务";
            exit(json_encode($data));
        }

        if (Model_Upgrade3::upgrade_task_library($upgrade_task_list) == false)
        {
            $data['msg'] = "准备执行失败，创建执行任务失败";
            exit(json_encode($data));
        }

        $data['status'] = 1;
        $data['msg'] = '准备执行成功';

        echo json_encode($data);
    }

    public function action_ajax_send_task_data()
    {
        $data['status'] = 0;

        $check_environment_result = Model_Upgrade3Env::check_environment();
        if ($check_environment_result['success'] !== true) //检测目录写权限
        {
            $data['msg'] = $check_environment_result['msg'];
            exit(json_encode($data));
        }
        if (!$this->checkLicense()) //检测序列号是否正确
        {
            $data['msg'] = '序列号验证失败,请检查序列号';
            exit(json_encode($data));
        }

        $ugrade_task_list = Model_Upgrade3::upgrade_task_library();

        $data['status'] = 1;
        $data['task_list'] = $ugrade_task_list;
        echo json_encode($data);
    }

    public function action_install()
    {
        //$this->send_task_data();
        $this->display('stourtravel/upgrade/install');
    }

    public function action_ajax_exec_install()
    {
        set_time_limit(0);
        $result['status'] = 0;

        $current_task = Arr::get($_POST, 'task');
        if (empty($current_task))
        {
            $result["msg"] = "没有可执行的任务";
            exit(json_encode($result));
        }

        $get_installer_result;
        if (in_array($current_task["app_number"], Model_Upgrade3::$SPECIAL_APP_NUMBERS))
        {
            //本地安装包
            $get_installer_result = $this->get_local_installer($current_task);
        } else
        {
            //在线安装包
            $get_installer_result = $this->get_online_installer($current_task);
        }

        if ($get_installer_result['status'] == 2)
        {
            $result['status'] = 1;
            $result["msg"] = $get_installer_result['msg'];
            exit(json_encode($result));
        }
        if ($get_installer_result['status'] == 0)
        {
            $result["msg"] =  $get_installer_result['msg'];
            exit(json_encode($result));
        }


        $unzippath = $get_installer_result["data"];
        $pick_install_config_result = Model_Upgrade3::pick_install_config_files($unzippath);
        if ($pick_install_config_result === false)
        {
            $result['msg'] = "提取安装配置文件失败";
            exit(json_encode($result));
        }

        //执行SQL
        if (file_exists($pick_install_config_result['sql']))
        {
            $url = str_ireplace(BASEPATH, $GLOBALS['cfg_basehost'], $pick_install_config_result['sql']);
            $flag = Common::http($url);
            if ($flag === false || strlen($flag) > 6)
            {
                $result['msg'] = '数据安装失败' . $flag;
                exit(json_encode($result));
            }
        }
        //移动文件
        $app_files_install_result = Common::xCopy($unzippath, BASEPATH, true);
        if ($app_files_install_result['success'] == false)
        {
            $result['msg'] = '文件安装失败,' . $app_files_install_result['errormsg'];
            exit(json_encode($result));
        }

        //写入数据库
        $appapi_model = new Model_AppApi();
        $insertId = $appapi_model->setup_app_install_data($current_task);
        if ($insertId > 0)
        {
            if (Model_Upgrade3::setup_install_config_files($unzippath, Model_Upgrade3::get_app_install_path($insertId)) == false)
            {
                $appapi_model->unsetup_app_install_data($insertId);
                $result['msg'] = '配置文件安装失败';
                exit(json_encode($result));
            }

            //如果是模板安装，将模板的页面路径配置写入page_config表
            if($current_task['system_part_type'] == 3)
            {
                Model_Templet::setup_templet_page($insertId);
            }

            $result['status'] = 1;
            $result['msg'] = '安装成功';
            echo json_encode($result);
        } else
        {
            $result['msg'] = '安装数据写入失败';
            echo json_encode($result);
        }

    }

    private function get_online_installer($current_task)
    {
        $result['status'] = 0;

        if (empty($current_task["app_number"]))
        {
            $result["msg"] = "不是正确的安装任务";
            return $result;
        }

        $appapi_model = new Model_AppApi();
        if ($appapi_model->is_app_installed($current_task["app_number"]))
        {
            $result['status'] = 2;
            $result["msg"] = "已安装";
            return $result;
        }

        $upgrade3api_model = new Model_Upgrade3Api();
        $upgrade3api_model->initialise(array(
            'pcode' => $current_task["upgrade_code"],
            'cVersion' => '0.0.0.0'
        ));
        $down_apply_result = $upgrade3api_model->downloadInstaller();
        if (!$down_apply_result['Success'])
        {
            $result['msg'] = $down_apply_result['Message'];
            return $result;
        }

        $download_and_unzip_result = Model_Upgrade3::download_and_unzip($down_apply_result['Data']);
        if ($download_and_unzip_result["status"] != 1)
        {
            $result['msg'] = $download_and_unzip_result['msg'];
            return $result;
        }

        $result['status'] = 1;
        $result['data'] = $download_and_unzip_result["unzippath"];
        return $result;

    }

    private function get_local_installer($current_task)
    {
        $result['status'] = 0;

        $download_and_unzip_result = Model_Upgrade3::download_and_unzip($current_task['installer_url']);
        if ($download_and_unzip_result["status"] != 1)
        {
            $result['msg'] = $download_and_unzip_result['msg'];
            return $result;
        }

        $result['status'] = 1;
        $result['data'] = $download_and_unzip_result["unzippath"];
        return $result;

    }


    public function action_uninstall()
    {
        //$this->send_task_data();
        $this->display('stourtravel/upgrade/uninstall');
    }

    public function action_ajax_exec_uninstall()
    {
        set_time_limit(0);
        $result['status'] = 0;

        $current_task = Arr::get($_POST, 'task');
        if (empty($current_task))
        {
            $result["msg"] = "没有可执行的任务";
            exit(json_encode($result));
        }

        $app_config_install_dir = Model_Upgrade3::get_app_install_path($current_task["system_part_code"]);
        if (!file_exists($app_config_install_dir))
        {
            $result["msg"] = "安装配置文件目录不存在";
            exit(json_encode($result));
        }

        $app_data_uninstall_file_list = array();
        $app_file_uninstall_file_list = array();

        $handler = opendir($app_config_install_dir);
        while ($file = readdir($handler))
        {
            if (in_array($file, array('.', '..')))
            {
                continue;
            }

            $file_full_path = realpath($app_config_install_dir . '/' . $file);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $version_no_tmparr = explode(".", pathinfo($file, PATHINFO_BASENAME));
            unset($version_no_tmparr[count($version_no_tmparr) - 1]);
            $version_no = implode('', $version_no_tmparr);
            if (preg_match("/^\\d+$/", $version_no))
            {
                switch ($extension)
                {
                    case 'txt':
                        $app_file_uninstall_file_list[$version_no] = $file_full_path;
                        break;
                    case 'php':
                        $app_data_uninstall_file_list[$version_no] = $file_full_path;
                        break;
                }
            }
        }
        closedir($handler);

        //卸载SQL
        if (!empty($app_data_uninstall_file_list))
        {
            krsort($app_data_uninstall_file_list, SORT_NUMERIC);
            foreach ($app_data_uninstall_file_list as $k => $v)
            {
                $url = str_ireplace(BASEPATH, $GLOBALS['cfg_basehost'], $v);
                $flag = Common::http($url);
                if ($flag === false || strlen($flag) > 6)
                {
                    $result['msg'] = '数据卸载失败' . $flag;
                    exit(json_encode($result));
                }
            }
        }

        //卸载文件
        if (!empty($app_file_uninstall_file_list))
        {
            $dirs = array();
            krsort($app_file_uninstall_file_list, SORT_NUMERIC);
            //删除文件
            foreach ($app_file_uninstall_file_list as $k => $v)
            {
                $file_handler = fopen($v, 'r');
                while (!feof($file_handler))
                {
                    $file_line = trim(fgets($file_handler));
                    if (empty($file_line) || Model_Upgrade3::is_install_config_file($file_line))
                        continue;

                    $file = realpath(BASEPATH . '/' . $file_line);
                    if (file_exists($file))
                    {
                        if (is_dir($file))
                            $dirs = array_merge($dirs, Model_Upgrade3::get_parent_dir($file));
                        else
                        {
                            $dirs = array_merge($dirs, Model_Upgrade3::get_parent_dir(dirname($file)));
                            unlink($file);
                        }
                    }
                }
                fclose($file_handler);
            }

            //删除目录
            $dirs = array_unique($dirs);
            usort($dirs, array($this,"cmp_path_depth"));
            foreach ($dirs as $v)
            {
                if (file_exists($v))
                {
                    rmdir($v);
                }
            }
        }

        //删除应用目录
        $app_config_install_dir_del_result = Common::rrmdir($app_config_install_dir);
        if ($app_config_install_dir_del_result['success'] == false)
        {
            $result['msg'] = '安装配置文件目录删除失败，' . $app_config_install_dir_del_result["errormsg"];
            exit(json_encode($result));
        }

        //清除数据库记录
        $appapi_model = new Model_AppApi();
        $appapi_model->unsetup_app_install_data($current_task["system_part_code"]);

        $result['status'] = 1;
        $result["msg"] = "卸载成功";
        echo json_encode($result);
    }

    public function action_patch_list()
    {
        $system_part_code =  $this->params['system_part_code'];
        if (empty($system_part_code))
        {
            exit("不正确的请求参数");
        }

        $this->assign("system_part_code", $system_part_code);
        $this->display('stourtravel/upgrade/patchlist');
    }

    public function action_ajax_get_patch_list()
    {
        set_time_limit(0);
        $result['status'] = 0;

        $system_part_code = Arr::get($_POST, 'system_part_code');
        if (empty($system_part_code))
        {
            $result["msg"] = "不正确的请求参数";
            exit(json_encode($result));
        }
        $system_part_list = Model_Upgrade3::get_system_part_list(array($system_part_code));
        if (Model_Upgrade3::is_valid_system_part($system_part_code, $system_part_list) == false)
        {
            $result["msg"] = "加载系统组件信息失败";
            exit(json_encode($result));
        }

        $patch_list = Model_Upgrade3::get_patch_list($system_part_list[$system_part_code], 60);
        if (!is_array($patch_list))
        {
            $result["msg"] = $patch_list;
            exit(json_encode($result));
        }

        $result['status'] = 1;
        $result['data'] = $patch_list;
        $result['current_version'] = $system_part_list[$system_part_code]['cVersion'];
        echo json_encode($result);
    }

    public function action_betaupgrade()
    {
        //$this->send_task_data();
        $this->assign("is_betaupgrade", "1");
        $this->display('stourtravel/upgrade/upgrade');
    }

    public function action_upgrade()
    {
        //$this->send_task_data();
        $this->assign("is_betaupgrade", "0");
        $this->display('stourtravel/upgrade/upgrade');
    }

    public function action_ajax_exec_upgrade_task()
    {
        set_time_limit(0);
        $result['status'] = 0;

        $current_task = Arr::get($_POST, 'task');
        $is_betaupgrade = Arr::get($_POST, 'is_betaupgrade');

        if (empty($current_task))
        {
            $result["msg"] = "没有可执行的任务";
            exit(json_encode($result));
        }
        $system_part_list = Model_Upgrade3::get_system_part_list(array($current_task["system_part_code"]));
        if (Model_Upgrade3::is_valid_system_part($current_task["system_part_code"], $system_part_list) == false)
        {
            $result["msg"] = "加载系统组件信息失败";
            exit(json_encode($result));
        }

        //非内核系统升级，检查依赖性
        if ($current_task["system_part_code"] != Model_SystemParts::$coreSystemPartCode)
        {
            if (Model_Upgrade3::exists_noupgrade_in_coresystem($is_betaupgrade ? 3 : 1))
            {
                $error_msg = "基础系统存在未执行的" . ($is_betaupgrade ? '公测更新' : '正式更新') . "，需要先执行完这些更新才能继续此操作。";
                $core_system_upgrade_link = "<br/><a class=\"sure\" href=\"javascript:;\" onclick=\"javascript:ST.Util.addTab('升级管理', '".URL::site()."systemparts/upgrade_manager/menuid/192');ST.Util.closeBox();\">执行基础系统更新</a>";
                $result["msg"] = "{$error_msg}&nbsp;{$core_system_upgrade_link}";
                exit(json_encode($result));
            }
        }

        $new_patch_list = Model_Upgrade3::get_new_patch_list($system_part_list[$current_task["system_part_code"]], $is_betaupgrade);
        if (!is_array($new_patch_list))
        {
            $result["msg"] = $new_patch_list;
            exit(json_encode($result));
        }

        if (Model_Upgrade3::upgrade_data_library($new_patch_list) <= 0)
        {
            $result["msg"] = "存储升级包数据信息失败";
            exit(json_encode($result));
        }

        foreach ($new_patch_list as &$new_patch)
        {
            unset($new_patch["Url"]);
        }


        //基础系统有重大版本升级需要用户确认
        $current_major_version = explode(".", $system_part_list[$current_task["system_part_code"]]['cVersion']);
        $current_major_version = $current_major_version[0];
        $newest_major_version = explode(".", $new_patch_list[count($new_patch_list) - 1]['Version']);
        $newest_major_version = $newest_major_version[0];
        if ($current_task["system_part_code"] == Model_SystemParts::$coreSystemPartCode && $current_major_version != $newest_major_version)
        {
            $result["msg"] = "major_upgrade_confirm";
        } else
        {
            $result['status'] = 1;
        }

        $result['data'] = $new_patch_list;
        echo json_encode($result);
    }

    public function action_ajax_exec_upgrade_patch()
    {
        set_time_limit(0);
        $result['status'] = 0;

        $current_task = Arr::get($_POST, 'task');
        if (empty($current_task))
        {
            $result["msg"] = "没有可执行的任务";
            exit(json_encode($result));
        }
        $system_part_list = Model_Upgrade3::get_system_part_list(array($current_task["system_part_code"]));
        if (Model_Upgrade3::is_valid_system_part($current_task["system_part_code"], $system_part_list) == false)
        {
            $result["msg"] = "加载系统组件信息失败";
            exit(json_encode($result));
        }

        $current_patch = Arr::get($_POST, 'patch');
        if (empty($current_patch))
        {
            $result["msg"] = "没有可执行的升级包";
            exit(json_encode($result));
        }
        $upgrade_patch_data_list = Model_Upgrade3::upgrade_data_library();
        $patch_data = null;
        foreach ($upgrade_patch_data_list as $upgrade_patch_data)
        {
            if ($upgrade_patch_data["Version"] == $current_patch["Version"] &&
                $upgrade_patch_data["ProductCode"] == $current_patch["ProductCode"]
            )
            {
                $patch_data = $upgrade_patch_data;
                break;
            }
        }
        if (empty($patch_data))
        {
            $result["msg"] = "没有找到升级包的原始数据";
            exit(json_encode($result));
        }

        if ($system_part_list[$current_task["system_part_code"]]["cVersion"] == $patch_data["Version"])
        {
            $result["status"] = 1;
            $result["msg"] = "此版本升级包已经被执行";
            exit(json_encode($result));
        }

        $download_and_unzip_result = Model_Upgrade3::download_and_unzip($patch_data['Url']);
        if ($download_and_unzip_result["status"] != 1)
        {
            $result['msg'] = $download_and_unzip_result['msg'];
            exit(json_encode($result));
        }

        $unzippath = $download_and_unzip_result["unzippath"];

        $replace_background_dir_result = Model_Upgrade3::replace_background_dir($unzippath);
        if ($replace_background_dir_result['success'] == false)
        {
            $result['msg'] = '变更后台目录失败,' . $replace_background_dir_result['errormsg'];
            exit(json_encode($result));
        }

        $backup_original_file_result = Model_Upgrade3::backup_original_file($unzippath);
        if ($backup_original_file_result['success'] == false)
        {
            $result['msg'] = '备份文件失败,' . $backup_original_file_result['errormsg'];
            exit(json_encode($result));
        }

        $pick_install_config_result = Model_Upgrade3::pick_install_config_files($unzippath);
        if ($pick_install_config_result === false)
        {
            $result['msg'] = "提取升级安装配置文件失败";
            exit(json_encode($result));
        }

        //执行SQL
        if (file_exists($pick_install_config_result['sql']))
        {
            $url = str_ireplace(BASEPATH, $GLOBALS['cfg_basehost'], $pick_install_config_result['sql']);
            $flag = Common::http($url);
            if ($flag === false || strlen(trim($flag)) > 6)
            {
                $result['msg'] = '升级数据安装失败' . $flag;
                exit(json_encode($result));
            }
        }

        //移动文件
        $upgrade_files_install_result = Common::xCopy($unzippath, BASEPATH, true);
        if ($upgrade_files_install_result['success'] == false)
        {
            //恢复备份文件
            Common::xCopy($backup_original_file_result['backupdir'], BASEPATH, true);

            $result['msg'] = '升级文件安装失败,' . $upgrade_files_install_result['errormsg'];
            exit(json_encode($result));
        }

        //不是系统组件升级，需要提取安装配置文件
        if ($current_task["system_part_type"] != "1")
        {
            if (Model_Upgrade3::setup_install_config_files($unzippath, Model_Upgrade3::get_app_install_path($current_task["system_part_code"]), $patch_data['Version']) == false)
            {
                $result['msg'] = '升级配置文件安装失败';
                exit(json_encode($result));
            }
        }

        //反馈版本信息
        $upgrade3api_model = new Model_Upgrade3Api();
        $upgrade3api_model->regUpgradeStatus($patch_data['Url']);
        
        //写版本
        $pubdate = Common::myDate('Y-m-d', strtotime($patch_data['ReleaseDate']));
        $version = $patch_data['Version'];
        $beta = $patch_data['Status'] != 1 ? 1 : 0;
        if (!Model_Upgrade3Api::rewriteVersion(BASEPATH . '/' .$system_part_list[$current_task["system_part_code"]]['version_path'],
            $system_part_list[$current_task["system_part_code"]]['pcode'], $version, $beta, $pubdate))
        {
            $result['msg'] = '更新本地版本文件失败';
            exit(json_encode($result));
        }

        $result['status'] = 1;
        $result['msg'] = "升级包{$version}执行成功";
        echo json_encode($result);       

    }

    //ajax检测所有系统组件版本与更新( 首页使用)
    public function action_ajax_check_all_systempart_update()
    {
        $appapi_model = new Model_AppApi();

        $upgrade_check_params = array(Model_SystemParts::$coreSystemPartCode);
        $app_install_data_list = array_merge($appapi_model->app_install_data(), $appapi_model->templet_install_data());
        foreach ($app_install_data_list as $app_install_data)
        {
            if ($app_install_data["is_upgrade"] == 1)
                $upgrade_check_params[] = $app_install_data["id"];
        }

        $result = 0;
        $upgrade_check_result = Model_Upgrade3::check_system_part_update($upgrade_check_params);
        foreach ($upgrade_check_result as $upgrade_check_result_key => $upgrade_check_result_value)
        {
            if (is_array($upgrade_check_result_value) && count($upgrade_check_result_value) > 0)
            {
                foreach ($upgrade_check_result_value as $patch_info)
                {
                    if ($patch_info['Status'] == "1")
                    {
                        $result = 1;
                        break;
                    }
                }
            }
        }

        $core_system_part = Model_Upgrade3::get_core_system_part();
        echo json_encode(array('status' => $result, 'core_system_version' => $core_system_part['cVersion']));
    }

    //ajax检测版本权限(正版检测)
    public function action_ajax_check_right()
    {
        $flag = $this->checkLicense();
        echo json_encode(array('status' => $flag));
    }

    //绑定授权ID
    public function action_bind()
    {
        $model = new Model_Upgrade3Api();
        $serial = $model->getSerialnumber();
        $this->assign('licenseid', $serial);
        $this->display('stourtravel/upgrade/bind');
    }

    //绑定ID
    public function action_ajax_bind_license()
    {
        $licenseid = Arr::get($_POST, 'licenseid');
        Model_Upgrade3Api::rewriteLicense($licenseid);

        $flag = $this->checkLicense();
        if ($flag)
        {
            echo json_encode(array('status' => 1, 'msg' => '序列号绑定成功'));
        } else
        {
            echo json_encode(array('status' => 0, 'msg' => '序列号错误'));
        }
    }

    /*
     * 备份数据库
     *
     * */
    public function action_ajax_backup_database()
    {
        set_time_limit(0);

        $back = new Model_Backup();
        $back->backupAll();
        echo json_encode(array('status' => true));
    }


    //检测license是否有效
    private function checkLicense()
    {
        $corepart = Model_Upgrade3::get_core_system_part();
        if ($corepart == null)
            return 0;

        $model = new Model_Upgrade3Api();
        $model->initialise($corepart);
        return $model->checkRightV();
    }

    private function cmp_path_depth($a, $b)
    {
        $adepth = substr_count(str_ireplace("\\", "/", $a), "/");
        $bdepth = substr_count(str_ireplace("\\", "/", $b), "/");
        return $bdepth - $adepth;
    }

}

