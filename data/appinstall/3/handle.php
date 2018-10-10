<?php
define('DATAPATH', dirname(dirname(dirname(__FILE__))));
require_once(DATAPATH . "/slinesql.class.php");

const TEMPLET_RUN_PLATFORM_PC = "pc";
const TEMPLET_RUN_PLATFORM_WAP = "wap";
const TEMPLET_RUN_PLATFORM_SUBSITE = "sub_site";

//模板名称，如：dsc_st15578_wap_qj
$templet_name = "standard_pc_0_3";

//如果模板支持扩展模块，扩展模块页面信息统一使用 #ex_module_pinyin# 代替，例如 #ex_module_pinyin#_show 将匹配所有扩展模板的show页面
$templet_page_info_list = array(
//array(pagename=>'photo_show',path=>'cl_st15578_m189_photo_show',run_platform=>'TEMPLET_RUN_PLATFORM_PC|TEMPLET_RUN_PLATFORM_WAP|TEMPLET_RUN_PLATFORM_SUBSITE'),

    array(pagename=>'index',path=>'standard_pc_0_3_index',run_platform=>TEMPLET_RUN_PLATFORM_PC),
);

//===========================================================================================================================================================================

//广告位中的模板关联标识
$advertise_templet_id = "install_templet_name:{$templet_name}";

$ex_module_page_list = array();
for ($i = 0; $i < count($templet_page_info_list); $i++)
{
    if (stripos($templet_page_info_list[$i]['pagename'], "#ex_module_pinyin#") !== false)
        $ex_module_page_list[] = $i;
}

if (count($ex_module_page_list) > 0)
{
    $ex_model_info_list = $mysql->query("select * from sline_model where isopen=1 and id>14 and issystem=0");
    foreach ($ex_module_page_list as $ex_module_page_index)
    {
        foreach ($ex_model_info_list as $ex_model_info)
        {
            $templet_page_info_list[] = array(pagename => str_ireplace("#ex_module_pinyin#", $ex_model_info['pinyin'], $templet_page_info_list[$ex_module_page_index]['pagename']), path => $templet_page_info_list[$ex_module_page_index]['path'], run_platform=>$templet_page_info_list[$ex_module_page_index]['run_platform']);
        }
    }
    foreach ($ex_module_page_list as $ex_module_page_index)
    {
        unset($templet_page_info_list[$ex_module_page_index]);
    }
}

if ($_GET["action"] == "apply_templet")
{
    foreach ($templet_page_info_list as $templet_page_info)
    {
        $page_list = $mysql->query("select * from sline_page where pagename='{$templet_page_info['pagename']}'");
        if (count($page_list) > 0)
        {
            $page_config_table = get_page_config_table($templet_page_info['run_platform']);
            if($templet_page_info['run_platform'] == TEMPLET_RUN_PLATFORM_SUBSITE)
            {
                $sub_site_list = $mysql->query("select id from sline_destinations where isopen=1 and iswebsite=1 and weburl<>''");
                foreach($sub_site_list as $sub_site)
                {
					$mysql->query("update {$page_config_table} set isuse=0 where webid={$sub_site['id']} and pageid={$page_list[0]['id']}");
                    if ($mysql->check_data("select * from {$page_config_table} where webid={$sub_site['id']} and pageid={$page_list[0]['id']} and path='{$templet_page_info['path']}'"))
                    {
                        $mysql->query("update {$page_config_table} set isuse=1 where webid={$sub_site['id']} and pageid={$page_list[0]['id']} and path='{$templet_page_info['path']}'");
                    } else
                    {
                        $mysql->query("insert into {$page_config_table}(webid,pageid,path,isuse) values ({$sub_site['id']},{$page_list[0]['id']},'{$templet_page_info['path']}',1)");
                    }
                }
            }
            else
            {
				$mysql->query("update {$page_config_table} set isuse=0 where pageid={$page_list[0]['id']}");
                if ($mysql->check_data("select * from {$page_config_table} where pageid={$page_list[0]['id']} and path='{$templet_page_info['path']}'"))
                {
                    $mysql->query("update {$page_config_table} set isuse=1 where pageid={$page_list[0]['id']} and path='{$templet_page_info['path']}'");
                } else
                {
                    $mysql->query("insert into {$page_config_table}(pageid,path,isuse) values ({$page_list[0]['id']},'{$templet_page_info['path']}',1)");
                }
            }


        }
    }
}

if ($_GET["action"] == "cancel_apply_templet")
{
    foreach ($templet_page_info_list as $templet_page_info)
    {
        $page_config_table = get_page_config_table($templet_page_info['run_platform']);
        $mysql->query("update {$page_config_table} set isuse=0 where path='{$templet_page_info['path']}'");
    }
}

//通过模板
function get_page_config_table($templet_run_platform)
{
    if($templet_run_platform == TEMPLET_RUN_PLATFORM_PC)
        return "sline_page_config";
    if($templet_run_platform == TEMPLET_RUN_PLATFORM_WAP)
        return "sline_m_page_config";
    if($templet_run_platform == TEMPLET_RUN_PLATFORM_SUBSITE)
        return "sline_site_page_config";
    return "";
}







