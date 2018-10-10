<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_App extends ORM
{

    /**
     * @function 移动文件
     * @param $unzippath
     * @return bool|string
     */
    public static function pick_file($unzippath)
    {
        $files = array('FileManifest.txt', 'installer.ini', 'sql.php', 'unsql.php');
        $dir = $unzippath . '_temp';
        if (!is_dir($dir) && !mkdir($dir, 0777, true))
        {
            return false;
        }
        foreach ($files as $v)
        {
            $org = $unzippath . "/{$v}";
            $dist = $dir . "/{$v}";
            if (file_exists($org))
            {
                if (!rename($org, $dist))
                {
                    return false;
                }
            }
        }
        return array('distDir' => $dir, 'sql' => $dir . '/sql.php');
    }

    /**
     * @function 移动文件
     * @param $unzippath
     * @param $distDir
     * @param string $version
     * @return bool
     */
    public static function move_install_files($unzippath, $distDir, $version = '0.0.0.0')
    {
        $bool = true;
        $files = array('FileManifest.txt', 'installer.ini', 'unsql.php');
        foreach ($files as $v)
        {
            $orgFile = $unzippath . '_temp' . "/{$v}";
            $distFile = $distDir . "/{$v}";
            if (!file_exists($orgFile))
            {
                continue;
            }
            $ext = pathinfo($v, PATHINFO_EXTENSION);
            if ($ext == 'php')
            {
                $distFile = $distDir . "/{$version}.php";
            }
            if ($ext == 'txt')
            {
                $distFile = $distDir . "/{$version}.txt";
            }
            if (!rename($orgFile, $distFile))
            {
                $bool = false;
                break;
            }
        }
        return $bool;
    }

    /**
     * @function 获取目录
     * @param $dir
     * @return array
     */
    public static function get_parent_dir($dir)
    {
        $dirs = array();
        $count = substr_count($dir, '/');
        $dir = BASEPATH . '/' . $dir;
        if ($count < 1)
        {
            $dirs[] = $dir;
            return $dirs;
        }
        for ($i = 0; $i < $count - 1; $i++)
        {
            $dirs[] = $dir = dirname($dir);
        }
        return $dirs;
    }

}