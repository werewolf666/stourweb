<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_AppApi extends ORM
{
    //产品编号
    private $_pcode;
    //授权域名
    private $_domain;
    //会员ID
    private $_memberId;
    //应用商城接口
    const APPURL = 'http://www.stourweb.com/api/app/get_list';
    //应用类型接口
    const APPTYPEURL = 'http://www.stourweb.com/api/app/get_type';
    //标签接口
    const TAGURL = 'http://www.stourweb.com/api/app/get_tag';
    //应用详情接口
    const INFOURL = 'http://www.stourweb.com/api/app/get_info';
    //我的应用
    const MYAPPURL = 'http://www.stourweb.com/api/app/get_my_app';
    //app状态
    public static $APPSTATUSLIST = array("0" => "未安装", "1" => "已安装", "2" => "禁止升级", "3" => "公测更新", "4" => "正式更新", "5" => "已是最新");


    //获取模板运行平台列表
    const TEMPLET_SUPPORT_PLATFORM_TYPE_URL = 'http://www.stourweb.com/api/templetnew/get_support_platform_type';
    //获取模板安装站列表
    const TEMPLET_SUPPORT_SITE_TYPE_URL = 'http://www.stourweb.com/api/templetnew/get_support_site_type';
    //获取我的授权模板列表
    const MY_TEMPLET_URL = 'http://www.stourweb.com/api/templetnew/get_my_templet';

    //app状态
    public static $TEMPLETSTATUSLIST = array("0" => "暂不能安装","1" => "未安装", "2" => "已安装", "3" => "禁止升级", "4" => "公测更新", "5" => "正式更新", "6" => "已是最新");


    public function __construct()
    {
        $SerialNumber = '';
        include(Kohana::find_file('data', 'license'));
        $code = Model_Upgrade3::get_core_system_part();
        $this->_pcode = $code['pcode'];
        $this->_memberId = $SerialNumber;
        $this->_domain = $_SERVER['HTTP_HOST'];
    }

    public function get_app_tag_list()
    {
        //产品编号
        $common['pcode'] = $this->_pcode;
        return json_decode($this->curl_post_data(self::TAGURL, true, $common));
    }

    public function get_app_type_list()
    {
        //产品编号
        $common['pcode'] = $this->_pcode;
        return json_decode($this->curl_post_data(self::APPTYPEURL, true, $common));
    }

    public function get_app_list(array $conditions)
    {
        //产品编号
        $conditions['pcode'] = $this->_pcode;
        //会员ID
        $conditions['memberId'] = $this->_memberId;
        //域名
        $conditions['domain'] = $this->_domain;
        //获取数据
        return json_decode($this->curl_post_data(self::APPURL, true, $conditions));
    }

    //购买应用
    public function app_buy($number)
    {
        $params = array();
        //产品编号
        $params['pcode'] = $this->_pcode;
        //会员ID
        $params['memberId'] = $this->_memberId;
        //应用编号
        $params['number'] = $number;
        //域名
        $params['domain'] = $this->_domain;

        return json_decode($this->curl_post_data(self::APPBUY, true, $params));
    }

    //应用详情
    public function get_app_info($number)
    {
        $params = array();
        $params['number'] = $number;
        $params['pcode'] = $this->_pcode;
        $params['memberId'] = $this->_memberId;
        $params['domain'] = $this->_domain;
        return json_decode($this->curl_post_data(self::INFOURL, true, $params));
    }

    //我的应用
    public function get_my_app_list(array $conditions)
    {
        $conditions['pcode'] = $this->_pcode;
        $conditions['memberId'] = $this->_memberId;
        $conditions['domain'] = $this->_domain;
        return json_decode($this->curl_post_data(self::MYAPPURL, true, $conditions));
    }

    public function app_install_data()
    {
        return DB::select()->from('app')->where("system_part_type=2")->execute()->as_array();
    }

    /**
     * 格式化数据
     * @param $data
     * @return mixed
     */
    public function app_data_format(array $app_data, $is_check_upgrade = false)
    {
        $app_installed_result = $this->app_install_data();

        $result = array();
        $upgrade_check_params = array();
        $menu_config = Model_Menu_New::set_config();
        foreach ($app_data as $app_data_item)
        {
            $app = (array)$app_data_item;
            $app['id'] = "";
            $app['appStatus'] = 0;
            if ($app['isBuy'] === 1)
            {
                foreach ($app_installed_result as $app_installed_item)
                {
                    if (strtolower($app['number']) == strtolower($app_installed_item['number']))
                    {
                        $app['id'] = $app_installed_item['id'];
                        $app['appStatus'] = 1;

                        $app["app_setup_config"] = Model_Upgrade3::load_app_handle_file($app['id']);
                        $app["app_config_title"] = "";
                        $app["app_config_url"] = "";
                        if (is_array($app["app_setup_config"]["config_menu"]))
                        {
                            if (!empty($app["app_setup_config"]["config_menu"]["extlink"]))
                            {
                                foreach ($menu_config as $menu_config_item)
                                {
                                    if ($menu_config_item["extlink"] == $app["app_setup_config"]["config_menu"]["extlink"])
                                    {
                                        $app["app_config_title"] = $menu_config_item["title"];
                                        $app["app_config_url"] = $menu_config_item["url"];
                                        break;
                                    }
                                }

                            } else
                            {
                                if (!empty($app["app_setup_config"]["config_menu"]["controller"]))
                                {
                                    foreach ($menu_config as $menu_config_item)
                                    {
                                        if ($menu_config_item["directory"] == $app["app_setup_config"]["config_menu"]["directory"] &&
                                            $menu_config_item["controller"] == $app["app_setup_config"]["config_menu"]["controller"] &&
                                            $menu_config_item["method"] == $app["app_setup_config"]["config_menu"]["method"] &&
                                            $menu_config_item["extparams"] == $app["app_setup_config"]["config_menu"]["extparams"]
                                        )
                                        {
                                            $app["app_config_title"] = $menu_config_item["title"];
                                            $app["app_config_url"] = $menu_config_item["url"];
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        if ($app_installed_item["is_upgrade"] == 0)
                        {
                            $app['appStatus'] = 2;
                        } else
                        {
                            $upgrade_check_params[] = $app["id"];
                        }

                        //将服务端的产品名称更新到本地库中进行存储
                        DB::update('app')->set(array('productname' => $app['name']))->where("id", "=", $app['id'])->execute();

                        break;
                    }
                }

            }

            array_push($result, $app);
        }

        if (count($upgrade_check_params) > 0 && $is_check_upgrade)
        {
            $upgrade_check_result = Model_Upgrade3::check_system_part_update($upgrade_check_params);
            foreach ($result as &$app)
            {
                foreach ($upgrade_check_result as $upgrade_check_result_key => $upgrade_check_result_value)
                {
                    if (strtolower($app["id"]) == strtolower($upgrade_check_result_key))
                    {
                        if (is_array($upgrade_check_result_value))
                        {
                            if (count($upgrade_check_result_value) > 0)
                            {
                                if ($upgrade_check_result_value[count($upgrade_check_result_value) - 1]["Status"] == 1)
                                    $app['appStatus'] = 4;
                                else
                                    $app['appStatus'] = 3;
                            } else
                            {
                                $app['appStatus'] = 5;
                            }

                        }

                        break;
                    }
                }
            }
        }

        foreach ($result as &$app)
        {
            $app["appStatusName"] = self::$APPSTATUSLIST[$app['appStatus']];
        }
        return $result;
    }


    public function get_templet_support_platform_type_list()
    {
        //产品编号
        $common['pcode'] = $this->_pcode;
        return json_decode($this->curl_post_data(self::TEMPLET_SUPPORT_PLATFORM_TYPE_URL, true, $common));
    }

    public function get_templet_support_site_type_list()
    {
        //产品编号
        $common['pcode'] = $this->_pcode;
        return json_decode($this->curl_post_data(self::TEMPLET_SUPPORT_SITE_TYPE_URL, true, $common));
    }

    //我的应用
    public function get_my_templet_list()
    {
        $conditions = array();
        $conditions['pcode'] = $this->_pcode;
        $conditions['memberId'] = $this->_memberId;
        $conditions['domain'] = $this->_domain;
        return json_decode($this->curl_post_data(self::MY_TEMPLET_URL, true, $conditions));
    }

    public function templet_install_data($system_part_code = 0)
    {
        $table = DB::select()->from('app')->where("system_part_type=3");
        if (!empty($system_part_code))
        {
            $table->and_where(" and id={$system_part_code}");
        }
        return $table->execute()->as_array();
    }

    public function templet_data_format(array $templet_data, $is_check_upgrade, array $params)
    {
        $templet_installed_result = $this->templet_install_data();

        //在线模板关联本地安装数据
        $result = array();
        $upgrade_check_params = array();
        foreach ($templet_data as $templet_data_item)
        {
            $templet = (array)$templet_data_item;
            $templet['id'] = "";
            $templet['product_attr'] = explode(",", $templet['product_attr']);
            $templet['product_pagename'] = explode(",", $templet['product_pagename']);
            $templet['handle_pagepath'] = array();
            $templet['handle_name'] = "";
            $templet['handle_advertise_name'] = "";
            $templet['from_type'] = "mall_templet";
            if (empty($templet["update_ID"]))
            {
                $templet['appStatus'] = 0;
            } else
            {
                $templet['appStatus'] = 1;

                for ($index = 0; $index < count($templet_installed_result); $index++)
                {
                    $templet_installed_item = $templet_installed_result[$index];
                    if (strtolower($templet['update_ID']) == strtolower($templet_installed_item['number']))
                    {
                        $templet['id'] = $templet_installed_item['id'];
                        $templet['status'] = $templet_installed_item['status'];
                        $templet['appStatus'] = 2;
                        if ($templet_installed_item["is_upgrade"] == 0)
                        {
                            $templet['appStatus'] = 3;
                        } else
                        {
                            $upgrade_check_params[] = $templet["id"];
                        }

                        $templet_handle_file_config = Model_Upgrade3::load_templet_handle_file($templet['id']);
                        foreach ($templet_handle_file_config['templet_page_info_list'] as $templet_page_info)
                        {
                            $templet['handle_pagepath'][] = $templet_page_info["path"];
                        }
                        $templet['handle_name'] = $templet_handle_file_config["templet_name"];
                        $templet['handle_advertise_name'] = $templet_handle_file_config["advertise_templet_id"];

                        unset($templet_installed_result[$index]);
                        $templet_installed_result = array_values($templet_installed_result);
                        //将服务端的产品名称更新到本地库中进行存储
                        DB::update('app')->set(array('productname' => $templet['name']))->where("id", "=", $templet['id'])->execute();

                        break;
                    }
                }
            }
            array_push($result, $templet);
        }

        //未禁止升级的在线模板检测升级状态
        if (count($upgrade_check_params) > 0 && $is_check_upgrade)
        {
            $upgrade_check_result = Model_Upgrade3::check_system_part_update($upgrade_check_params);
            foreach ($result as &$templet)
            {
                foreach ($upgrade_check_result as $upgrade_check_result_key => $upgrade_check_result_value)
                {
                    if (strtolower($templet["id"]) == strtolower($upgrade_check_result_key))
                    {
                        if (is_array($upgrade_check_result_value))
                        {
                            if (count($upgrade_check_result_value) > 0)
                            {
                                if ($upgrade_check_result_value[count($upgrade_check_result_value) - 1]["Status"] == 1)
                                    $templet['appStatus'] = 5;
                                else
                                    $templet['appStatus'] = 4;
                            } else
                            {
                                $templet['appStatus'] = 6;
                            }

                        }

                        break;
                    }
                }
            }
        }
        unset($templet);

        //本地模板数据组装
        $ex_model_info_list = DB::query(DataBase::SELECT, "select * from sline_model where isopen=1 and id>14 and issystem=0")->execute()->as_array();
        for ($index = 0; $index < count($templet_installed_result); $index++)
        {
            $templet_installed_item = $templet_installed_result[$index];

            $templet = array();
            $templet['id'] = $templet_installed_item['id'];
            $templet['name'] = $templet_installed_item['productname'];
            $templet['update_ID'] = $templet_installed_item['number'];
            $templet['from_type'] = in_array($templet_installed_item['number'], Model_Upgrade3::$SPECIAL_APP_NUMBERS) ? $templet_installed_item['number'] : "mall_templet";
            $templet['status'] = $templet_installed_item['status'];
            $templet['appStatus'] = 2;
            $templet['update_ProductCode'] = "";
            $templet['url'] = "";
            $templet_handle_file_config = Model_Upgrade3::load_templet_handle_file($templet['id']);
            $templet['product_attr'] = array();
            $templet['product_pagename'] = array();
            $templet['handle_pagepath'] = array();
            $templet['handle_name'] = "";
            $templet['handle_advertise_name'] = "";
            if (count($templet_handle_file_config['templet_page_info_list']) > 0)
            {
                $run_platform = $templet_handle_file_config['templet_page_info_list'][0]['run_platform'];
                if ($run_platform == "pc" || $run_platform == "sub_site")
                {
                    $templet['product_attr'][] = 17;
                    if ($run_platform == "sub_site")
                    {
                        $templet['product_attr'][] = 40;
                    } else
                    {
                        $templet['product_attr'][] = 39;
                    }
                } else
                {
                    $templet['product_attr'][] = 27;
                    $templet['product_attr'][] = 39;
                }

                foreach ($templet_handle_file_config['templet_page_info_list'] as $templet_page_info)
                {
                    $templet['handle_pagepath'][] = $templet_page_info["path"];
                    if (stripos($templet_page_info["pagename"], "#ex_module_pinyin#") !== false)
                    {
                        foreach ($ex_model_info_list as $ex_model_info)
                        {
                            $templet['product_pagename'][] = str_ireplace("#ex_module_pinyin#", $ex_model_info['pinyin'], $templet_page_info["pagename"]);
                        }
                    } else
                    {
                        $templet['product_pagename'][] = $templet_page_info['pagename'];
                    }
                }

                $templet['handle_name'] = $templet_handle_file_config["templet_name"];
                $templet['handle_advertise_name'] = $templet_handle_file_config["advertise_templet_id"];
            }

            array_push($result, $templet);

        }

        //按条件搜索模板
        $total = count($result);
        for ($index = 0; $index < $total; $index++)
        {
            $templet = $result[$index];

            $isok = true;
            if ($isok && is_numeric($params["upgrade_status"]) && $templet["appStatus"] != $params["upgrade_status"])
            {
                $isok = false;
            }
            if ($isok && !empty($params["support_site_type"]) && !in_array($params["support_site_type"], $templet["product_attr"]))
            {
                $isok = false;
            }
            if ($isok && !empty($params["support_platform_type"]) && !in_array($params["support_platform_type"], $templet["product_attr"]))
            {
                $isok = false;
            }
            if ($isok && !empty($params["page_name"]) && !in_array($params["page_name"], $templet["product_pagename"]))
            {
                $isok = false;
            }
            if ($isok && !empty($params["templet_from"]) && $params["templet_from"] != $templet["from_type"])
            {
                $isok = false;
            }
            if ($isok && !empty($params["searchkey"]) && stripos($templet["name"], $params["searchkey"]) === false)
            {
                $isok = false;
            }

            if (!$isok)
            {
                unset($result[$index]);
            }
        }
        $result = array_values($result);

        //排序结果
        $sort_result_appstatus = array(
            0 => array(),
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => array(),
            6 => array()
        );
        foreach ($result as $item)
        {
            $sort_result_appstatus[$item["appStatus"]][] = $item;
        }
        $result = array_merge(
            $sort_result_appstatus[5],
            $sort_result_appstatus[4],
            $sort_result_appstatus[6],
            $sort_result_appstatus[3],
            $sort_result_appstatus[2],
            $sort_result_appstatus[1],
            $sort_result_appstatus[0]
        );

        //分页
        $start_index = ($params["page"] - 1) * $params["pageSize"];
        $end_index = ($start_index + $params["pageSize"]) - 1;
        $total = count($result);
        for ($index = 0; $index < $total; $index++)
        {
            if ($index < $start_index || $index > $end_index)
            {
                unset($result[$index]);
            }
        }
        $result = array_values($result);

        //将结果数据的相关字段补充完整
        $support_platform_type_list = self::get_templet_support_platform_type_list();
        $support_site_type_list = self::get_templet_support_site_type_list();
        $page_config = Common::format_page_name(false);
        foreach ($result as &$templet)
        {
            $templet["appStatusName"] = self::$TEMPLETSTATUSLIST[$templet['appStatus']];
            $templet["site_name"] = "";
            $templet["platform_name"] = "";
            foreach ($templet["product_attr"] as $product_attr)
            {
                foreach ($support_platform_type_list->data as $support_platform_type)
                {
                    if ($product_attr == $support_platform_type->id)
                    {
                        $templet["platform_name"] .= "{$support_platform_type->title},";
                        break;
                    }
                }
                foreach ($support_site_type_list->data as $support_site_type)
                {
                    if ($product_attr == $support_site_type->id)
                    {
                        $templet["site_name"] .= "{$support_site_type->title},";
                        break;
                    }
                }

            }
            $templet["site_name"] = rtrim($templet["site_name"], ",");
            $templet["platform_name"] = rtrim($templet["platform_name"], ",");

            $templet["page"] = "";
            foreach ($templet["product_pagename"] as $product_pagename)
            {
                foreach ($page_config["page"] as $page_config_item)
                {
                    if ($page_config_item['page_name'] == $product_pagename)
                    {
                        $templet["page"] .= "{$page_config_item['name']},";
                        break;
                    }
                }
            }
            $templet["page"] = rtrim($templet["page"], ",");

            $templet["from"] = "";
            if ($templet["update_ID"] == "custom_templet")
            {
                $templet["from"] = "定制";
            } else if ($templet["update_ID"] == "system_templet")
            {
                $templet["from"] = "默认";
            } else
            {
                $templet["from"] = "商城";
            }

        }
        unset($templet);

        return array("total" => $total, "data" => $result);
    }


    public function setup_app_install_data(array $app_install_task)
    {
        list($insertId, $rows) = DB::insert('app', array('number', 'productcode', 'productname', 'system_part_type'))
            ->values(array($app_install_task["app_number"], $app_install_task['upgrade_code'], $app_install_task['name'], $app_install_task['system_part_type']))
            ->execute();
        return $insertId;
    }

    public function update_app_install_data(array $app_install_data)
    {
        if (!empty($app_install_data['id']))
        {
            $id = $app_install_data['id'];
            unset($app_install_data['id']);

            DB::update("app")->set($app_install_data)->where("id={$id}")->execute();
        }

    }

    public function unsetup_app_install_data($system_part_code)
    {
        DB::delete('app')->where("id={$system_part_code}")->execute();
    }
    /**
     * 判断指定pcode的应用是否已经安装
     * @param $dir
     * @return array
     */
    public function is_app_installed($code_number)
    {
        $result = DB::select()->from('app')->where("productcode='{$code_number}' or number='{$code_number}'")->execute()->as_array();
        return !empty($result);
    }

    public function set_upgrade_is_enable($appid, $is_enable)
    {
        $status = $is_enable ? 1 : 0;
        $sql = "update sline_app set is_upgrade={$status} where id={$appid}";
        DB::query(DATABASE::UPDATE, $sql)->execute();
    }

    public function set_templet_is_apply($appid, $is_apply)
    {
        $app_config_install_dir = Model_Upgrade3::get_app_install_path($appid);
        if (!file_exists($app_config_install_dir))
        {
            return "安装配置文件目录不存在";
        }

        $handle_url = realpath($app_config_install_dir . '/handle.php');
        $handle_url = str_ireplace(BASEPATH, $GLOBALS['cfg_basehost'], $handle_url);

        if ($is_apply)
        {
            $flag = Common::http($handle_url . '?action=apply_templet');
            if ($flag === false || strlen($flag) > 6)
            {
                return "应用模板失败，" . $flag;
            }

            $sql = "update sline_app set status=2 where id={$appid}";
            DB::query(DATABASE::UPDATE, $sql)->execute();
        }
        else
        {
            $flag = Common::http($handle_url . '?action=cancel_apply_templet');
            if ($flag === false || strlen($flag) > 6)
            {
                return "取消应用模板失败，" . $flag;
            }

            $sql = "update sline_app set status=1 where id={$appid}";
            DB::query(DATABASE::UPDATE, $sql)->execute();
        }

        return true;
    }
    /**
     * 获取远程数据
     * @param $url
     * @param bool|true $isPost
     * @param null $data
     * @return mixed
     */
    private function curl_post_data($url, $isPost = false, $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($isPost)
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}