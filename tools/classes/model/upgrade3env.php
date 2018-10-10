<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 系统升级与安装环境检测类
 * */

class Model_Upgrade3Env
{
    //检测升级或安装所需要的权限功能环境是否满足需求
    public static function check_environment()
    {
        $result = self::check_database_permissions();
        if ($result['success'] !== true)
        {
            return $result;
        }

        $result = self::check_file_download_and_unzip();
        if ($result['success'] !== true)
        {
            return $result;
        }

        $result = self::check_file_permissions($result['data']);
        if ($result['success'] !== true)
        {
            return $result;
        }

        return $result;
    }

    private static function check_database_permissions()
    {
        $result = array(
            'success' => false,
            'msg' => ''
        );
        //检测数据库权限
        //清除测试表
        $sql = <<<sql
DROP TABLE IF EXISTS `sline_environment_test`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "删除数据表失败，请检查数据库帐户的表删除权限";
            return $result;
        }

        //重新创建测试表
        $sql = <<<sql
CREATE TABLE `sline_environment_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '登陆用户名',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `logintime` int(10) unsigned DEFAULT NULL COMMENT '上次登陆时间',
  `loginip` varchar(255) DEFAULT NULL COMMENT '登陆ip',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='思途升级安装环境测试';
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "创建数据表失败，请检查数据库帐户的表创建权限";
            return $result;
        }

        //添加字段
        $sql = <<<sql
ALTER TABLE `sline_environment_test`
ADD COLUMN `descript`  varchar(500) NULL COMMENT '备注信息' AFTER `loginip`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "添加数据表字段失败，请检查数据库帐户的表结构修改权限";
            return $result;
        }

        //修改字段
        $sql = <<<sql
ALTER TABLE `sline_environment_test`
MODIFY COLUMN `descript`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注信息' AFTER `loginip`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "修改数据表字段失败，请检查数据库帐户的表结构修改权限";
            return $result;
        }

        //添加数据
        $sql = <<<sql
INSERT INTO `sline_environment_test` VALUES ('1', 'netman', 'e10adc3949ba59abbe56e057f20f883e', '1494912622', '127.0.0.1', '权限测试');
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "添加数据失败，请检查数据库帐户的表数据写权限";
            return $result;
        }

        //修改数据
        $sql = <<<sql
UPDATE `sline_environment_test` set `username`='netman1';
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "修改数据失败，请检查数据库帐户的表数据写权限";
            return $result;
        }

        //查询数据
        $sql = <<<sql
SELECT * FROM `sline_environment_test`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "查询数据失败，请检查数据库帐户的表数据查询权限";
            return $result;
        }

        //删除数据
        $sql = <<<sql
DELETE FROM `sline_environment_test`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "删除数据失败，请检查数据库帐户的表数据删除权限";
            return $result;
        }

        //添加表索引
        $sql = <<<sql
ALTER TABLE `sline_environment_test`
ADD INDEX `IDX_username` (`username`) ;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "添加表索引失败，请检查数据库帐户的表索引创建权限";
            return $result;
        }

        //删除表索引
        $sql = <<<sql
ALTER TABLE `sline_environment_test`
DROP INDEX `IDX_username`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "删除表索引失败，请检查数据库帐户的表索引删除权限";
            return $result;
        }

        //创建视图
        $sql = <<<sql
CREATE VIEW `sline_environment_test_view` AS SELECT
	'通用' AS `f_channelname`,
	`id` AS `f_id`,
	`username` AS `f_username`,
	`logintime` AS `f_logintime`,
	`loginip` AS `f_loginip`,
	0 AS `f_status`
FROM
	`sline_environment_test`
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "创建视图失败，请检查数据库帐户的视图创建权限";
            return $result;
        }

        //修改视图
        $sql = <<<sql
ALTER VIEW `sline_environment_test_view` AS SELECT
	'测试' AS `f_channelname`,
	`id` AS `f_id`,
	`username` AS `f_username`,
	`logintime` AS `f_logintime`,
	`loginip` AS `f_loginip`,
	0 AS `f_status`
FROM
	`sline_environment_test`
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "修改视图失败，请检查数据库帐户的视图修改权限";
            return $result;
        }

        //查询视图
        $sql = <<<sql
SELECT * FROM `sline_environment_test_view`
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "查询视图失败，请检查数据库帐户的视图查询权限";
            return $result;
        }

        //删除视图
        $sql = <<<sql
DROP VIEW `sline_environment_test_view`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "删除视图失败，请检查数据库帐户的视图删除权限";
            return $result;
        }

        //删除测试表
        $sql = <<<sql
DROP TABLE IF EXISTS `sline_environment_test`;
sql;
        if (self::exec_sql($sql) !== true)
        {
            $result['msg'] = "删除数据表失败，请检查数据库帐户的表删除权限";
            return $result;
        }

        $result['success'] = true;
        return $result;
    }

    private static function exec_sql($sql)
    {
        try
        {
            DB::query(DataBase::UPDATE, $sql)->execute();
        } catch (Exception $ex)
        {
            return $ex;
        }

        return true;
    }

    private static function check_file_download_and_unzip()
    {
        $result = array(
            'success' => false,
            'msg' => '',
            'data' => ''
        );

        //下载测试升级包并解压
        $upgrade3api_model = new Model_Upgrade3Api();
        $api_url = $upgrade3api_model->api_url;
        $api_url = parse_url($api_url);
        if ($api_url === false)
        {
            $result['msg'] = "升级服务器地址格式不正确";
            return $result;
        }
        $testzippath = "{$api_url['scheme']}://{$api_url['host']}/upgradeenvtest/upgradeenvtest.zip";
        $download_and_unzip_result = Model_Upgrade3::download_and_unzip($testzippath);
        if ($download_and_unzip_result["status"] != 1)
        {
            $result['msg'] = "不能正常的从升级服务器下载升级包并解压，确认本服务器能正常访问{$api_url['host']}以及php zip扩展可用";
            return $result;
        }
        $result['data'] = $download_and_unzip_result["unzippath"];

        $result['success'] = true;
        return $result;
    }

    private static function check_file_permissions($testfolderpath)
    {
        $result = array(
            'success' => false,
            'msg' => ''
        );

        //将解压的测试包分别拷贝到网站的所有目录并删除 ，以证明对网站的文件夹有足够的访问权限
        $testdirlist = array("", "api", "core", "data", "image", "min", "{$GLOBALS['cfg_backdir']}", "payment", "phone"
        , "plugins", "res", "taglib", "tools", "uc_client", "uploads", "usertpl", "v5");

        foreach ($testdirlist as $to)
        {
            if (empty($to))
            {
                $topath = BASEPATH;
            } else
            {
                $topath = BASEPATH . "/" . $to;
            }

            $xcopy_result = Common::xCopy($testfolderpath, $topath, true);
            if ($xcopy_result['success'] == false)
            {
                $result['msg'] = "{$xcopy_result['errormsg']}，请检查目录权限";
                return $result;
            }
            Common::rrmdir("{$topath}/upgradeenvtest");
        }

        $result["success"] = true;
        return $result;
    }
}