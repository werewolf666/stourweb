<?php defined('SYSPATH') or die('No direct script access.');

class Controller_SystemParts extends Stourweb_Controller
{
    public function before()
    {
        parent::before();
        $this->assign('cmsurl', URL::site());
    }

    /**
     * 图片首页
     */
    public function action_index()
    {
        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
        $this->parentkey = $this->params['parentkey'];
        $this->itemid = $this->params['itemid'];

        $this->assign('pcVersionList', Model_SystemParts::getSystemPart(Model_SystemParts::$pcSystemPartCode));
        $this->assign('mobileVersionList', Model_SystemParts::getSystemPart(Model_SystemParts::$mobileSystemPartCode));

        $configinfo = ORM::factory('sysconfig')->getConfig(0);
        if (!isset($configinfo['cfg_mobile_version']))
        {
            $configinfo['cfg_mobile_version'] = 0;
        }
        if (!isset($configinfo['cfg_pc_version']))
        {
            $configinfo['cfg_pc_version'] = 0;
        }
        if (!isset($configinfo['cfg_pc_upgrade']))
        {
            $configinfo['cfg_pc_upgrade'] = 0;
        }
        if (!isset($configinfo['cfg_mobile_upgrade']))
        {
            $configinfo['cfg_mobile_upgrade'] = 0;
        }
        $this->assign('config', $configinfo);

        $this->display('stourtravel/systemparts/version_manage');
    }

    public function action_ajax_further_processing()
    {
        $out = array('status' => true);

        $sysconfiginfo = ORM::factory('sysconfig')->getConfig(0);

        //检测手机域名是否更改
        if (isset($sysconfiginfo['cfg_m_main_url']))
        {
            //写入mobile 配置
            $file = BASEPATH . '/data/mobile.php';
            $config = include($file);
            $config['domain']['mobile'] = $sysconfiginfo['cfg_m_main_url'];
            $config['domain']['main'] = St_Functions::get_http_prefix() . $_SERVER['HTTP_HOST'];
            $config['delimiterLeft'] = '#mobile start';
            $config['delimiterRight'] = '#mobile end';
            $config['rules'] = '{PHP_EOL}RewriteCond %{HTTP_HOST} ^{host}${PHP_EOL}RewriteCond %{REQUEST_URI} !^/uploads/ {PHP_EOL} RewriteRule (.*) {path}/$1 [L]{PHP_EOL}';
            $config['rulesReplace'] = false;
            $config['version'] = array(
                1 => array(
                    'no' => '6.0',
                    'path' => '/phone/',
                )
            );
            //重写伪静态
            $htFile = BASEPATH . '/.htaccess';
            $content = file_get_contents($htFile);
            if ($config['domain']['mobile'] == $config['domain']['main'])
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
            file_put_contents($htFile, $content);
            file_put_contents($file, '<?php ' . "\r\n" . 'return ' . var_export($config, true) . ';');
        }


        echo json_encode($out);
    }

    public function action_upgrade_manager()
    {
        Common::check_right('',true);
        $upgrade3api_model = new Model_Upgrade3Api();
        $core_system_part = Model_Upgrade3::get_core_system_part();

        $upgrade3api_model->releaseFeedback(array($core_system_part));

        $core_system_data = array();
        if ($core_system_part != null)
        {
            $core_system_data['system_part'] = $core_system_part;
            $core_system_data['system_part']['code'] = Model_SystemParts::$coreSystemPartCode;

            $upgrade3api_model->initialise($core_system_part);
            $get_last_patch_result = $upgrade3api_model->getLastPatch();
            if ($get_last_patch_result['Success'] == true)
            {
                $core_system_data['last_upgrade_info'] = $get_last_patch_result['Data'];
            }

            $core_system_check_update_result = Model_Upgrade3::check_system_part_update(array(Model_SystemParts::$coreSystemPartCode));
            $core_system_data['new_upgrade_info'] = $core_system_check_update_result[Model_SystemParts::$coreSystemPartCode];
        }
        $this->assign('core_system_data', json_encode($core_system_data));


        $app_data_list = array();
        $appapi_model = new Model_AppApi();
        $get_my_app_list_result = $appapi_model->get_my_app_list(array('page' => 1, 'pageSize' => 10000));
        if ($get_my_app_list_result->status === 1)
        {
            $app_list = $appapi_model->app_data_format($get_my_app_list_result->data->data, true);
            foreach ($app_list as $app)
            {
                unset($app['isExistsInstaller']);
                unset($app['author']);
                unset($app['team']);
                unset($app['contributor']);
                unset($app['tag']);
                unset($app['applicationDescription']);
                unset($app['installationInstructions']);
                unset($app['interfacesPic']);
                unset($app['commonProblem']);
                unset($app['otherRemarks']);
                unset($app['litpic']);
                unset($app['summary']);

                $app_data_list[] = array(
                    'app_info' => $app
                );
            }

        }
        $this->assign('app_data_list', json_encode($app_data_list));

        $templet_data_list = array();
        $get_my_templet_list_result = $appapi_model->get_my_templet_list();
        if ($get_my_templet_list_result->status === 1)
        {
            $templet_list = $appapi_model->templet_data_format($get_my_templet_list_result->data->data, true, array('page' => 1, 'pageSize' => 10000));
            foreach ($templet_list["data"] as $templet)
            {
                if (!in_array($templet["update_ID"], Model_Upgrade3::$SPECIAL_APP_NUMBERS))
                {
                    unset($templet['url']);

                    $templet_data_list[] = array(
                        'templet_info' => $templet
                    );
                }
            }

        }
        $this->assign('templet_data_list', json_encode($templet_data_list));

        $this->display('stourtravel/systemparts/upgrade_manager');
    }
//end
}