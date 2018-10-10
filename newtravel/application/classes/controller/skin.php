<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @desctription 皮肤管理控制器
 * Class Controller_Skin
 */
class Controller_Skin extends Stourweb_Controller
{



    public function before()
    {
        parent::before();

    }

    /*
     * 皮肤管理
     * */


    public function action_index()
    {
        $cfg_skin_id = DB::select('value')->from('sysconfig')->where('varname','=','cfg_skin_id')->execute()->get('value');
        $skin_list = DB::select()->from('skin')->execute()->as_array();
        $this->assign('cfg_skin_id',$cfg_skin_id);
        $this->assign('skinlist',$skin_list);
        $this->display('stourtravel/skin/index');
    }

    public function action_ajax_save()
    {
        //皮肤id
        $skin_id = Arr::get($_POST,'cfg_skin_id');

        //保存skin_id到sysconfig表
        $data = array();
        $data['cfg_skin_id'] = $skin_id;
        $data['webid'] = 0;
        $m = new Model_Sysconfig();
        $m->saveConfig($data);
        unset($data);

        //自定义配色保存色值

        if($skin_id == 8)
        {

            $data['main_color'] = Arr::get($_POST,'main_color');
            $data['icon_color'] = Arr::get($_POST,'icon_color');
            $data['line_color'] = Arr::get($_POST,'line_color');
            $data['font_color'] = Arr::get($_POST,'font_color');
            $data['font_hover_color'] = Arr::get($_POST,'font_hover_color');
            $data['nav_color'] = Arr::get($_POST,'nav_color');
            $data['nav_hover_color'] = Arr::get($_POST,'nav_hover_color');
            $data['footer_level_color'] = Arr::get($_POST,'footer_level_color');
            $data['usernav_color'] = Arr::get($_POST,'usernav_color');
            DB::update('skin')->set($data)->where('id','=',$skin_id)->execute();

        }
        //生成CSS文件
        Model_Skin::create_css_file($skin_id);

        echo json_encode(array('status'=>1));





    }







}