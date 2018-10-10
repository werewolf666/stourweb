<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Templet extends Stourweb_Controller
{

    private $_app_model;

    /*
     * 模板管理总控制器
     * */
    public function before()
    {
        parent::before();

        $this->assign('cmsurl', URL::site());
        $this->_app_model = new Model_AppApi();
    }

    /*
     * 列表
     * */
    public function action_index()
    {

        $templet_support_platform_type_list = array();
        $result = $this->_app_model->get_templet_support_platform_type_list();
        if ($result->status === 1)
        {
            $templet_support_platform_type_list = $result->data;
        }
        $this->assign("templet_support_platform_type_list", $templet_support_platform_type_list);

        $selected_platform = Arr::get($_GET, "platform") ? Arr::get($_GET, "platform") : 17;
        $this->assign("selected_platform", $selected_platform);

        //pc站
        if ($selected_platform == 17)
        {
            $selected_webid = Arr::get($_GET, "webid") && Arr::get($_GET, "webid") >= 0 ? Arr::get($_GET, "webid") : 0;
            $weblist = array(array("id" => 0, "kindname" => "主站", "weburl" => "", "webroot" => "", "webprefix" => "", "webid" => 0, "webname" => "主站"));
            $weblist = array_merge($weblist, Common::getWebList());
            $this->assign('weblist', $weblist);
        } else
        {
            $selected_webid = -1;
        }
        $this->assign("selected_webid", $selected_webid);

        $my_templet_list_result = $this->_app_model->get_my_templet_list();
        if ($my_templet_list_result->status === 1)
        {
            $templet_info_list = $this->_app_model->templet_data_format($my_templet_list_result->data->data, false, array('page' => 1, 'pageSize' => 10000));
            $this->assign("templet_info_list_json", json_encode($templet_info_list['data']));
        } else
        {
            $this->assign("templet_info_list_json", json_encode(array()));
        }

        $this->display('stourtravel/templet/list');
    }

    public function action_ajax_list()
    {
        $pid = Arr::get($_POST, 'pid');
        $paltform = Arr::get($_POST, 'platform');
        $webid = Arr::get($_POST, 'webid');
        $paltform = $this->to_platform_code($paltform, $webid);
        $pid = explode("-", $pid);
        $level = $pid[0];
        $pid = $pid[1];

        $list = array();
        if ($level == 0) //属性组根
        {
            $page_module_list = Model_Templet::get_page_module_info();
            foreach ($page_module_list as $page_module_info)
            {
                $list[] = array(
                    "id" => "1-" . $page_module_info["id"],
                    "pid" => $pid,
                    "title" => $page_module_info["name"]
                );
            }
        }

        if ($level == 1)
        {
            $page_list = Model_Templet::get_page_info($pid);
            foreach ($page_list as $page_info)
            {
                $list[] = array(
                    "id" => "2-" . $page_info["page_name"],
                    "pid" => $pid,
                    "title" => $page_info["name"]
                );
            }
        }

        if ($level == 2)
        {
            $page_config_data = Model_Templet::get_page_config_data($paltform, $pid, $webid);
            $use_default_templet = 1;
            foreach ($page_config_data as $page_config_data_item)
            {
                $list[] = array(
                    "id" => "3-" . $page_config_data_item["id"],
                    "pid" => $pid,
                    "title" => $page_config_data_item["path"],
                    "pagepath" => $page_config_data_item["path"],
                    "isuse" => $page_config_data_item["isuse"]
                );
                if($page_config_data_item["isuse"] == 1)
                {
                    $use_default_templet = 0;
                }
            }
            $list[] = array(
                "id" => "3-0",
                "pid" => $pid,
                "title" => "标准模板",
                "pagepath" => "",
                "isuse" => $use_default_templet
            );
        }


        echo json_encode(array('status' => 1, 'msg' => '', 'data' => $list));
    }

    public function action_ajax_set_page_templet_use()
    {
        $pagename = Arr::get($_POST, 'pagename');
        $pagepath = Arr::get($_POST, 'pagepath');

        $paltform = Arr::get($_POST, 'platform');
        $webid = Arr::get($_POST, 'webid');
        $paltform = $this->to_platform_code($paltform, $webid);

        Model_Templet::set_use_page_path($paltform, $pagename, $pagepath, $webid);
        Model_Templet::set_templet_page_advertise_status(array(array("platform_code" => $paltform, "pagename" => $pagename, "webid" => $webid)));

        echo json_encode(array('status' => 1, 'msg' => '', 'data' => ""));
    }

    public function action_ajax_delete_page_templet()
    {
        $pagename = Arr::get($_POST, 'pagename');
        $pagepath = Arr::get($_POST, 'pagepath');

        $paltform = Arr::get($_POST, 'platform');
        $webid = Arr::get($_POST, 'webid');
        $paltform = $this->to_platform_code($paltform, $webid);

        Model_Templet::delete_page_path($paltform, $pagename, $pagepath, $webid);

        echo json_encode(array('status' => 1, 'msg' => '', 'data' => ""));
    }

    private function to_platform_code($platform, $webid)
    {
        $paltform = $platform == 17 ? "pc" : "wap";
        if ($webid > 0)
        {
            $paltform = "sub_site";
        }
        return $paltform;
    }

    public function action_upload_templet()
    {
        $this->display('stourtravel/templet/config');
    }

    /*
     * ajax上传模板
     * */
    public function action_ajax_upload_templet()
    {
        $result = array("status" => 0, "msg" => "");

        $uploadtempletfolder = BASEPATH . '/data/uploadtemplet/';
        Common::rrmdir($uploadtempletfolder);

        if (!file_exists($uploadtempletfolder))
        {
            mkdir($uploadtempletfolder, 0777, true);
        }

        $filedata = Arr::get($_FILES, 'filedata');
        if (!preg_match('/^[a-zA-Z0-9_]+\.zip$/is', $filedata['name']))
        {
            $result["msg"] = "上传失败，文件名只能包含英文数字与下划且以.zip结尾";
            echo json_encode($result);
            return;
        }

        $zippath = $uploadtempletfolder . $filedata['name'];
        if (!move_uploaded_file($filedata['tmp_name'], $zippath))
        {
            if (empty($filedata['tmp_name']))
            {
                $upload_max_filesize = ini_get("upload_max_filesize");
                $result["msg"] = "上传失败，您的服务器上传文件大小限制为：{$upload_max_filesize}";
            } else
            {
                $result["msg"] = "上传失败，请注意您网站目录是否有写入权限";
            }

            echo json_encode($result);
            return;
        }

        $result["data"] = $filedata['name'];
        $result["status"] = 1;
        echo json_encode($result);
    }

    /*
     * ajax上传模板
     * */
    public function action_ajax_save_templet()
    {
        $result = array("status" => 0, "msg" => "");

        $uploadtempletfolder = BASEPATH . '/data/uploadtemplet/';
        $filename = Arr::get($_POST, "filename");
        $zippath = $uploadtempletfolder . $filename;

        if (!file_exists($zippath))
        {
            $result["msg"] = "不能找到指定的文件";
            echo json_encode($result);
            return;
        }

        $process_templet_zipfile_result = Model_Templet::process_templet_zipfile($zippath);
        if ($process_templet_zipfile_result["status"] != 1)
        {
            $result["msg"] = $process_templet_zipfile_result["msg"];
            echo json_encode($result);
            return;
        }

        $result["data"] = str_ireplace(BASEPATH, $GLOBALS['cfg_basehost'], $zippath);
        $result["status"] = 1;
        echo json_encode($result);
    }

    public function action_edit_templet_page()
    {
        $model_appapi = new Model_AppApi();
        $templet_install_data = $model_appapi->templet_install_data($this->params["system_part_code"]);
        if (count(templet_install_data) > 0)
        {
            $this->assign("templetname", $templet_install_data[0]["productname"]);
            $this->assign("system_part_code", $this->params["system_part_code"]);
        }
        $this->assign("folder",$this->params["folder"]);
        $this->assign("ismobile",$this->params["ismobile"]);

        $this->display('stourtravel/templet/edit_templet_page');
    }

    public function action_ajax_save_templet_page()
    {
        $result = array("status" => 0, "msg" => "");
        $system_part_code = Arr::get($_POST, "system_part_code");
        $templetname = Arr::get($_POST, "templetname");

        $model_appapi = new Model_AppApi();
        $model_appapi->update_app_install_data(array('id' => $system_part_code, 'productname' => $templetname));

        $result["status"] = 1;
        echo json_encode($result);
    }
}