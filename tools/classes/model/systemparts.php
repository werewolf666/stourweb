<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 系统升级类(新版本)
 * */

class Model_SystemParts
{
    public static $coreSystemPartCode = 'core';
    public static $pcSystemPartCode = 'pc';
    public static $mobileSystemPartCode = 'mobile';

    public static function getSystemPart($partcode, $partid = null)
    {
        $systemParts = self::getSystemParts();
        foreach ($systemParts as $systempartcode => $systempartarr)
        {
            if ($systempartcode == $partcode)
            {
                if ($partid == null)
                    return $systempartarr;

                foreach ($systempartarr as $systempart)
                {
                    if ($systempart['id'] == $partid)
                        return $systempart;
                }
            }
        }
        return null;
    }

    public static function getAppPart($partcode, $appname)
    {
        $installerini = (Model_Upgrade3::get_app_install_path($partcode)."/installer.ini");
        if (!is_file($installerini))
            return null;

        $filehandle = fopen($installerini, "r");
        if (!$filehandle)
            return null;

        $versionpath = "";
        while (!feof($filehandle))
        {
            $line = fgets($filehandle, 4096);
            if (!empty($line))
            {
                $tmparr = explode("=", $line);
                if (is_array($tmparr) && count($tmparr) == 2)
                {
                    if (trim($tmparr[0]) == 'version_file_path')
                    {
                        $versionpath = str_ireplace('\\', '/', trim($tmparr[1]));
                        break;
                    }
                }
            }
        }
        fclose($filehandle);

        if (empty($versionpath))
            return null;

        $appPart = array('id' => $partcode, 'name' => $appname, 'version_path' => $versionpath, 'status' => '1');
        $appPart = self::loadVersionConfigFile($appPart);
        return $appPart;
    }


    public static function getSystemParts()
    {
        $result = array();
        $partsconfig = Common::getConfig('systemparts');
        foreach ($partsconfig as $partname => $partconfigarr)
        {
            $result[$partname] = array();
            foreach ($partconfigarr as $partconfig)
            {
                $partinfo = self::loadVersionConfigFile($partconfig);
                if($partinfo != null)
                    $result[$partname][] = $partinfo;
            }
        }

        return $result;
    }

    private static function loadVersionConfigFile($partinfo)
    {
        if (isset($partinfo['version_path']))
        {
            $versionfile = BASEPATH . '/' . $partinfo['version_path'];
            if (is_file($versionfile))
            {
                include($versionfile);
                $partinfo = Arr::merge($partinfo, array('pcode' => $pcode, 'cVersion' => $cVersion, 'versiontype' => $versiontype, 'pubdate' => $pubdate));
                return $partinfo;
            }
            else
                return null;
        }
        else
            return null;
    }

    public static function getCoreMajorVersion()
    {
        $majorVersion = '4.2';
        $coresystempart = self::getSystemPart(self::$coreSystemPartCode, '0');
        if ($coresystempart != null)
        {
            $verarr = explode('.', $coresystempart['cVersion']);
            $majorVersion = implode('.', array_slice($verarr, 0, 2));
        }
        return $majorVersion;
    }
}