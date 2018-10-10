<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Templet
{
    public static function setup_templet_page($system_part_code)
    {
        $templet_handle = Model_Upgrade3::load_templet_handle_file($system_part_code);
        $templet_name = $templet_handle['templet_name'];
        $templet_page_info_list = $templet_handle['templet_page_info_list'];
        if (!empty($templet_name) && count($templet_page_info_list) > 0)
        {
            $ex_module_page_list = array();
            for ($i = 0; $i < count($templet_page_info_list); $i++)
            {
                if (stripos($templet_page_info_list[$i]['pagename'], "#ex_module_pinyin#") !== false)
                    $ex_module_page_list[] = $i;
            }

            if (count($ex_module_page_list) > 0)
            {
                $ex_model_info_list = DB::query(DataBase::SELECT, "select * from sline_model where isopen=1 and id>14 and issystem=0")->execute()->as_array();
                foreach ($ex_module_page_list as $ex_module_page_index)
                {
                    foreach ($ex_model_info_list as $ex_model_info)
                    {
                        $templet_page_info_list[] = array(
                            pagename => str_ireplace("#ex_module_pinyin#", $ex_model_info['pinyin'], $templet_page_info_list[$ex_module_page_index]['pagename']),
                            path => $templet_page_info_list[$ex_module_page_index]['path'],
                            run_platform => $templet_page_info_list[$ex_module_page_index]['run_platform']
                        );
                    }
                }
                foreach ($ex_module_page_list as $ex_module_page_index)
                {
                    unset($templet_page_info_list[$ex_module_page_index]);
                }
            }

            foreach ($templet_page_info_list as $templet_page_info)
            {
                $page_list = self::get_page_data($templet_page_info['pagename']);
                if (count($page_list) > 0)
                {
                    $page_config_table = self::get_page_config_table($templet_page_info['run_platform']);
                    if (!empty($page_config_table))
                    {
                        if ($templet_page_info['run_platform'] == "sub_site")
                        {
                            $sub_site_list = DB::query(DataBase::SELECT, "select id from sline_destinations where isopen=1 and iswebsite=1 and weburl<>''")->execute()->as_array();
                            foreach ($sub_site_list as $sub_site)
                            {
                                $page_config = DB::query(DataBase::SELECT, "select * from {$page_config_table} where webid={$sub_site['id']} and pageid={$page_list[0]['id']} and path='{$templet_page_info['path']}'")->execute()->as_array();
                                if (count($page_config) <= 0)
                                {
                                    DB::query(DataBase::INSERT, "insert into {$page_config_table}(webid,pageid,path,isuse) values ({$sub_site['id']},{$page_list[0]['id']},'{$templet_page_info['path']}',0)")->execute();
                                }
                            }
                        } else
                        {
                            $page_config = DB::query(DataBase::SELECT, "select * from {$page_config_table} where pageid={$page_list[0]['id']} and path='{$templet_page_info['path']}'")->execute()->as_array();
                            if (count($page_config) <= 0)
                            {
                                DB::query(DataBase::INSERT, "insert into {$page_config_table}(pageid,path,isuse) values ({$page_list[0]['id']},'{$templet_page_info['path']}',0)")->execute();
                            }
                        }
                    }
                }
            }

        }
    }

    public static function set_templet_page_advertise_status(array $templet_page_list)
    {
        $templet_advertise_prefix = "install_templet_name:";
        $app_model = new Model_AppApi();
        $my_templet_list_result = $app_model->get_my_templet_list();
        $templet_info_list = array();
        if ($my_templet_list_result->status === 1)
        {
            $templet_info_list = $app_model->templet_data_format($my_templet_list_result->data->data, false, array('page' => 1, 'pageSize' => 10000));
        }
        $ex_model_info_list = DB::query(DataBase::SELECT, "select * from sline_model where isopen=1 and id>14 and issystem=0")->execute()->as_array();
        $sub_site_list = DB::query(DataBase::SELECT, "select id from sline_destinations where isopen=1 and iswebsite=1 and weburl<>''")->execute()->as_array();

        foreach ($templet_page_list as $templet_page)
        {
            $divergent_templet_page = array();
            if (stripos($templet_page["pagename"], "#ex_module_pinyin#") !== false)
            {
                foreach ($ex_model_info_list as $ex_model_info)
                {
                    $divergent_templet_page[] = array(
                        "platform_code" => $templet_page["platform_code"],
                        "pagename" => str_ireplace("#ex_module_pinyin#", $ex_model_info['pinyin'], $templet_page["pagename"]),
                        "webid" => $templet_page["webid"]
                    );
                }
            } else
            {
                $divergent_templet_page[] = $templet_page;
            }

            $divergent_templet_page2 = array();
            foreach ($divergent_templet_page as $templet_page)
            {
                if ($templet_page["platform_code"] == "sub_site" && $templet_page["webid"] <= 0)
                {
                    foreach ($sub_site_list as $sub_site)
                    {
                        $divergent_templet_page2[] = array(
                            "platform_code" => $templet_page["platform_code"],
                            "pagename" => $templet_page["pagename"],
                            "webid" => $sub_site['id']
                        );

                    }
                } else
                {
                    $divergent_templet_page2[] = $templet_page;
                }
            }

            foreach ($divergent_templet_page2 as $templet_page)
            {
                $page_config_data = self::get_page_config_data($templet_page["platform_code"], $templet_page["pagename"], $templet_page["webid"]);

                $use_page_path = ""; //默认使用默认页面
                foreach ($page_config_data as $page_config)
                {
                    if ($page_config["isuse"] == 1)
                    {
                        $use_page_path = $page_config["path"];
                        break;
                    }
                }

                if (empty($use_page_path))
                {
                    $open_where_sql = "remark not like '{$templet_advertise_prefix}%' and prefix = '{$templet_page["pagename"]}' ";
                    $close_where_sql = "remark like '{$templet_advertise_prefix}%' and prefix = '{$templet_page["pagename"]}' ";
                } else
                {
                    $templet_installer_name = "";
                    foreach ($templet_info_list["data"] as $templet_info)
                    {
                        if ($templet_installer_name == "")
                        {
                            foreach ($templet_info["handle_pagepath"] as $pagepath)
                            {
                                if ($pagepath == $page_config["path"])
                                {
                                    $templet_installer_name = $templet_info["handle_name"];
                                    break;
                                }
                            }
                        } else
                        {
                            break;
                        }
                    }

                    if ($templet_installer_name != "")
                    {
                        $templet_advertise_name = "{$templet_advertise_prefix}{$templet_installer_name}";
                        $open_where_sql = "remark = '{$templet_advertise_name}' and (prefix = '{$templet_page["pagename"]}' or prefix not in (select pagename from sline_page where pagename<>'')) ";
                        $close_where_sql = "remark <> '{$templet_advertise_name}' and prefix = '{$templet_page["pagename"]}' ";
                    }
                }

                if ($open_where_sql != "" && $close_where_sql != "")
                {
                    $is_pc = ($templet_page["platform_code"] == "wap" ? 0 : 1);
                    $webid = ($templet_page["webid"] > 0 ? $templet_page["webid"] : 0);
                    $where_sql = " and is_pc='{$is_pc}' and webid='{$webid}'";

                    DB::query(DataBase::UPDATE, "update sline_advertise_5x set is_use='1' where {$open_where_sql} {$where_sql}")->execute();
                    DB::query(DataBase::UPDATE, "update sline_advertise_5x set is_use='0' where {$close_where_sql} {$where_sql}")->execute();
                }
            }
        }

    }

    public static function get_page_data($pagename)
    {
        return DB::query(DataBase::SELECT, "select * from sline_page where pagename='{$pagename}'")->execute()->as_array();
    }

    public static function get_page_config_table($platform_code)
    {
        $page_config_table = "";
        if ($platform_code == "pc")
            $page_config_table = "sline_page_config";
        if ($platform_code == "wap")
            $page_config_table = "sline_m_page_config";
        if ($platform_code == "sub_site")
            $page_config_table = "sline_site_page_config";

        return $page_config_table;
    }

    public static function get_page_config_data($platform_code, $pagename, $webid = -1)
    {
        $result = array();
        $page_config_table = self::get_page_config_table($platform_code);
        if (!empty($page_config_table))
        {
            $page_data = self::get_page_data($pagename);
            if (count($page_data) <= 0)
            {
                $pageid = -1;
            } else
            {
                $pageid = $page_data[0]["id"];
            }

            $sql = "select * from {$page_config_table} where pageid={$pageid}";
            if ($webid > 0)
            {
                $sql .= " and webid={$webid}";
            }
            $result = DB::query(DataBase::SELECT, $sql)->execute()->as_array();
        }

        return $result;
    }

    public static function set_use_page_path($platform_code, $pagename, $pagepath, $webid = -1)
    {

        $page_config_table = self::get_page_config_table($platform_code);
        if (!empty($page_config_table))
        {
            $page_data = self::get_page_data($pagename);
            if (count($page_data) <= 0)
            {
                $pageid = -1;
            } else
            {
                $pageid = $page_data[0]["id"];
            }

            $sql = "update {$page_config_table} set isuse=0 where pageid={$pageid}";
            if ($webid > 0)
            {
                $sql .= " and webid={$webid}";
            }
            DB::query(DataBase::UPDATE, $sql)->execute();

            if (!empty($pagepath))
            {
                $sql = "update {$page_config_table} set isuse=1 where pageid={$pageid} and path='{$pagepath}'";
                if ($webid > 0)
                {
                    $sql .= " and webid={$webid}";
                }
                DB::query(DataBase::UPDATE, $sql)->execute();
            }

        }


    }

    public static function delete_page_path($platform_code, $pagename, $pagepath, $webid = -1)
    {

        $page_config_table = self::get_page_config_table($platform_code);
        if (!empty($page_config_table))
        {
            $page_data = self::get_page_data($pagename);
            if (count($page_data) <= 0)
            {
                $pageid = -1;
            } else
            {
                $pageid = $page_data[0]["id"];
            }

            $sql = "delete from {$page_config_table} where pageid={$pageid} and path='{$pagepath}'";
            if ($webid > 0)
            {
                $sql .= " and webid={$webid}";
            }
            DB::query(DataBase::UPDATE, $sql)->execute();

        }


    }

    public static function get_page_module_info()
    {
        $page_list = Common::format_page_name(false);
        return $page_list["mould"];
    }

    public static function get_page_info($pid = 0)
    {
        $page_list = Common::format_page_name(false);
        $page_list = $page_list["page"];
        $page_list = array_values($page_list);
        if (!empty($pid))
        {
            $page_count = count($page_list);
            for ($index = 0; $index < $page_count; $index++)
            {
                if ($page_list[$index]["pid"] != $pid)
                {
                    unset($page_list[$index]);
                }
            }
        }
        $page_list = array_values($page_list);
        return $page_list;
    }

    //解压上传的压缩包.
    public static function process_templet_zipfile($file)
    {
        $result = array("status" => 0, "msg" => "");

        $name = time();
        $storedir = dirname($file) . "/" . $name . "/";

        if (!class_exists("PclZip"))
        {
            include(PUBLICPATH . '/vendor/pclzip.lib.php');
        }
        $archive = new PclZip($file);
        if ($archive->extract(PCLZIP_OPT_PATH, $storedir, PCLZIP_OPT_REPLACE_NEWER) == 0)
        {
            $result["msg"] = "解压模板压缩文件失败，错误：" . $archive->errorInfo(true);
            return $result;

        }

        if (!file_exists($storedir . "sql.php"))
        {
            $result["msg"] = "模板压缩文件中没有sql.php文件";
            return $result;
        }
        if (!file_exists($storedir . "unsql.php"))
        {
            $result["msg"] = "模板压缩文件中没有unsql.php文件";
            return $result;
        }
        if (!file_exists($storedir . "handle.php"))
        {
            $result["msg"] = "模板压缩文件中没有handle.php文件";
            return $result;
        }

        $filemanifest = array();
        self::create_filemanifest($storedir, $filemanifest);
        $filemanifest_content = "";
        foreach ($filemanifest as $filemanifest_item)
        {
            $filemanifest_content .= str_ireplace($storedir, "", $filemanifest_item) . PHP_EOL;
        }
        file_put_contents(dirname($file) . "/FileManifest.txt", $filemanifest_content);

        $archive->add(array(dirname($file) . "/FileManifest.txt"), PCLZIP_OPT_REMOVE_PATH, dirname($file));

        $result["status"] = 1;
        return $result;
    }

    private static function create_filemanifest($parent_path, array &$filemanifest)
    {
        if (is_dir($parent_path))
        {
            $handle = opendir($parent_path);
            if ($handle)
            {
                while ($entry = readdir($handle))
                {
                    if (($entry != ".") && ($entry != ".."))
                    {
                        $fullpath = $parent_path . $entry;
                        if (is_dir($fullpath))
                        {
                            self::create_filemanifest($fullpath . "/", $filemanifest);
                        } else
                        {
                            $filemanifest[] = $fullpath;

                        }
                    }
                }
                closedir($handle);
            }

        }
    }
}