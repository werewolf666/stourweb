<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 缓存相关处理类
 */
class St_Cache{
    

    /**
     * @function 配置文件缓存
     * @return mixed
     */
    public static function cache_config()
    {
        $file = DATAPATH . 'config.cache.php';
        //缓存文件不存在
        if (!file_exists($file))
        {
            $data = DB::select()->from('sysconfig')->execute()->as_array();
            $config = array();
            foreach ($data as $v)
            {
                $config[$v['varname']] = trim($v['value']);
            }
            file_put_contents($file, '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');' . PHP_EOL . 'return ' . var_export($config, true) . ';');
        }
        $data = require_once($file);
        return $data;
    }


   

    

}