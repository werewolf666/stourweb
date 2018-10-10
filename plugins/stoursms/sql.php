<?php define('DATAPATH', dirname(dirname(dirname(__FILE__))));
require_once(DATAPATH . "/slinesql.class.php");
//执行sql  $mysql->query
//检测数据 $mysql->check_data  bool
//检测字段 $mysql->check_column bool
//检测表   $mysql->check_table bool
//检测索引 $mysql->check_index bool
//获取错误 $mysql->error() void | string(错误信息)

if(!$mysql->check_table("sline_sms_provider"))
{
    $mysql->query("CREATE TABLE `sline_sms_provider` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '短信供应商名称',
  `config_url` varchar(1000) NOT NULL COMMENT '短信接口配置地址',
  `execute_file` varchar(1000) NOT NULL COMMENT '短信发送功能实现程序文件',
  `execute_classname` varchar(1000) NOT NULL COMMENT '短信发送功能实现程序类名',
  `isopen` int(1) unsigned NOT NULL default '0' COMMENT '是否开启',
  `exdata` longtext COMMENT '短信接口配置扩展数据',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='短信信息配置表';");

    $mysql->error();
}


if(!$mysql->check_data("select * from `sline_sms_provider`  where name='思途短信';")){
    $mysql->query("INSERT INTO `sline_sms_provider` (
    name,
    config_url,
    execute_file,
    execute_classname,
    isopen
)
VALUES
	(
	 '思途短信',
	 '/plugins/stoursms/sms/index',
	 '/plugins/stoursms/application/classes/model/stoursmsprovider.php',
	 'StourSMSProvider',
     '0'
	);");

    $mysql->error();
}
