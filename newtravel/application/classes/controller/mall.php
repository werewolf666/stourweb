<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Mall extends Stourweb_Controller
{
    private $_app_model;

    //初始化
    public function before()
    {
        parent::before();

        $this->_app_model = new Model_AppApi();

    }

    //商城首页
    public function action_index()
    {
        //获取标签
        $tag = array();
        $getTag = $this->_app_model->get_app_tag_list();
        if ($getTag->status === 1)
        {
            $tag = $getTag->data;
            $tag = Common::struct_to_array($tag);
        }
        //获取数据
        $this->assign('tag', $tag);
        $this->display('stourtravel/mall/index');
    }

    public function action_read()
    {
        //当前页
        $params['page'] = Arr::get($_POST, 'page');
        //分页数
        $params['pageSize'] = Arr::get($_POST, 'size');
        //分类标签
        $type = Arr::get($_POST, 'type');
        if (!empty($type))
        {
            $params['type'] = $type;
        }
        //关键字
        $keywords = Arr::get($_POST, 'keyword');
        if (!empty($keywords))
        {
            $params['keywords'] = $keywords;
        }

        //初始化数据
        $data['success'] = false;
        //当前页
        $data['page'] = $params['page'];
        //分页数
        $data['size'] = $params['pageSize'];

        //获取数据
        $result = $this->_app_model->get_app_list($params);
        if ($result->status === 1)
        {
            $data['count'] = $result->data->count;
            $data['success'] = true;
            $data['app'] = $this->_app_model->app_data_format($result->data->data);
        }
        echo json_encode($data);
    }

    //购买应用
    public function action_ajax_app_buy()
    {
        //应用编号
        $number = Arr::get($_POST, 'number');

        $data['status'] = 0;
        $result = $this->_app_model->app_buy($number);
        if ($result->status === 1)
        {
            $data['status'] = 1;
            $data['isfree'] = $result->isfree;
            $data['url'] = $result->payurl;
        } else
        {
            $data['msg'] = $result->msg;
        }
        echo json_encode($data);
    }

    //应用详情
    public function action_info()
    {
        $number = $this->params['number'];

        $result = $this->_app_model->get_app_info($number);
        $info = array();
        if ($result->status === 1)
        {
            $info = (array)$result->data;
        }
        $this->assign('info', $info);
        $this->display('stourtravel/mall/info');
    }

    //我的应用 view
    public function action_app()
    {
        Common::check_right('',true);
        $app_install_data = $this->_app_model->app_install_data();
        if (is_array($app_install_data) && count($app_install_data) > 0)
        {
            $system_part_code_list = array();
            foreach ($app_install_data as $app_install)
            {
                if (!in_array($app_install['number'], Model_Upgrade3::$SPECIAL_APP_NUMBERS))
                {
                    $system_part_code_list[] = $app_install["id"];
                }
            }
            $upgradeapi_model = new Model_Upgrade3Api();
            $upgradeapi_model->releaseFeedback(Model_Upgrade3::get_system_part_list($system_part_code_list));
        }

        $app_type_list = array();
        $result = $this->_app_model->get_app_type_list();
        if ($result->status === 1)
        {
            $app_type_list = $result->data;
        }
        $this->assign("app_type_list", $app_type_list);
        $this->assign("app_status_list", Model_AppApi::$APPSTATUSLIST);
        $this->display('stourtravel/mall/app');
    }

    //我的应用
    public function action_ajax_app_read()
    {
        //当前页
        $params['page'] = Arr::get($_GET, 'page');
        //分页数
        $params['pageSize'] = Arr::get($_GET, 'limit');
        //分类
        $app_type = Arr::get($_GET, 'app_type');
        if (!empty($app_type))
        {
            $params['app_type'] = $app_type;
        }
        //关键字
        $keywords = Arr::get($_GET, 'searchkey');
        if (!empty($keywords))
        {
            $params['keywords'] = $keywords;
        }

        $result = $this->_app_model->get_my_app_list($params);
        $data = array('success' => false, 'total' => 0);
        if ($result->status === 1)
        {
            $data['success'] = true;
            $data['total'] = $result->data->count;
            $data['app'] = $this->_app_model->app_data_format($result->data->data, true);
            $app_status = Arr::get($_GET, "app_status");
            if ($app_status != "")
            {
                $applist = $data['app'];
                $data['app'] = array();
                foreach ($applist as $app)
                {
                    if ($app_status == 1)
                    {
                        if ($app["appStatus"] >= $app_status)
                            $data['app'][] = $app;
                    } else
                    {
                        if ($app["appStatus"] == $app_status)
                            $data['app'][] = $app;
                    }

                }
            }

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
        $data['msg'] = '设置应用升级状态成功';

        echo json_encode($data);
    }

}