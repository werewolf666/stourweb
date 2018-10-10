<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Skin extends ORM {

    /**
     * @function 创建前台css皮肤文件
     * @param $skin_id
     *
     */
    public static function create_css_file($skin_id)
    {
        $srcfile = APPPATH.'data/init/skin.css';
        $destpath = BASEPATH.'/res/css/skin.css';

        $skin_info = DB::select()->from('skin')->where('id','=',$skin_id)->execute()->current();
        $content = file_get_contents($srcfile);
        $content = str_replace('#main_color#',$skin_info['main_color'],$content);
        $content = str_replace('#icon_color#',$skin_info['icon_color'],$content);
        $content = str_replace('#line_color#',$skin_info['line_color'],$content);
        $content = str_replace('#font_color#',$skin_info['font_color'],$content);
        $content = str_replace('#font_hover_color#',$skin_info['font_hover_color'],$content);
        $content = str_replace('#nav_color#',$skin_info['nav_color'],$content);
        $content = str_replace('#nav_hover_color#',$skin_info['nav_hover_color'],$content);
        $content = str_replace('#footer_level_color#',$skin_info['footer_level_color'],$content);
        $content = str_replace('#usernav_color#',$skin_info['usernav_color'],$content);
        file_put_contents($destpath,$content);

    }
 
}