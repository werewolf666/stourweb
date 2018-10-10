<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 模型管理
 * Class Model
 */
class Model_Model extends ORM
{
    private static $_uncommentable_typeids=array(10,11,14,109,111);
    /**
     * @function 获取模型信息
     * @param $typeid
     * @return array
     */
    public static function get_module_info($typeid)
    {
        $row = ORM::factory('model', $typeid)->as_array();
        return $row;
    }

    /**
     * @function 获取当前模型名称
     * @param $typeid
     * @return mixed
     */
    public static function get_module_name($typeid)
    {
        $rs = DB::select('modulename')->from('model')->where('id', '=', $typeid)->execute()->current();
        return $rs['modulename'];
    }

    /**
     * @function 获取产品属性表
     * @param $typeid
     * @return null
     */
    public static function get_attr_table($typeid)
    {
        $row = ORM::factory('model', $typeid)->as_array();
        return $row['attrtable'] ? $row['attrtable'] : null;
    }

    /**
     * @function 获取扩展表名称
     * @param $typeid
     * @return mixed
     */
    public static function get_extend_table($typeid)
    {
        $row = ORM::factory('model', $typeid)->as_array();
        return $row['addtable'];

    }

    ///*********************** PC端开始  ****************************///

    /**
     * @function 查询指定模型
     * @param $id
     * @param null $field
     * @return mixed
     */
    public static function all_model($id,$field=null)
    {
        $sql = "SELECT * FROM sline_model ";
        $sql .= "WHERE id={$id}";
        $arr = DB::query(1, $sql)->execute()->current();
        return is_null($field)?$arr:$arr[$field];
    }

    /**
     * @function 获取通用模型
     * @return mixed
     */
    public static function tongyoug_model()
    {
        $sql = "SELECT * FROM sline_model ";
        $sql .= "WHERE issystem=0";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }
    /**
     * @function 获取可搜索的模块
     * @return mixed
     *  新增 游记 结伴
     */
    public static function get_search_model()
    {
        $sql = "SELECT a.typeid,a.shortname,a.url,b.pinyin,b.correct,b.issystem FROM `sline_nav` a ";
        $sql.= "INNER JOIN `sline_model` b ON a.typeid=b.id ";
        $sql.= "WHERE a.isopen=1 AND a.webid=0 AND a.linktype=1 ";
        $sql.= "AND a.typeid not in(7,9,10,12,14,105) ";
        $sql.= "ORDER BY a.displayorder ASC";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as &$r)
        {
            $r['modulename'] = $r['shortname'];
            $r['id'] = $r['typeid'];
        }
        return $arr;
    }


    /**
     * @function 获取可搜索的模块
     * @return mixed
     *  新增 游记 结伴
     */
    public static function get_wap_search_model()
    {
        $sql = "SELECT a.typeid,a.shortname,a.url,b.pinyin,b.correct,b.issystem FROM `sline_nav` a ";
        $sql.= "INNER JOIN `sline_model` b ON a.typeid=b.id left join sline_m_nav as c on a.typeid=c.m_typeid ";
        $sql.= "WHERE c.m_isopen=1 AND a.webid=0 AND a.linktype=1 ";
        $sql.= "AND a.typeid not in(7,9,10,12,14,105) ";
        $sql.= "ORDER BY c.m_displayorder ASC";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as &$r)
        {
            $r['modulename'] = $r['shortname'];
            $r['id'] = $r['typeid'];
        }
        return $arr;
    }



    /**
     * @function 获取可使用的模块，订单中心使用
     * @return mixed
     */
    public static function get_used_model()
    {
        $sql = "SELECT a.typeid,a.shortname,a.url,b.pinyin,b.correct,b.issystem FROM `sline_nav` a ";
        $sql.= "INNER JOIN `sline_model` b ON a.typeid=b.id ";
        $sql.= "WHERE a.webid=0 AND a.linktype=1 ";
        //$sql.= "AND a.typeid not in(4,6,7,9,10,11,12,101) ";
        $sql.= "ORDER BY a.displayorder ASC";
        $arr = DB::query(1,$sql)->execute()->as_array();
        $out = array();
        foreach($arr as &$r)
        {
           if(self::is_orderable($r['typeid']))
           {
               $r['modulename'] = $r['shortname'];
               $r['id'] = $r['typeid'];
               $r['is_install_model'] = self::is_install_model($r['typeid']);
               $out[] = $r;
           }
        }
        return $out;

    }

    /**
     * @function 检测模块是否可以安装
     * @param $typeid
     * @return int
     */
    public static function is_install_model($typeid)
    {
        $pinyin = DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
        $product_code = 'stourwebcms_app_system_'.$pinyin;
        $product = DB::select('status')->from('app')->where('productcode','=',$product_code)->execute()->current();
        return $product['status']==1 ? 1 : 0;

    }

    ////************************ PC端结束  **************************////

    ///********************  手机端开始   *******************///


    /**
     * @function 检测模型是否存在
     * @param $pinyin
     * @return bool
     */
    public static function exsits_model($pinyin){
        $sql = "SELECT * FROM sline_model ";
        $sql .= "WHERE pinyin='{$pinyin}'";
        $arr = DB::query(1, $sql)->execute()->current();
        return !empty($arr)?ture:false;
    }


    /**
     * @function 在栏目中全局查询产品信息
     * @param $modelid
     * @param $productautoid
     * @param $fields
     * @return null
     */
    public static function get_product_bymodel($modelid, $productautoid, $fields)
    {
        $modelrow = Model_Model::all_model($modelid);
        if ($modelrow == null)
            return null;

        $fieldsarr = explode(",", $fields);
        foreach ($fieldsarr as $fieldname)
        {
            $sql = "show columns from `sline_{$modelrow['maintable']}` like '{$fieldname}'";
            if (count(DB::query(1, $sql)->execute()->as_array()) <= 0)
                return null;
        }

        $sql = "select {$fields} from sline_{$modelrow['maintable']} where id={$productautoid}";
        return DB::query(1, $sql)->execute()->as_array();
    }

    ///***********************手机端结束 ************************* ///

    ///***********************  后台开始  *************************///

    /**
     * @function 创建模型
     * @param $arr
     * @return int
     */
    public static function createModel($arr)
    {
        $flag = 0;
        if (!self::checkWriteRight()) return $flag;//检测是否有权限
        $modulename = $arr['modulename'];
        $pinyin = $arr['pinyin'];
        $addtable = $pinyin . '_extend_field';
        $modelId = self::getLastId();
        $sql = "insert into sline_model(id,modulename,pinyin,maintable,addtable,issystem)";
        $sql .= "values($modelId,'$modulename','$pinyin','model_archive','$addtable',0)";


        $status = DB::query(2, $sql)->execute();
        $typeid = $status[0];//typeid
        if ($typeid)
        {
            //后端创建表数据
            self::createTupianContent($typeid);
            self::createAddTable($pinyin, $modulename);
            self::createPageTemplet($pinyin, $modulename);
            self::createRightModule($typeid);
            self::createDestTable($pinyin);
            self::createNavItem($typeid, $pinyin, $modulename);
            self::createAdminMenu($typeid, $pinyin, $modulename);

            self::create_jifen($typeid,$modulename,$pinyin);

            //前端创建相应操作


            //如果PC4.1版本存在

            if (is_dir(BASEPATH . '/include'))
            {
                self::createModuleDir($typeid, $pinyin, $modulename);
                self::writeHtaccess($pinyin, $modulename);
            }
            //如果PC5.0版本存在
            if (is_dir(BASEPATH . '/v5'))
            {
                self::createTyFor5($typeid, $pinyin, $modulename);
            }

            //如果mobile5.0版本存在
            if (is_dir(BASEPATH . '/phone'))
            {
                self::createTyForMobile($typeid, $pinyin, $modulename);
            }


            $flag = 1;
        }
        return $flag;
    }


    /**
     * @function 获取最新模块ID，即200以后的模块
     * @return int
     */
    public static function getLastId()
    {

        $sql = "SHOW TABLE STATUS WHERE `name` = 'sline_model'";
        $table = DB::query(1,$sql)->execute()->current();
        return intval($table['Auto_increment'] < 201) ? 201 : intval($table['Auto_increment']);
    }

    /**
     * @function 创建通用产品内容的 图片项
     * @param $typeid
     */
    public static function createTupianContent($typeid)
    {
        DB::query(null, "insert into sline_model_content(webid,typeid,columnname,chinesename,issystem,isopen,isrealfield) values(0,$typeid,'tupian','图片',1,1,0)")->execute();
    }

    /**
     * @function 创建模型附加表
     * @param $pinyin
     * @param $modulename
     */
    public function createAddTable($pinyin, $modulename)
    {
        $sql = "CREATE TABLE `sline_" . $pinyin . "_extend_field` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
            `productid` INT(11) NULL DEFAULT NULL COMMENT '产品id',
            PRIMARY KEY (`id`),
            INDEX `id` (`id`)
        )
        COMMENT='" . $modulename . "字段扩展表'
        COLLATE='utf8_general_ci'
        ENGINE=MyISAM;";
        DB::query(null, $sql)->execute();

    }

    /**
     * @function 创建模型目的地表
     * @param $pinyin
     */
    public function createDestTable($pinyin)
    {
        $sql = "CREATE TABLE `sline_" . $pinyin . "_kindlist` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `kindid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
                `seotitle` VARCHAR(255) NULL DEFAULT NULL,
                `keyword` VARCHAR(255) NULL DEFAULT NULL,
                `description` VARCHAR(255) NULL DEFAULT NULL,
                `tagword` VARCHAR(255) NULL DEFAULT NULL,
                `jieshao` TEXT NULL,
                `isfinishseo` INT(1) UNSIGNED NOT NULL DEFAULT '0',
                `displayorder` INT(4) UNSIGNED NULL DEFAULT '9999',
                `isnav` INT(1) UNSIGNED NULL DEFAULT '0' COMMENT '是否导航',
                `ishot` INT(1) UNSIGNED NULL DEFAULT '0' COMMENT '是否热门',
                `shownum` INT(8) NULL DEFAULT NULL,
                `templetpath` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `kindid` (`kindid`)
            )
            COMMENT='模型目的地表'
            COLLATE='utf8_general_ci'
            ENGINE=MyISAM";
        DB::query(null, $sql)->execute();
    }

    /**
     * @function 创建模型页面模板配置信息
     * @param $pinyin
     * @param $modulename
     */
    public function createPageTemplet($pinyin, $modulename)
    {
        $arr = array(
            array('kindname' => '首页', 'pagename' => 'index'),
            array('kindname' => '列表页', 'pagename' => 'list'),
            array('kindname' => '详细页', 'pagename' => 'show')
        );
        $sql = "insert into sline_page(pid,kindname,pagename) values (0,'$modulename','')";
        $status = DB::query(2, $sql)->execute();
        $pid = $status[0];
        if ($pid)
        {
            foreach ($arr as $row)
            {
                $kindname = $modulename . $row['kindname'];
                $pagename = $pinyin . '_' . $row['pagename'];
                $sql = "insert into sline_page(pid,kindname,pagename) values ('$pid','$kindname','$pagename')";
                DB::query(2, $sql)->execute();
            }
        }

    }

    /**
     * @function 创建模型右侧模块配置信息
     * @param $typeid
     */
    public function createRightModule($typeid)
    {
        $arr = array(
            array('pagename' => '栏目首页', 'shortname' => 'index'),
            array('pagename' => '栏目列表页', 'shortname' => 'search'),
            array('pagename' => '栏目详细页', 'shortname' => 'show')
        );
        foreach ($arr as $row)
        {
            $aid = Common::getLastAid('sline_module_config', 0);
            $sql = "insert into sline_module_config(webid,aid,pagename,shortname,typeid,moduleids)";
            $sql .= "values(0,'$aid','{$row['pagename']}','{$row['shortname']}','$typeid','')";
            DB::query(2, $sql)->execute();
        }


    }

    /**
     * @function 创建模型导航信息到主导航
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function createNavItem($typeid, $pinyin, $modulename)
    {
        //添加nav
        $sql = "insert into sline_nav(webid,typeid,typename,shortname,url,linktype,isopen,issystem) values ";
        $sql .= "('0','$typeid','$modulename','$modulename','/{$pinyin}/',1,1,1)";
        DB::query(2, $sql)->execute();
        //添加 m_nav
        $sql = "insert into sline_m_nav(m_title,m_url,m_typeid,m_isopen,m_issystem) values ";
        $sql .= "('{$modulename}','/{$pinyin}/',{$typeid},1,1)";
        DB::query(2, $sql)->execute();
    }

    /**
     * @function 创建模型后台菜单列表
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function createAdminMenu($typeid, $pinyin, $modulename)
    {
        $pinyin = mysql_real_escape_string($pinyin);
        $modulename = mysql_real_escape_string($modulename);

        $base = array(
            array(1, 1, $typeid, $modulename, '', '', '', '1,2,3', '1', '', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}列表", '', 'tongyong', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}订单", '', 'order', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}咨询", '', 'question', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}评论", '', 'comment', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}配置", '', '', '', '', '1', '', '0', ''),
            array('index_0', 2, $typeid, '短信通知', '', 'noticemanager', 'order_sms', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, '邮件通知', '', 'noticemanager', 'order_email', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, '站内通知', '', 'message', 'order', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '目的地', '', 'destination', 'destination', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '属性分类', '', 'attrid', 'modelattr', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '内容介绍', '', 'attrid', 'content', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '扩展字段', '', 'attrid', 'extendlist', '', '1', '/typeid/{$typeid}', '0', '')
        );

        foreach ($base as $k => &$j)
        {
            $pid = explode("_", $j[0]);
            if ($pid[0] == "index")
                $pid = $base[$pid[1]][11];
            else
                $pid = $j[0];

            $exists_sql = "select * from sline_menu_new where typeid={$j[2]} and level={$j[1]} and title= '{$j[3]}'";
            $findresult = DB::query(DataBase::SELECT, $exists_sql)->execute()->as_array();
            if (count($findresult) <= 0)
            {
                $values = "{$pid}, {$j[1]}, {$j[2]}, '{$j[3]}', '{$j[4]}', '{$j[5]}', '{$j[6]}', '{$j[7]}', {$j[8]}, '{$j[9]}', {$j[10]}";
                $sql = "insert into sline_menu_new (pid, level, typeid, title, directory, controller, method, datainfo, isshow, extparams, extlink) values ({$values});";

                $status = DB::query(DataBase::INSERT, $sql)->execute();
                $pk = $status[0];
            }
            else
            {
                $pk = $findresult[0]['id'];
            }
            $j[11] = $pk;
        }

    }

    /**
     * @function 创建目录,复制控制器到相应目录
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function createModuleDir($typeid, $pinyin, $modulename)
    {
        $destdir = BASEPATH . '/' . $pinyin;
        if (!file_exists($destdir))
        {
            mkdir($destdir);
        }
        $need_file_arr = array('index.php', 'show.php', 'booking.php');
        foreach ($need_file_arr as $file)
        {
            $sourcefile = BASEPATH . '/tongyong/' . $file;
            $destfile = $destdir . '/' . $file;
            copy($sourcefile, $destfile);
        }
        self::writeConfigFile($typeid, $pinyin, $modulename);


    }

    /**
     * @function 写配置文件
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function writeConfigFile($typeid, $pinyin, $modulename)
    {
        $file = BASEPATH . '/' . $pinyin . '/config.php';
        $fp = fopen($file, 'wb');
        flock($fp, 3);
        fwrite($fp, "<?php\r\n");
        fwrite($fp, "\$module_pinyin='" . $pinyin . "';\r\n");
        fwrite($fp, "\$typeid='" . $typeid . "';\r\n");
        fwrite($fp, "\$module_name='" . $modulename . "';\r\n");
        fwrite($fp, "\$module_dest_table='sline_" . $pinyin . "_kindlist';\r\n");
        fwrite($fp, "\$module_extend_table='sline_" . $pinyin . "_extend_field';\r\n");
        fclose($fp);

    }

    /**
     * @function 写伪静态规则
     * @param $pinyin
     * @param $modulename
     */
    public function writeHtaccess($pinyin, $modulename)
    {
        $pc5 = is_dir(BASEPATH . '/application');
        if (!$pc5)
        {
            $file = BASEPATH . '/.htaccess';
            $fp = fopen($file, "a+");
            flock($fp, 3);
            fwrite($fp, "\r\n");
            fwrite($fp, '#' . $modulename . "\r\n");
            fwrite($fp, 'RewriteRule ^' . $pinyin . '/([a-z0-9]+)(/)?$ ' . $pinyin . '/index.php?dest_id=$1' . "\r\n");
            fwrite($fp, 'RewriteRule ^' . $pinyin . '/([a-z0-9]+)-([0-9_]+)?$ ' . $pinyin . '/index.php?dest_id=$1&attrid=$2' . "\r\n");
            fwrite($fp, 'RewriteRule ^' . $pinyin . '/([a-z0-9]+)-([0-9_]+)-([0-9]+)?$ ' . $pinyin . '/index.php?dest_id=$1&attrid=$2&pageno=$3' . "\r\n");
            fwrite($fp, 'RewriteRule ^' . $pinyin . '/show_([0-9]+)+\.html$ ' . $pinyin . '/show.php?aid=$1');
            fclose($fp);
        }


    }

    /**
     * @function 获取当前模型名称
     * @param $typeid
     * @return mixed
     */
    public static function getModuleName($typeid)
    {
        $rs = DB::select('modulename')->from('model')->where('id', '=', $typeid)->execute()->current();
        return $rs['modulename'];
    }

    /**
     * @function 获取模型信息
     * @param $typeid
     * @return array
     */
    public static function getModuleInfo($typeid)
    {
        $row = ORM::factory('model', $typeid)->as_array();
        return $row;
    }

    /**
     * @function 获取扩展表名称
     * @param $typeid
     * @return mixed
     */
    public static function getExtendTable($typeid)
    {
        $row = ORM::factory('model', $typeid)->as_array();
        return $row['addtable'];

    }

    /**
     * @function 获取通用模型
     * @return Array
     */
    public static function getAllModule()
    {
        $arr = ORM::factory('model')->where('isopen=1 and id>14 and issystem=0')->get_all();

        return $arr;
    }

    /**
     * @function 检测是否有写权限
     * @return int
     */
    public function checkWriteRight()
    {

        $flag = 0;
        $filename = BASEPATH . '/stcms.txt';
        $fp = @fopen($filename, 'w');
        if ($fp)
        {

            @fclose($fp);
            @unlink($filename);
            $flag = 1;

        }
        return $flag;
    }

    /**
     * @function 更新导航
     * @param $typeid
     * @param $isopen
     * @throws Kohana_Exception
     */
    public static function updateNav($typeid, $isopen)
    {
        $model = ORM::factory('nav')->where("typeid='$typeid'")->find();
        if ($model->id)
        {
            $model->isopen = $isopen;
            $model->save();

        }

    }


    /**
     * @function 删除模型
     * @param $typdinfo
     * @return int
     */
    public static function deleteModel($typdinfo)
    {
        $flag = 0;
        if (!self::checkWriteRight()) return $flag; //检测是否有权限

        $typeid = $typdinfo->id;
        $modulename = $typdinfo->modulename;
        $pinyin = $typdinfo->pinyin;

        self::deleteTupianContent($typeid);
        self::deleteAddTable($pinyin, $modulename);
        self::deletePageTemplet($pinyin, $modulename);
        self::deleteRightModule($typeid);
        self::deleteDestTable($pinyin);
        self::deleteNavItem($typeid, $pinyin, $modulename);
        self::deleteAdminMenu($typeid, $pinyin, $modulename);
        self::deleteModuleDir($typeid, $pinyin, $modulename);
        self::deleteHtaccess($pinyin, $modulename);

        self::delete_jifen($typeid);
        //pc5.0版本
        self::deleteTyFor5($typeid, $pinyin, $modulename);
        //mobile5.0版本
        self::deleteTyForMobile($typeid, $pinyin, $modulename);
        self::deleteNavItemMobile($typeid, $pinyin, $modulename);
        $sql = "delete from sline_model where id={$typeid}";
        DB::query(Database::DELETE, $sql)->execute();

        $flag = 1;
        return $flag;
    }

    /**
     * @function 删除内容介绍的图片项
     * @param $typeid
     */
    public static function deleteTupianContent($typeid)
    {
        DB::query(null, "delete from sline_model_content where webid=0 and typeid={$typeid}")->execute();
    }

    /**
     * @function 创建模型附加表
     * @param $pinyin
     * @param $modulename
     */
    public function deleteAddTable($pinyin, $modulename)
    {
        $sql = "DROP TABLE IF EXISTS `sline_" . $pinyin . "_extend_field`;";
        DB::query(null, $sql)->execute();
    }

    /**
     * @function 创建模型目的地表
     * @param $pinyin
     */
    public function deleteDestTable($pinyin)
    {
        $sql = "DROP TABLE IF EXISTS `sline_" . $pinyin . "_kindlist`;";
        DB::query(null, $sql)->execute();
    }

    /**
     * @function 创建模型页面模板配置信息
     * @param $pinyin
     * @param $modulename
     */
    public function deletePageTemplet($pinyin, $modulename)
    {
        $sql = "select id from sline_page where pid=0 and kindname='{$modulename}' and pagename=''";
        $status = DB::query(DataBase::SELECT, $sql)->execute()->as_array();
        $id = $status[0]['id'];
        if ($id)
        {
            $sql = "delete from sline_page where pid={$id}";
            DB::query(2, $sql)->execute();

            $sql = "delete from sline_page where id={$id}";
            $status = DB::query(2, $sql)->execute();
        }
    }

    /**
     * @function 创建模型右侧模块配置信息
     * @param $typeid
     */
    public function deleteRightModule($typeid)
    {
        $sql = "delete from sline_module_config where webid=0 and typeid={$typeid}";
        DB::query(2, $sql)->execute();
    }

    /**
     * @function 创建模型导航信息到主导航
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function deleteNavItem($typeid, $pinyin, $modulename)
    {
        $sql = "delete from sline_nav where webid=0 and typeid={$typeid} and typename='{$modulename}'";
        DB::query(2, $sql)->execute();
    }

    /**
     * @function 删除模型后台菜单列表
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function deleteAdminMenu($typeid, $pinyin, $modulename)
    {
        $pinyin = mysql_real_escape_string($pinyin);
        $modulename = mysql_real_escape_string($modulename);

        $base = array(
            array(1, 1, $typeid, $modulename, '', '', '', '1,2,3', '1', '', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}列表", '', 'tongyong', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}订单", '', 'order', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}咨询", '', 'question', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}评论", '', 'comment', 'index', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, "{$modulename}配置", '', '', '', '', '1', '', '0', ''),
            array('index_0', 2, $typeid, '短信通知', '', 'noticemanager', 'order_sms', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_0', 2, $typeid, '邮件通知', '', 'noticemanager', 'order_email', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '目的地', '', 'destination', 'destination', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '属性分类', '', 'attrid', 'modelattr', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '内容介绍', '', 'attrid', 'content', '', '1', '/typeid/{$typeid}', '0', ''),
            array('index_5', 3, $typeid, '扩展字段', '', 'attrid', 'extendlist', '', '1', '/typeid/{$typeid}', '0', '')
        );

        foreach ($base as $k => &$j)
        {
            $sql = "delete from sline_menu_new where typeid={$j[2]} and level={$j[1]} and title= '{$j[3]}'";
            DB::query(DataBase::DELETE, $sql)->execute();
        }

    }

    public function deleteNavItemMobile($typeid, $pinyin, $modulename)
    {
        $sql = "delete from sline_m_nav where m_typeid={$typeid} and m_url='/{$pinyin}/'";
        DB::query(2, $sql)->execute();
    }
    /**
     * @function 删除目录
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function deleteModuleDir($typeid, $pinyin, $modulename)
    {
        $destdir = BASEPATH . '/' . $pinyin;
        if (is_dir($destdir))
        {
            $need_file_arr = array('index.php', 'show.php', 'booking.php', 'config.php', 'func.php');
            foreach ($need_file_arr as $file)
            {
                $destfile = $destdir . '/' . $file;
                unlink($destfile);
            }

            rmdir($destdir);
        }
    }

    /**
     * @function 写伪静态规则
     * @param $pinyin
     * @param $modulename
     */
    public function deleteHtaccess($pinyin, $modulename)
    {

        $file = BASEPATH . '/.htaccess';
        $fp = fopen($file, "r");
        $content = fread($fp, filesize($file));
        fclose($fp);

        $fp = fopen($file, "wb");
        flock($fp, 3);

        $content = str_ireplace('#' . $modulename . "\r\n", '', $content);
        $content = str_ireplace('RewriteRule ^' . $pinyin . '/([a-z0-9]+)(/)?$ ' . $pinyin . '/index.php?dest_id=$1' . "\r\n", '', $content);
        $content = str_ireplace('RewriteRule ^' . $pinyin . '/([a-z0-9]+)-([0-9_]+)?$ ' . $pinyin . '/index.php?dest_id=$1&attrid=$2' . "\r\n", '', $content);
        $content = str_ireplace('RewriteRule ^' . $pinyin . '/([a-z0-9]+)-([0-9_]+)-([0-9]+)?$ ' . $pinyin . '/index.php?dest_id=$1&attrid=$2&pageno=$3' . "\r\n", '', $content);
        $content = str_ireplace('RewriteRule ^' . $pinyin . '/show_([0-9]+)+\.html$ ' . $pinyin . '/show.php?aid=$1', '', $content);

        fwrite($fp, $content);
        fclose($fp);
    }

    /**
     * @function 添加pc5.0通用模块文件
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function createTyFor5($typeid, $pinyin, $modulename)
    {
        $dest_RoutePath = BASEPATH . '/v5/tyroute.php';
        $dest_ControllerPath = BASEPATH . '/v5/classes/controller/' . $pinyin . '.php';

        $tongyong_route_file = APPPATH . 'data/init/tongyongroute5.1.txt';
        $tongyong_controll_file = APPPATH . 'data/init/tongyong5.1.txt';

        //创建控制器
        $file_handle = fopen($tongyong_controll_file, "r");
        $content = "";
        while (!feof($file_handle))
        {
            $content .= fgets($file_handle, 1024);
        }
        $content = str_replace('#pinyin#', $pinyin, $content);
        $content = str_replace('#typeid#', $typeid, $content);
        $content = str_replace('#classname#', ucfirst($pinyin), $content);
        if (!empty($content))
        {
            Common::saveToFile($dest_ControllerPath, $content);
        }

        //创建路由


        $file_handle = fopen($tongyong_route_file, "r");
        $route = "";
        while (!feof($file_handle))
        {
            $route .= fgets($file_handle, 1024);
        }
        $route = str_replace('#pinyin#', $pinyin, "\r\n" . $route);
        if (!empty($route))
        {
            $fp = fopen($dest_RoutePath, "a+");
            flock($fp, 3);
            //@flock($this->open,3);
            $result = fwrite($fp, $route);
            fclose($fp);

        }

    }

    /**
     * @function  删除通用模块路由和文件
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function deleteTyFor5($typeid, $pinyin, $modulename)
    {
        //1.删除控制器
        $dest_ControllerPath = BASEPATH . '/v5/classes/controller/' . $pinyin . '.php';
        $dest_RoutePath = BASEPATH . '/v5/tyroute.php';

        unlink($dest_ControllerPath);

        //删除路由

        $fp = fopen($dest_RoutePath, "r");
        $content = fread($fp, filesize($dest_RoutePath));
        fclose($fp);
        $pattern = '/\/\*\*' . $pinyin . 'start\*\*\/';
        $pattern .= '(.*)';
        $pattern .= '\/\*\*' . $pinyin . 'end\*\*\//si';
        $content = preg_replace($pattern, '', $content);
        Common::saveToFile($dest_RoutePath, $content);


    }

    /**
     * @function  添加手机5.0通用模块文件
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function createTyForMobile($typeid, $pinyin, $modulename)
    {
        $dest_RoutePath = BASEPATH . '/phone/application/tyroute.php';
        $dest_ControllerPath = BASEPATH . '/phone/application/classes/controller/' . $pinyin . '.php';

        $tongyong_route_file = APPPATH . 'data/init/tongyongroute5.1.txt';
        $tongyong_controll_file = APPPATH . 'data/init/tongyong5.1.txt';

        //创建控制器
        $file_handle = fopen($tongyong_controll_file, "r");
        $content = "";
        while (!feof($file_handle))
        {
            $content .= fgets($file_handle, 1024);
        }
        $content = str_replace('#pinyin#', $pinyin, $content);
        $content = str_replace('#typeid#', $typeid, $content);
        $content = str_replace('#classname#', ucfirst($pinyin), $content);
        if (!empty($content))
        {
            Common::saveToFile($dest_ControllerPath, $content);
        }

        //创建路由


        $file_handle = fopen($tongyong_route_file, "r");
        $route = "";
        while (!feof($file_handle))
        {
            $route .= fgets($file_handle, 1024);
        }
        $route = str_replace('#pinyin#', $pinyin, "\r\n" . $route);
        if (!empty($route))
        {
            $fp = fopen($dest_RoutePath, "a+");
            flock($fp, 3);
            //@flock($this->open,3);
            $result = fwrite($fp, $route);
            fclose($fp);

        }

    }

    /**
     * @function 删除通用模块路由和文件
     * @param $typeid
     * @param $pinyin
     * @param $modulename
     */
    public function deleteTyForMobile($typeid, $pinyin, $modulename)
    {
        //1.删除控制器
        $dest_ControllerPath = BASEPATH . '/phone/application/classes/controller/' . $pinyin . '.php';
        $dest_RoutePath = BASEPATH . '/phone/application/tyroute.php';

        unlink($dest_ControllerPath);

        //删除路由

        $fp = fopen($dest_RoutePath, "r");
        $content = fread($fp, filesize($dest_RoutePath));
        fclose($fp);
        $pattern = '/\/\*\*' . $pinyin . 'start\*\*\/';
        $pattern .= '(.*)';
        $pattern .= '\/\*\*' . $pinyin . 'end\*\*\//si';
        $content = preg_replace($pattern, '', $content);
        Common::saveToFile($dest_RoutePath, $content);


    }


    ///********************** 后台结束  ****************************///

    /**
     * @function 判断某产品是否支付评论功能
     * @param $typeid
     * @return bool
     */
    public static  function is_commentable($typeid)
    {

        $is_commentable = DB::select('is_commentable')->from('model')->where('id','=',$typeid)->execute()->get('is_commentable');
        return $is_commentable==1?true:false;
    }


    /**
     * @function 获取不能评论的产品块
     * @return array
     */
    public static function get_uncommentable_typeids()
    {
        $list = DB::select('id')->from('model')->where('is_commentable','=','0')->execute()->as_array();
        $uncommentable_arr = array();
        foreach($list as $row)
        {
            $uncommentable_arr[] = $row['id'];
        }
        return $uncommentable_arr;
    }
    /**
     * @function 判断某产品是否为标准产品
     * @param $typeid
     */
    public static function is_standard_product($typeid)
    {
        $un_standard_typeids = array(14,107);
        if(in_array($typeid,$un_standard_typeids))
            return false;
        return true;
    }

    /**
     * @function 判断某模块是否可以预订
     * @param $typeid
     * @return bool
     */
    public static function is_orderable($typeid)
    {
        $is_orderable = DB::select('is_orderable')->from('model')->where('id','=',$typeid)->execute()->get('is_orderable');
        return $is_orderable==1?true:false;
    }

    /**
     * @function 判断某模块是否用户可以发布
     * @param $typeid
     * @return bool
     */
    public static function is_member_product($typeid)
    {
        $is_publishable = DB::select('is_publishable')->from('model')->where('id','=',$typeid)->execute()->get('is_publishable');
        return $is_publishable==1?true:false;
    }

    /**
     * @function 创建积分相关的配置
     * @param $typeid
     * @param $modulename
     */
    public static function create_jifen($typeid,$modulename,$pinyin)
    {
        //产品预定
        $label = 'sys_book_' .$pinyin;
        $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen')->where('label', '=', $label)->execute()->get('num');
        if ($num <=0)
        {
            $title = $modulename . '产品预订(全局)';
            $model = ORM::factory('jifen');
            $model->issystem = 1;
            $model->isopen = 0;
            $model->typeid = $typeid;
            $model->label = $label;
            $model->title = $title;
            $model->rewardway = 0;
            $model->section = 1;
            $model->frequency_type = 0;
            $model->disable_fields = 'frequency_type';
            $model->save();
        }
        //评论送积分
            $label = 'sys_comment_' .$pinyin;
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen')->where('label', '=', $label)->execute()->get('num');
            if ($num <=0)
            {
                $title =$modulename . '产品评论(全局)';
                $model = ORM::factory('jifen');
                $model->issystem = 1;
                $model->isopen = 0;
                $model->typeid =$typeid;
                $model->label = $label;
                $model->title = $title;
                $model->rewardway = 0;
                $model->section = 2;
                $model->frequency_type = 0;
                $model->disable_fields = 'rewardway,frequency_type';
                $model->save();
          }

        //积分抵现

        $label = 'sys_tprice_' .$pinyin;
        $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen_price')->where('label', '=', $label)->execute()->get('num');
        if ($num <=0)
        {
            $title = $modulename . '产品预订(全局)';
            $model = ORM::factory('jifen_price');
            $model->issystem = 1;
            $model->isopen = 0;
            $model->typeid = $typeid;
            $model->label = $label;
            $model->title = $title;
            $model->expiration_type = 0;
            $model->toplimit = 0;
            $model->save();
        }
    }
    public static function delete_jifen($typeid)
    {
        DB::delete('jifen')->where('typeid','=',$typeid)->and_where('issystem','=',1)->execute();
        DB::delete('jifen_price')->where('typeid','=',$typeid)->and_where('issystem','=',1)->execute();
    }

    //payment
    /**
     * 获取模型拼音标识
     * @param $id
     * @return mixed
     */
    static function pinyin_by_id($id, $ismsg = false)
    {
        $sql = "select pinyin,correct,maintable from sline_model where id={$id}";
        $arr = DB::query(Database::SELECT, $sql)->execute()->current();

        if ($ismsg == true)
        {
            if ($arr['maintable'] == "model_archive")
                return "tongyong";
            else
                return $arr['pinyin'];
        } else
        {
            $py = empty($arr['correct']) ? $arr['pinyin'] : $arr['correct'];
            return $py;
        }

    }

}