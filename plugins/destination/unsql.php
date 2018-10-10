<?php define('DATAPATH', dirname(dirname(dirname(__FILE__))));
require_once(DATAPATH . "/slinesql.class.php");
//执行sql  $mysql->query
//检测数据 $mysql->check_data  bool
//检测字段 $mysql->check_column boo
//检测表   $mysql->check_table bool
//检测索引 $mysql->check_index bool
//获取错误 $mysql->error() void | string(错误信息)

delete_data();
//delete_table();
//添加核心数据
function delete_data()
{
    global $mysql;
    $sqls = array(
        //model
        'sline_model' => 'where id=12 and pinyin="destination"',
        //page
        'sline_page' => 'where pagename in ("dest_boot","dest_index") or (pid=0 and kindname="目的地模块")',
        //advertise_5x
        'sline_advertise_5x' => 'where is_system="1" and prefix="dest_index"',
        //sline_menu_new
        'sline_menu_new' => 'where typeid=12',
        //sline_nav
        'sline_nav' => 'where typeid=12',
        //sline_m_nav
        'sline_m_nav' => 'where m_typeid=12'
    );
    foreach ($sqls as $k => $v) {
        $mysql->query("delete from {$k} {$v}");
    }
}

//卸载引导模块
$moduleArr = array();
$moduleFile = DATAPATH . str_replace('/', DIRECTORY_SEPARATOR, '/module.php');

if (file_exists($moduleFile)) {
    $moduleArr = include $moduleFile;
}
if (isset($moduleArr['destination'])) {
    unset($moduleArr['destination']);
    file_put_contents($moduleFile, "<?php \r\n" . 'return ' . var_export($moduleArr, true) . ';');
}
