<?php
/**
 * @version        $Id: index.php 1 13:41 2016年8月19日 netman
 * @package        Stourweb.Install
 * @copyright      Copyright (c) 2008 - 2016, Stourweb, Inc.
 * @link           http://www.stourweb.com
 */

@set_time_limit(0);
//error_reporting(E_ALL);
//error_reporting(E_ALL || ~E_NOTICE);
//ini_set('display_errors',0);


error_reporting(E_ERROR);
$verMsg = ' V7.1';
$dfDbname = 'stourwebcms';
$dblang ='utf8';
$errmsg = '';

define('SLINEDATA',dirname(__FILE__).'/../data');
define('SLINEROOT',preg_replace("#[\\\\\/]install#", '', dirname(__FILE__)));
header("Content-Type: text/html; charset=utf-8");

require_once(SLINEROOT.'/install/install.func.php');


foreach(Array('_GET','_POST','_COOKIE') as $_request)
{
    foreach($$_request as $_k => $_v) ${$_k} = RunMagicQuotes($_v);
}

$insLockfile = dirname(__FILE__).'/install_lock.txt';
if(file_exists($insLockfile))
{
    exit(" 思途CMS程序已运行安装，如果你确定要重新安装，请先从FTP中删除 install/install_lock.txt！");
}

if(empty($step))
{
    $step = 1;
}
/*------------------------
使用协议书
------------------------*/
if($step==1)
{
    include('./templets/step1.htm');
    exit();
}
/*------------------------
环境检测
------------------------*/
else if($step==2)
{
    $sys_php = phpversion(); //php版本
    $sys_os = PHP_OS;
    $sys_gd = gdversion();
	$sys_register_globals = getRegisterGlobals();
	$sys_uploadsize = getServerFileUpload();
	$sys_diskspace = getDiskSpace(SLINEROOT);

    //目录检测
    $testdirs = array(
        '/*',
        '/data/*',
        '/newtravel/application/data/*',
		'/newtravel/application/cache/*',
		'/newtravel/application/logs/*',
		'/newtravel/application/config/*',
        '/v5/data/*',
        '/v5/cache/*',
        '/v5/logs/*',
        '/v5/config/*',
        '/uploads/*'
    );
	//函数检测
	$funccheck = array(
	    'mysql_connect',
        'curl_init',
        'fsockopen',
        'mb_internal_encoding',
        'zip_open'
		);
		
	

    include('./templets/step2.htm');
    exit();
}
/*------------------------
设置参数(安装第三步)

------------------------*/
else if($step==3)
{
    

    include('./templets/step3.htm');
    exit();
}
//所选数据库中是否存在CMS历史遗留数据
else if($step == 'isExistsLegacyData') 
{
	$data = 'no';
	$conn = mysql_connect($dbhost,$dbuser,$dbpwd) ;
    $query_id = mysql_query("SELECT * FROM information_schema.SCHEMATA where SCHEMA_NAME='".$dbname."';",$conn);
	if($query_id)
    {
		if(mysql_num_rows($query_id)>0)
		{
			if(mysql_select_db($dbname, $conn))
			{
				$query_id = mysql_query("SHOW TABLES LIKE 'sline_admin';",$conn);
				if($query_id)
				{
					if(mysql_num_rows($query_id)>0)
					{
						$data='ok';
					}
										
				}
			}
		}

    }
	echo $data;
	exit;
   
	
}

/*------------------------
安装第4步
function _4_Setup()
------------------------*/
else if($step==4)
{
    //exit;
    include('./templets/step4.htm');
    exit();

}

//创建数据库
else if($step == 'createDataBase') //创建数据库
{
	$conn = mysql_connect($dbhost,$dbuser,$dbpwd);
	$flag = mysql_query("SELECT @@sql_mode as sql_mode;",$conn);
	if(!$flag)
    {
		echo '数据库服务器或登录帐户无效，无法连接数据库，请重新设定';
		exit;
    }
	if(mysql_num_rows($flag)>0)
	{
		while($sql_mode_row = mysql_fetch_assoc($flag))
		{
			if(stripos($sql_mode_row['sql_mode'],'STRICT_TRANS_TABLES')!==false)
			{
				echo '数据库服务使用了SQL严格模式（STRICT_TRANS_TABLES）,系统将不能正常工作，请取消SQL严格模式';
				exit;
			}
		}
	}

    $flag = mysql_query("CREATE DATABASE IF NOT EXISTS `".$dbname."`;",$conn);
	if(!$flag)
    {
        echo '执行数据库创建SQL失败';
		exit;
    }

	echo 'ok';
	exit;
   
	
}
//创建common.inc.php
else if($step == 'createDataConfig') //数据库配置
{
  	$fp = fopen(dirname(__FILE__)."/common.inc.php","r");
    $configStr1 = fread($fp,filesize(dirname(__FILE__)."/common.inc.php"));
    fclose($fp);
	 //common.inc.php
    $configStr1 = str_replace("~dbhost~",$dbhost,$configStr1);
    $configStr1 = str_replace("~dbname~",$dbname,$configStr1);
    $configStr1 = str_replace("~dbuser~",$dbuser,$configStr1);
    $configStr1 = str_replace("~dbpwd~",$dbpwd,$configStr1);

    //写数据库文件
    @chmod(SLINEDATA,0777);
    $fp = fopen(SLINEDATA."/common.inc.php","w") ;
	$flag = 'no';
	if($fp)
	{
	   fwrite($fp,$configStr1);
       fclose($fp);	
	   $flag = 'ok';
	}

	echo $flag;
	exit;

}
else if($step == 'createDefaultConfig') //创建默认配置
{
	echo 'ok';
	exit;
}

else if($step == 'creattable')//创建表
{	
	$conn = mysql_connect($dbhost,$dbuser,$dbpwd) ;
	
    mysql_select_db($dbname);

    mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';",$conn);

      //创建数据表
  
    $query = '';
    $fp = fopen(dirname(__FILE__).'/sql-dftables.txt','r');
    while(!feof($fp))
    {
        $line = trim(fgets($fp,1024*1024));
        if(empty($line))
        {
            continue;
        }

        if(preg_match("/;$/", $line))
        {
            $query .= $line."\n";
            $rs = mysql_query($query,$conn);
            $error=mysql_error();
            if(!empty($error))
            {
                echo $error;
                exit;
            }
            $query='';
        } else
        {
            $query .= $line;
        }
    }
    fclose($fp);
	echo 'ok';
	exit;
}
else if($step == 'creatview')//创建视图
{
    $conn = mysql_connect($dbhost, $dbuser, $dbpwd);

    mysql_select_db($dbname);

    mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';", $conn);

    //创建视图
    $cr_view_result = crView($conn);
    if ($cr_view_result !== true)
    {
        echo $cr_view_result;
    } else
    {
        echo 'ok';
    }

    exit;
}
else if($step == 'initbasedata')//初始基础数据
{
	$conn = mysql_connect($dbhost,$dbuser,$dbpwd) ;
	
    mysql_select_db($dbname);
	mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';",$conn);
    //导入默认数据
    $query = '';
    $fp = fopen(dirname(__FILE__).'/sql-dfdata.txt','r');
    while(!feof($fp))
    {
        $line = rtrim(fgets($fp,1024*1024));
        if(empty($line))
            continue;
        if(preg_match("/;$/", $line))
        {
            $query .= $line."\n";
            $rs = mysql_query($query,$conn);
            $error=mysql_error();
            if(!empty($error))
            {
                echo 'error sql:'.$query.'\n';
                echo $error;
                exit;
            }
            $query='';
        } else
        {
            $query .= $line;
        }
    }
    fclose($fp);
	echo 'ok';
	exit;
}
else if($step == 'initdemodata')//初始演示数据
{
    if($usedata == 1)
    {
        $conn = mysql_connect($dbhost, $dbuser, $dbpwd);

        mysql_select_db($dbname);
        mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';", $conn);

        $query = '';
        $fp = fopen(dirname(__FILE__) . '/sql-moredata.txt', 'r');
        while (!feof($fp))
        {
            $line = rtrim(fgets($fp, 1024 * 1024));
            if(empty($line)) continue;
            if(preg_match("/;$/", $line))
            {
                $query .= $line . "\n";
                $rs = mysql_query($query, $conn);
                $error = mysql_error();
                if(!empty($error))
                {
                    echo 'error sql:' . $query . '\n';
                    echo $error;
                    exit;
                }
                $query = '';
            }
            else
            {
                $query .= $line;
            }
        }
        fclose($fp);
    }
    echo 'ok';
    exit;
}
else if($step == 'completedatabaseconfig')//完成数据库配置和安装
{
   $conn = mysql_connect($dbhost,$dbuser,$dbpwd) ;
	
    mysql_select_db($dbname);
	mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';",$conn);
	
	    //增加管理员帐号
    $adminquery = "INSERT INTO `sline_admin` (username,password,logintime,loginip,roleid) VALUES ('$adminuser', '".md5($adminpwd)."', '".time()."', '127.0.0.1','1');";
    mysql_query($adminquery,$conn);


	
	 //写入网站信息
	   if(!empty($_SERVER['REQUEST_URI']))
		$scriptName = $_SERVER['REQUEST_URI'];
		else
		$scriptName = $_SERVER['PHP_SELF'];
	
		$basepath = preg_replace("#\/install(.*)$#i", '', $scriptName);
	
		if(!empty($_SERVER['HTTP_HOST']))
			$baseurl = 'http://'.$_SERVER['HTTP_HOST'];
		else
			$baseurl = "http://".$_SERVER['SERVER_NAME'];
			
	 $rootDir  = dirname(dirname(dirname(__FILE__))).'\\';
     $webPrefix=substr($baseurl,strlen('http://')-1,strpos($baseurl,'.'));
	 $webroot = str_replace($rootDir,'',SLINEROOT);
     $webPrefix=str_replace('http://','',substr($baseurl,0,strpos($baseurl,'.')));
	 $sql = " INSERT INTO `sline_weblist` (webname,weburl,webid,webroot,webprefix) VALUES('主站','{$baseurl}','0','$webroot','$webPrefix')";
	 mysql_query($sql,$conn);


    $fp = fopen(dirname(__FILE__)."/mobile.php","r");
    $configStr1 = fread($fp,filesize(dirname(__FILE__)."/mobile.php"));
    fclose($fp);
    //mobile.php
    $configStr1 = str_replace("~domainname~",$baseurl,$configStr1);

    //写移动配置文件
    @chmod(SLINEDATA,0777);
    $fp = fopen(SLINEDATA."/mobile.php","w") ;
    if($fp)
    {
        fwrite($fp,$configStr1);
        fclose($fp);
    }

    echo 'ok';
	exit;	
}


/*------------------------
安装第5步(安装成功)
function _5_Setup()
------------------------*/
else if($step==5)
{
    include('./templets/step5.htm');
	 //锁定安装程序
	$fp = fopen($insLockfile,'w');
	fwrite($fp,'ok');
	fclose($fp);

    //为了安全,更改安装目录
    $install_dir = SLINEROOT.'/install';
    $new_install_dir = SLINEROOT.'/ins_'.uniqid();
    rename($install_dir,$new_install_dir);
    exit(); 
  
}



