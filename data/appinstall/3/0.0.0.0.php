<?php
require_once(dirname(__FILE__) . "/handle.php");
//执行sql  $mysql->query
//检测数据 $mysql->check_data  bool
//检测字段 $mysql->check_column boo
//检测表   $mysql->check_table bool
//检测索引 $mysql->check_index bool
//获取错误 $mysql->error() void | string(错误信息)


foreach ($templet_page_info_list as $templet_page_info)
{
    $page_config_table = get_page_config_table($templet_page_info['run_platform']);
    $sql = "delete from {$page_config_table} where path='{$templet_page_info['path']}';";
    $mysql->query($sql);

}

$mysql->query("delete from sline_advertise_5x where remark='{$advertise_templet_id}'");








