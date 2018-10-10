<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Config extends ORM
{
    /**
     * 检测支付证书是否存在或证书目录
     * @param $config 系统配置
     * @param bool|false $returnCertPath 返回证书目录
     * @return array
     */
    public static function is_exists_certs($config, $returnCertPath = false)
    {
        $certsPath = array(
            //4.0
            array(
                'chinabank' => array(
                    '/thirdpay/yinlian/certs/',
                ),
                'wxpay' => array(
                    '/thirdpay/weixinpay/cert/'
                )
            ),
            //4.0PC与5.0Mobile
            array(
                'chinabank' => array(
                    //'/thirdpay/yinlian/certs/', 4.0与5.0SDK不同
                    '/payment/application/vendor/pc/chinabank/certs/'
                ),
                'wxpay' => array(
                    '/thirdpay/weixinpay/cert/',
                    '/payment/application/vendor/pc/wxpay/cert/'
                )
            ),
            //5.0
            array(
                'chinabank' => array('/payment/application/vendor/pc/chinabank/certs/'),
                'wxpay' => array('/payment/application/vendor/pc/wxpay/cert/'),
                'bill' => array('/payment/application/vendor/pc/bill/cert/'),
            )
        );
        $certs = array(
            'wxpay' => array('apiclient_cert.p12', 'apiclient_cert.pem', 'apiclient_key.pem', 'rootca.pem'),
            'chinabank' => array('zhengshu.pfx'),
            'bill' => array('public-rsa.cer'),
        );
        //根据版本选择需要检测的目录
        if ($config['cfg_pc_version'] == 0 && $config['cfg_mobile_version'] == 0)
        {
            $certsPath = $certsPath[0];
        }
        else if ($config['cfg_pc_version'] == 0 && $config['cfg_mobile_version'] == 1)
        {
            $certsPath = $certsPath[1];
        }
        else
        {
            $certsPath = $certsPath[2];
        }
        //返回上传证书目录
        if ($returnCertPath)
        {
            return $certsPath;
        }
        //遍历文件
        foreach ($certsPath as $k => $v)
        {
            $bool = false;
            foreach ($v as $sub)
            {
                if ($bool)
                {
                    break;
                }
                foreach ($certs[$k] as $filename)
                {
                    if (!file_exists(BASEPATH . $sub . $filename))
                    {
                        $bool = true;
                        break;
                    }

                }
            }
            if ($bool)
            {
                $info[$k] = false;
                continue;
            }
            else
            {
                $info[$k] = true;
            }
        }
        //返回检测结果
        return $info;
    }

    /**
     * @function 清理缓存
     * 缓存主要分为以下几个部分
     * 1.标准前端程序缓存
     * 2.标准手机端缓存
     * 3.标准后台缓存
     * 4.全局data目录缓存
     * 5.支付相关缓存
     * 5.针对以前开发的供应商应用的相关缓存
     *
     */
    public static function clear_cache()
    {
        $dir = array(
            'cache' => SLINEDATA . '/cache'
        );
        if (isset($_GET['clear']))
        {
            if ($_GET['clear'] == 'all')
            {
                $dir = array_merge($dir, array('logs' => SLINEDATA . '/logs', 'thumb' => SLINEDATA . '/thumb'));
            }
        }
        //先删除目录下的文件：
        foreach ($dir as $k => $v)
        {
            self::del_dir_file($v);
        }
    }



    /**
     * 循环删除目录
     * @param $dir
     */
    public static function del_dir_file($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh))
        {
            if ($file != "." && $file != "..")
            {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath))
                {
                    unlink($fullpath);
                }
                else
                {
                    self::del_dir_file($fullpath);
                }

            }
        }
        closedir($dh);
    }
}