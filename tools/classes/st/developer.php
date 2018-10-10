<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 开发者公共方法,为开发者提供的方法
 * Class St_Developer
 */
class St_Developer{


    /**
     * @function 加载开发者应用目录的CSS
     * @param $files ,要加载的css文件列表
     * @param $path ,加载的路径,如:'/developer/line/mobile/css/'
     * @return string ,返回生成的样式"<link type='text/css'>...."
     */
    static function css($files, $path)
    {

        $filearr = explode(',', $files);
        $file_list = array();
        $out = '';
        $plugin_res_url = $GLOBALS['cfg_plugin_developer_public_url'];
        $path = trim($path, '/\\');
        $root_path = rtrim(BASEPATH,'/\\');

        foreach ($filearr as $file)
        {
            $tfile = $root_path . '/' . $plugin_res_url .$path . '/'.$file;
            $file = ltrim($plugin_res_url, '/\\') .$path . '/'. $file;

            if (file_exists($tfile))
            {
                $file_list[] = $file;
            }
        }

        if (!empty($file_list))
        {

            $full_host = $GLOBALS['cfg_basehost'];
            //如果开启css合并,此项是默认开启的.
            if ($GLOBALS['cfg_compress_open'])
            {
                $f = implode(',', $file_list);
                $css_url = $full_host . '/min/?f=' . $f;
                $out = '<link type="text/css" href="' . $css_url . '" rel="stylesheet"  />' . "\r\n";
            }
            else
            {
                foreach ($file_list as $css)
                {
                    $out .= HTML::style($full_host.'/'.$css) . "\r\n";
                }
            }


        }
        return $out;
    }

    /**
     * @function 加载开发者应用目录的js
     * @param $files 要加载的js文件列表
     * @param $path  加载的路径,如:'/developer/line/pc/js/'
     * @return string 返回加载脚本的html代码 <script type="text/javascript" src=...
     */
    static function js($files,$path)
    {
        $file_list = explode(',', $files);
        $js_list = array();
        $out = $v = '';
        //开发资源目录
        $plugin_res_url = $GLOBALS['cfg_plugin_developer_public_url'];
        $path = trim($path, '/\\');
        $root_path = rtrim(BASEPATH,'/\\');
        foreach ($file_list as $file)
        {
            $tfile = $root_path . '/' . $plugin_res_url . $path .'/'.$file;

            $file = ltrim($plugin_res_url, '/\\') . $path . '/'. $file;
            if (file_exists($tfile))
            {
                $js_list[] = $file;
            }
        }
        if ($js_list)
        {

            $full_host =  $GLOBALS['cfg_basehost'];
            //如果开启自动合并js
            if ($GLOBALS['cfg_compress_open'])
            {
                $f = implode(',', $js_list);
                $js_url = $full_host.'/min/?f=' . $f;
                $out = '<script type="text/javascript" src="' . $js_url . '"></script>' . "\r\n";
            }
            else
            {
                foreach ($js_list as $js)
                {
                    $out .= HTML::script($full_host.'/'.$js) . "\r\n";
                }
            }
        }
        return $out;
    }
   

    

}