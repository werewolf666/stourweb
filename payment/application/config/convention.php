<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 通用配置
 */

$conf = array(
    //webid
    'webid' => 0,
    //网站基础URl
    'base_url' => '/payment',
    //支付接口目录
    'interface_path' => VENDORPATH,
    //支付日志目录
    'log_path' => 'logs/pay/',
    //口令开启
    'token_on' => false,
    //口令关闭
    'token_name' => '__hash__',
    //模板目录
    'template_dir' => 'default/',
    //产品详情URL
    'show_url' => '/{module}/show_{aid}.html',
    //PC
    'pc' => array(
        'template' => 'pc/index',
        'method' => array()
    ),
    //MOBILE
    'mobile' => array(
        'template' => 'mobile/index',
        'method' => array()
    )
);
//读取配置
$pathConf = array('pc', 'mobile');
foreach ($pathConf as $v)
{
    $path = VENDORPATH . "/{$v}/";
    if (is_dir($path))
    {
        $handler = opendir($path);
        while (false !== ($file = readdir($handler)))
        {
            if (in_array($file, array('.', '..')))
            {
                continue;
            }
            $file = $path . "/{$file}/conf.php";
            if (file_exists($file))
            {
                $payConf = include $file;
                if (is_array($payConf) && isset($payConf['id']))
                {
                    $conf[$v]['method']["{$payConf['id']}"] = $payConf;
                }
            }
        }
    }
}
return $conf;