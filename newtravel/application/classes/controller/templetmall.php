<?php defined('SYSPATH') or die('No direct script access.');

class Controller_TempletMall extends Stourweb_Controller
{
    private $_app_model;

    //初始化
    public function before()
    {
        parent::before();

        $this->assign('cmsurl', URL::site());
        $this->_app_model = new Model_AppApi();

    }


    //我的模板 view
    public function action_my_templet()
    {
        Common::check_right('',true);
        $templet_install_data = $this->_app_model->templet_install_data();
        if (is_array($templet_install_data) && count($templet_install_data) > 0)
        {
            $system_part_code_list = array();
            foreach ($templet_install_data as $templet_install)
            {
                if (!in_array($templet_install['number'], Model_Upgrade3::$SPECIAL_APP_NUMBERS))
                {
                    $system_part_code_list[] = $templet_install["id"];
                }
            }
            $upgradeapi_model = new Model_Upgrade3Api();
            $upgradeapi_model->releaseFeedback(Model_Upgrade3::get_system_part_list($system_part_code_list));
        }



        $templet_support_site_type_list = array();
        $result = $this->_app_model->get_templet_support_site_type_list();
        if ($result->status === 1)
        {
            $templet_support_site_type_list = $result->data;
        }
        $this->assign("templet_support_site_type_list", $templet_support_site_type_list);

        $templet_support_platform_type_list = array();
        $result = $this->_app_model->get_templet_support_platform_type_list();
        if ($result->status === 1)
        {
            $templet_support_platform_type_list = $result->data;
        }
        $this->assign("templet_support_platform_type_list", $templet_support_platform_type_list);

        $templet_status_list = array();
        $templet_status_list[5] = Model_AppApi::$TEMPLETSTATUSLIST[5];
        $templet_status_list[4] = Model_AppApi::$TEMPLETSTATUSLIST[4];
        $templet_status_list[6] = Model_AppApi::$TEMPLETSTATUSLIST[6];
        $templet_status_list[3] = Model_AppApi::$TEMPLETSTATUSLIST[3];
        $this->assign("templet_status_list", $templet_status_list);

        $this->display('stourtravel/templetmall/templet');
    }

    //我的应用
    public function action_ajax_templet_read()
    {
        //当前页
        $params['page'] = Arr::get($_GET, 'page');
        //分页数
        $params['pageSize'] = Arr::get($_GET, 'limit');
        //分类
        $upgrade_status = Arr::get($_GET, 'upgrade_status');
        if (is_numeric($upgrade_status))
        {
            $params['upgrade_status'] = $upgrade_status;
        }
        $support_site_type = Arr::get($_GET, 'support_site_type');
        if (!empty($support_site_type))
        {
            $params['support_site_type'] = $support_site_type;
        }
        $support_platform_type = Arr::get($_GET, 'support_platform_type');
        if (!empty($support_platform_type))
        {
            $params['support_platform_type'] = $support_platform_type;
        }
        $page_name = Arr::get($_GET, 'page_name');
        if (!empty($page_name))
        {
            $params['page_name'] = $page_name;
        }
        $templet_from = Arr::get($_GET, 'templet_from');
        if (!empty($templet_from))
        {
            $params['templet_from'] = $templet_from;
        }
        //关键字
        $keywords = Arr::get($_GET, 'searchkey');
        if (!empty($keywords))
        {
            $params['searchkey'] = $keywords;
        }

        $result = $this->_app_model->get_my_templet_list();
        $data = array('success' => false, 'total' => 0);
        if ($result->status === 1)
        {
            $result = $this->_app_model->templet_data_format($result->data->data, true, $params);
            $data['success'] = true;
            $data['total'] = $result['total'];
            $data['templet'] = $result['data'];

        }
        echo json_encode($data);
    }

    //设置应用升级状态
    public function action_is_upgrade_set()
    {
        $data['status'] = 0;
        $appid = Arr::get($_POST, 'appid');
        $status = Arr::get($_POST, 'status');

        $this->_app_model->set_upgrade_is_enable($appid, ($status == 1 ? true : false));

        $data['status'] = 1;
        $data['msg'] = '设置模板升级状态成功';

        echo json_encode($data);
    }

    //设置模板是否应用状态
    public function action_is_templet_apply_set()
    {
        $data['status'] = 0;
        $appid = Arr::get($_POST, 'appid');
        $is_apply = Arr::get($_POST, 'is_apply');

        $apply_result = $this->_app_model->set_templet_is_apply($appid, $is_apply);
        if ($apply_result === true)
        {
            $templet_handle_file = Model_Upgrade3::load_templet_handle_file($appid);
            $templet_page_list = array();
            foreach ($templet_handle_file['templet_page_info_list'] as $templet_page_info)
            {
                $templet_page_list[] = array(
                    "platform_code" => $templet_page_info["run_platform"],
                    "pagename" => $templet_page_info["pagename"],
                    "webid" => 0,
                );
            }
            Model_Templet::set_templet_page_advertise_status($templet_page_list);

            $data['status'] = 1;
            $data['msg'] = ($is_apply ? "应用" : "取消应用") . '模板成功';
        } else
            $data['msg'] = $apply_result;

        echo json_encode($data);
    }

}