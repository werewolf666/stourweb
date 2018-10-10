<?php define('DATAPATH', dirname(dirname(dirname(__FILE__))));
require_once(DATAPATH . "/slinesql.class.php");
//执行sql  $mysql->query
//检测数据 $mysql->check_data  bool
//检测字段 $mysql->check_column boo
//检测表   $mysql->check_table bool
//检测索引 $mysql->check_index bool
//获取错误 $mysql->error() void | string(错误信息)

add_core_data();
add_table();
add_extend_data();
//添加核心数据
function add_core_data()
{
    global $mysql;

    //sline_nav
    if (!$mysql->check_data('select * from sline_nav where typeid=12 and url="/destination/"'))
    {
        $data = array('webid' => 0, 'typeid' => 12, 'pid' => 0, 'shortname' => '目的地', 'seotitle' => '目的地', 'url' => '/destination/', 'linktype' => 1, 'isopen' => 1, 'displayorder' => 12, 'issystem' => 1);
        write_data($data, 'sline_nav');
    }
    //sline_m_nav
    if (!$mysql->check_data('select * from sline_m_nav where m_typeid=12 and m_url="/destination/"'))
    {
        $data = array(
            'm_typeid' => 12,
            'm_title' => '目的地',
            'm_url' => '/destination/',
            'm_isopen' => 1,
            'm_displayorder' => 12,
            'm_issystem' => 1
        );
        write_data($data, 'sline_m_nav');
    }

    //1.写入线路typeid到sline_model
    $result = $mysql->query('select * from sline_model where id=12');
    if ($result)
    {
        $result = $result[0];
        if ($result['pinyin'] != 'line')
        {
            exit('Fatal error: Line typeId is occupied！');
        }
    }
    else
    {
        $data = array('id' => 12, 'modulename' => '目的地', 'pinyin' => 'destination', 'correct' => 'destination', 'maintable' => 'destinations', 'addtable' => '', 'attrtable' => '', 'issystem' => 1, 'isopen' => 1);
        write_data($data, 'sline_model');
    }


    $topPage = $mysql->query('select * from sline_page where pid=0 and kindname="目的地模块"');
    if (!$topPage) {
        $mysql->query("INSERT INTO `sline_page` VALUES (null, '0', '目的地模块', null);");
        $id = $mysql->last_insert_id();
        if (!$id) {
            exit('Fatal error: Sline_Page data write failed');
        }
    }
    else {
        $id = $topPage[0]['id'];
    }
    $data = array(
        array('pid' => $id, 'kindname' => '目的地引导页', 'pagename' => 'dest_boot'),
        array('pid' => $id, 'kindname' => '目的地首页', 'pagename' => 'dest_index')
    );
    foreach ($data as $v) {
        if (!$mysql->check_data("select * from sline_page where pid={$v['pid']} and pagename='{$v['pagename']}'")) {
            write_data($v, 'sline_page');
        }
    }
}

//添加表
function add_table()
{

}

//添加附件数据
function add_extend_data()
{
    global $mysql;
    //sline_nav
    if (!$mysql->check_data('select * from sline_nav where typeid=12 and url="/destination/"')) {
        $data = array('webid' => 0, 'typeid' => 12, 'pid' => 0, 'shortname' => '目的地', 'seotitle' => '', 'url' => '/destination/', 'linktype' => 1, 'isopen' => 1, 'displayorder' => 12, 'issystem' => 1);
        write_data($data, 'sline_nav');
    }

    //sline_m_nav
    if (!$mysql->check_data("select * from sline_m_nav where m_typeid=12 and m_url='/hotels/'")) {
        $data = array(
            'm_typeid' => 12,
            'm_title' => '目的地',
            'm_url' => '/destination/',
            'm_isopen' => 1,
            'm_displayorder' => 12,
            'm_issystem' => 1
        );
        write_data($data, 'sline_m_nav');
    }

    //sline_menu_new
    $menuTop = $mysql->query("select * from sline_menu_new where pid=1 and typeid=12");
    if ($menuTop) {
        $pid = $menuTop[0]['id'];
    }
    else {
        $mysql->query("INSERT INTO `sline_menu_new` (`pid`, `level`, `typeid`, `title`, `directory`, `controller`, `method`, `datainfo`, `isshow`, `displayorder`, `extparams`, `extlink`) VALUES( 1, 1, 12, '目的地', NULL, '', '', '', 1, 9999, NULL, 0)");
        $pid = $mysql->last_insert_id();
    }
    $second = array(
        array('pid' => $pid, 'level' => 2, 'typeid' => 12, 'title' => '目的地设置', 'directory' => 'destination/admin', 'controller' => 'destination', 'method' => 'index', 'isshow' => 1, 'extparams' => ''),
        array('pid' => $pid, 'level' => 2, 'typeid' => 12, 'title' => '开发者', 'directory' => 'destination/admin', 'controller' => 'destination', 'method' => 'developer', 'isshow' => 1, 'extparams' => '/typeid/{$typeid}')
    );
    foreach ($second as $k => $v) {
        $secondResult = $mysql->query("select * from sline_menu_new where pid={$v['pid']} and title='{$v['title']}'");
        if (!$secondResult) {
            if ($k == 4) {

                $secondPid = write_data($v, 'sline_menu_new', true);
            }
            else {
                write_data($v, 'sline_menu_new');
            }
        }
        else {

            if ($k == 4) {
                $secondPid = $secondResult[0]['id'];
            }
            $sql = "update sline_menu_new set `directory`='{$v['directory']}',`controller`='{$v['controller']}',`method`='{$v['method']}',`extparams`='{$v['extparams']}' where pid={$v['pid']} and title='{$v['title']}'";
            $mysql->query($sql);
        }
    }

    $data = array(
        array('webid' => 0, 'flag' => '1', 'custom_label' => 'DestIndexTop', 'adsrc' => 'N;', 'adname' => 'N;', 'adlink' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'dest_index', 'number' => 1, 'position' => '目的地首页顶部', 'size' => '1920*380px', 'remark' => '目的地首页顶部通栏广告'),
        array('webid' => 0, 'flag' => '2', 'custom_label' => 'DestIndexMobile', 'adsrc' => 'N;', 'adname' => 'N;', 'adlink' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '0', 'prefix' => 'dest_index', 'number' => 1, 'position' => '移动端目的地首页顶部', 'size' => '750*320px', 'remark' => '移动端目的地首页广告')
    );
    foreach ($data as $v) {
        if (!$mysql->check_data("select * from sline_advertise_5x where prefix='{$v['prefix']}' and is_system='1' and is_pc='{$v['is_pc']}' and `number` ={$v['number']}")) {
            write_data($v, 'sline_advertise_5x');
        }
    }
}

//写入数据到数据库
function write_data($data, $table, $returnInsertId = false)
{
    global $mysql;
    //格式化数据
    foreach ($data as &$v) {
        if (is_string($v)) {
            $v = "'{$v}'";
        }
        if (is_null($v)) {
            $v = "''";
        }
    }
    $sql = "INSERT INTO `{$table}` (" . implode(',', array_keys($data)) . ") VALUES (" . implode(',', array_values($data)) . ");";
    $mysql->query($sql);
    if ($returnInsertId) {
        return $mysql->last_insert_id();
    }
}

//添加模型到引导模块
$moduleArr = array();
$moduleFile = DATAPATH . str_replace('/', DIRECTORY_SEPARATOR, '/module.php');
if (file_exists($moduleFile)) {
    $moduleArr = include $moduleFile;
}
if (!isset($moduleArr['destination'])) {
    $moduleArr['destination'] = '/plugins/destination';
    file_put_contents($moduleFile, "<?php \r\n" . 'return ' . var_export($moduleArr, true) . ';');
}


//重命名v5,phone目录下destination
$base_path = dirname(DATAPATH) . '';
foreach (array($base_path . '/phone/application/views/default/destination', $base_path . '/v5/views/default/destination') as $v) {
    $v = realpath($v);
    if ($v && !rename($v, preg_replace('~destination$~', '_destination', $v))) {
        exit("目录{$v},没有修改权限");
    }
}

