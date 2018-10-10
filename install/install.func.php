<?php
function RunMagicQuotes(&$str)
{
    if(!get_magic_quotes_gpc()) {
        if( is_array($str) )
            foreach($str as $key => $val) $str[$key] = RunMagicQuotes($val);
        else
            $str = addslashes($str);
    }
    return $str;
}
function getServerFileUpload()
{
	if (@ini_get('file_uploads')) {
		return '允许 '.ini_get('upload_max_filesize');
	} else {
		return '<font color="red" class="nopass">禁止</font>';
	}
}
function getRegisterGlobals()
{
	if (@ini_get('register_globals')) {
		return '<font color="red" class="nopass">开启</font>';
	} else {
		return '关闭';
	}

}
//获取磁盘空间
function getDiskSpace($dir)
{
	if(function_exists('disk_free_space')) 
	{
			$num = floor(disk_free_space($dir) / (1024*1024)).'M';
	} else 
	{
			$num = 'unknow';
	}
   return $num;
}


function gdversion()
{
  //没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
  if(!function_exists('phpinfo'))
  {
      if(function_exists('imagecreate')) return '2.0';
      else return '<font color="red" class="nopass">0</font>';
  }
  else
  {
    ob_start();
    phpinfo(8);
    $module_info = ob_get_contents();
    ob_end_clean();
    if(preg_match("/\bgd.*?version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches)) {   $gdversion_h = $matches[1];  }
    else {  $gdversion_h = '<font color="red" class="nopass">0</font>'; }
    return $gdversion_h;
  }
}




function TestWrite($d)
{
    $tfile = '_test.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = @fopen($d.'/'.$tfile,'w');
    if(!$fp) return false;
    else
    {
        fclose($fp);
        $rs = @unlink($d.'/'.$tfile);
        if($rs) return true;
        else return false;
    }
}



function crView($conn)
{
		
		  $sql1="DROP VIEW IF EXISTS `sline_search`;";
		  $sql2="create view sline_search (channelname,webid,aid,typeid,title,description,litpic,shownum,kindlist,attrid,headimgid,tid,ishidden)
AS

SELECT '通用',webid,aid,typeid,title,content,litpic,shownum,kindlist,attrid,0,id,ishidden FROM sline_model_archive";

    $rs = mysql_query($sql1,$conn);
    $error=mysql_error();
    if(!empty($error))
    {
        return $error;
    }

    $rs = mysql_query($sql2,$conn);
    $error=mysql_error();
    if(!empty($error))
    {
        return $error;
    }

    return true;
	
}
