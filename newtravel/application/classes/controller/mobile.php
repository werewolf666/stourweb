<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Mobile extends Stourweb_Controller
{
    /*
     * 手机设置总控制器
     * */

    public function before()
    {
        parent::before();

        $sysconfig_fields = array('cfg_mobile_open', 'cfg_mobile_version', 'cfg_m_main_url', 'cfg_m_img_url', 'cfg_m_phone', 'cfg_m_icp', 'cfg_m_tongjicode', 'cfg_m_logo');
        $configinfo = Model_Sysconfig::get_configs(0, $sysconfig_fields);

        if (!$configinfo['cfg_m_main_url']) {
            $configinfo['cfg_m_main_url'] = $GLOBALS['cfg_basehost'];
        }
        if (!isset($configinfo['cfg_mobile_version'])) {
            $configinfo['cfg_mobile_version'] = 0;
        }

        $this->assign('config', $configinfo);
        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
    }

    /*
     * 手机端配置首页
     * */
    public function action_index()
    {
        $this->display('stourtravel/mobile/index');
    }

    /*
     * 系统配置
     * */
    public function action_sys()
    {
        $this->display('stourtravel/mobile/sys');
    }

    public function action_logo()
    {
        $this->display('stourtravel/mobile/logo');
    }

    //统计代码
    public function action_stats()
    {
        $this->display('stourtravel/mobile/stats');
    }

    //在线客服
    public function action_customer_service()
    {
        $this->display('stourtravel/mobile/customer_service');
    }

    /*
    * 手机导航配置操作部分
     * -------------------------------
     * ----------------------------
    * */
    public function action_nav()
    {
        $action = $this->params['action'];

        if (empty($action)) {
            $this->display('stourtravel/mobile/nav');
        } else if ($action == 'read') {

            $node = Arr::get($_GET, 'node');

            $list = array();
            if ($node == 'root')//属性组根
            {


                $list = ORM::factory("m_nav")->order_by(DB::expr("ifnull(m_displayorder, 9999) asc"))->get_all();
                foreach ($list as $k => $v) {
                    $list[$k]['leaf'] = true;
                    $list[$k]['allowDrag'] = false;
                    $list[$k] = $this->_get_menu_ico($v);
                 }
                $list[] = array(
                    'leaf' => true,
                    'id' => '0add',
                    'm_title' => '<button class="dest-add-btn df-add-btn" onclick="addSub(0)">添加</button>',
                    'allowDrag' => false,
                    'allowDrop' => false,
                    'm_displayorder' => 'add',
                    'm_isopen' => 'add'
                );
            }
            echo json_encode(array('success' => true, 'text' => '', 'children' => $list));
        } else if ($action == 'addsub')//添加子级
        {
            $pid = Arr::get($_POST, 'pid');
            $model = ORM::factory("m_nav");
            $model->m_title = "未命名";
            $model->m_url = '';
            $model->save();

            if ($model->saved()) {
                $model->reload();
                echo json_encode($model->as_array());
            }
        } else if ($action == 'save') //保存修改
        {
            $rawdata = file_get_contents('php://input');
            $field = Arr::get($_GET, 'field');
            $data = json_decode($rawdata);
            $id = $data->id;
            if ($field) {
                $model = ORM::factory("m_nav", $id);
                if ($model->id) {
                    $model->$field = $data->$field;
                    $model->save();
                    if ($model->saved())
                        echo 'ok';
                    else
                        echo 'no';
                }
            }

        } else if ($action == 'delete')//属性删除
        {
            $rawdata = file_get_contents('php://input');
            $data = json_decode($rawdata);
            $id = $data->id;
            if (!is_numeric($id)) {
                echo json_encode(array('success' => false));
                exit;
            }
            $model = ORM::factory("m_nav", $id);
            $model->delete();

        } else if ($action == 'update')//更新操作
        {
            $id = Arr::get($_POST, 'id');
            $field = Arr::get($_POST, 'field');
            $val = Arr::get($_POST, 'val');
            $model = ORM::factory("m_nav", $id);
            if ($model->id) {
                $model->$field = $val;
                if ($field == 'm_displayorder') {
                    $val = intval($val) <= 0 ? 9999 : intval($val);
                    $model->$field = $val;
                }
                $model->save();
                if ($model->saved())
                    echo 'ok';
                else
                    echo 'no';
            }
        }

    }


    /**
     * @function 获取菜单的图片
     * @param $menu
     */
    private function _get_menu_ico($menu)
    {
        if(!$menu['m_ico'])
        {
            $menu['m_ico'] = Common::get_menu_no_ico($menu['m_typeid']);
        }
        $menu['default_ico'] = Common::get_menu_no_ico($menu['m_typeid']);
        return $menu;
    }


    /*
   * 手机导航获取(ajax)
   * */
    public function action_ajax_getnav()
    {
        $arr = DB::select()->from('m_nav')->order_by(DB::expr(" ifnull(m_displayorder, 9999) asc "))->execute()->as_array();
        $out = array();
        foreach ($arr as $row)
        {
            $isopen = $row['m_isopen'] ? $row['m_isopen'] : 0;
            $openstatus = $isopen ? Common::getIco('show') : Common::getIco('hide');
            $issystem = $row['m_issystem'];
            $editcls = $issystem ? "readonly='true'" : '';
            $ar = array();
            $ar['navname'] = $row['m_title'] ? $row['m_title'] : $row['shortname'];
            $ar['displayorder'] = $row['m_displayorder'] == 9999 ? '' : $row['m_displayorder'];
            $ar['isopen'] = $isopen;
            $ar['ico'] = $row['m_ico'] ? $row['m_ico'] : '';
            $ar['linkurl'] = $row['m_url'] ? $row['m_url'] : $row['url'];
            $ar['openstatus'] = $openstatus;
            $ar['issystem'] = $row['m_issystem'];
            $ar['id'] = $row['id'];
            $ar['editcls'] = $editcls;
            $out[] = $ar;
        }
        echo json_encode(array('list' => $out));
    }

    /*
     * 保存手机导航
     * */
    public function action_ajax_savenav()
    {
        $model = new Model_Nav();
        $model->save_mobile_nav($_POST);
        echo json_encode(array('status' => true));
    }

    /**
     * @function 更新手机导航字段
     */
    public function action_ajax_updatenav()
    {
        $id = Arr::get($_POST, 'id');
        $value  = Arr::get($_POST, 'value');
        $field  = Arr::get($_POST, 'field');
        $rtn    = array('status'=>false);
        $m_mnav = ORM::factory('m_nav')->where('id','=',$id)->find();
        if($m_mnav->loaded())
        {
            if($field == 'm_displayorder')
            {
                $value = empty($value) ? 9999 : $value;
            }
            $m_mnav->$field = $value;
            $m_mnav->save();
            if($m_mnav->saved())
            {
                $rtn['status'] = true;
            }
        }
        echo json_encode($rtn);
    }

    /*
     * 设置中心-主导航添加保存(ajax)
     *
     * */
    public function action_addnav()
    {
        $this->assign('webid', $this->params['webid']);
        $this->display('stourtravel/mobile/nav_add');
    }

    public function action_ajax_addnavsave()
    {
        $model = new Model_M_Nav();
        $model->m_title = Arr::get($_POST, 'shortname');
        $model->m_url = Arr::get($_POST, 'linkurl');
        $model->create();
        $out = array();
        if ($model->saved())
        {
            $out['status'] = true;
        }
        else
        {
            $out['status'] = false;
        }
        echo json_encode($out);
    }

    /*
     *删除导航
     * */
    public function  action_ajax_delnav()
    {
        $navid = Arr::get($_GET, 'id');
        $o = ORM::factory('m_nav')->where("id=$navid")->find();
        $o->delete();
        if (!$o->loaded())
        {
            $out['status'] = true;
        }
        else
        {
            $out['status'] = false;
        }
        echo json_encode($out);
    }

    /*
     * 导航ICO图标配置
     * */
    public function action_dialog_ico()
    {
        $navid = $this->params['id'];



        $info = ORM::factory('m_nav')->where("id=$navid")->find()->as_array();
        $info = $this->_get_menu_ico($info);
        $this->assign('info', $info);
        $this->display('stourtravel/mobile/ico');
    }

    /*
     *
     * ico保存
     * */
    public function action_ajax_ico_save()
    {
        $litpic = Arr::get($_POST, 'litpic');
        $id = Arr::get($_POST, 'id');
        $flag = false;
        if ($id)
        {
            $value_arr = array('m_ico' => $litpic);
            $isupdated = DB::update('m_nav')->set($value_arr)->where('id', '=', $id)->execute();
            if ($isupdated)
                $flag = true;
        }
        echo json_encode(array('status' => $flag));
    }
}