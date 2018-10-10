<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Quickmenu extends Stourweb_Controller
{
    private $_menu;
    private $_adminId;

    function before()
    {
        parent::before();
        $this->_adminId = Session::instance()->get('userid');
        $config_file = CACHE_DIR.'newtravel/menu_' . $this->_adminId . '.php';
        if(!file_exists($config_file)){
          Model_Menu_New::set_config();
        }
        $this->_menu = include $config_file;
    }

    function action_index()
    {
        $result = array();
        $exists = Model_Quickmenu::quick_menu_all($this->_adminId);
        foreach ($exists as $v)
        {
            if(!$v['menu_id']){
                continue;
            }
            $result[] = $v['menu_id'];
        }
        $result = $this->_data_format($result);
        $this->assign('quickmenu', $result);
        $this->display('stourtravel/quickmenu/index');
    }

    function action_sidemenu()
    {
        $result = array();
        $exists = Model_Quickmenu::quick_menu_all($this->_adminId);
        foreach ($exists as $v)
        {
            if(!$v['menu_id']){
                continue;
            }
            $result[] = $v['menu_id'];
        }
        $result = $this->_data_format($result);
        $this->assign('quickmenu', $result);
        $this->display('stourtravel/quickmenu/sidemenu');
    }

    function action_select()
    {
        $menu = Model_Quickmenu::all_menu($this->_menu);
        $quickMenu = array();
        $result = DB::select('menu_id')->from('menu_quick')->where('admin_id', '=', $this->_adminId)->execute()->as_array();
        foreach ($result as $v)
        {
            if(!$v['menu_id']){
                continue;
            }
            $quickMenu[] = $v['menu_id'];
        }
        $quickMenu = $this->_data_format($quickMenu);
        $this->assign('menu', $menu);
        $this->assign('quickmenu', $quickMenu);
        $this->display('stourtravel/quickmenu/select');
    }

    //aJax获取父级节点
    function action_ajax_parent()
    {
        $result = isset($_POST['data']) ? explode(',', $_POST['data']) : array();
        if ($result)
        {
            $result = $this->_data_format($result);
        }
        echo json_encode($result);
    }

    function action_ajax_save()
    {
        $result = isset($_POST['data']) ? explode(',', $_POST['data']) : array();
        if ($result)
        {
            $exists = array();
            $menuQuick = Model_Quickmenu::quick_menu_all($this->_adminId);
            foreach ($menuQuick as $v)
            {
                if(!$v['menu_id']){
                    continue;
                }
                $exists[] = $v['menu_id'];
            }
            //添加
            $add = array_diff($result, $exists);
            if ($add)
            {
                foreach ($add as $v)
                {
                    DB::insert('menu_quick', array('menu_id', 'admin_id'))->values(array($v, $this->_adminId))->execute();
                }
            }
            //删除
            $del = array_diff($exists, $result);
            if ($del)
            {
                DB::delete('menu_quick')->where('menu_id', 'in', $del)->execute();
            }
            //重新查询所有
            $result = array();
            $exists = Model_Quickmenu::quick_menu_all($this->_adminId);
            foreach ($exists as $v)
            {
                if(!$v['menu_id']){
                    continue;
                }
                $result[] = $v['menu_id'];
            }
            $result = $this->_data_format($result);
        }
        echo json_encode($result);
    }

    //关闭快捷菜单
    function action_ajax_set()
    {
        $status = isset($_GET['open']) ? $_GET['open'] : 0;
        $total_rows = DB::update('sysconfig')->set(array('value' => $status))->where('varname', '=', 'cfg_quick_menu')->execute();
        echo $total_rows > 0 ? 'true' : 'false';
    }

    //菜单搜索
    function action_search()
    {
        $template=json_decode(file_get_contents('http://www.stourweb.com/api/cms/template'));
        $app=json_decode(file_get_contents('http://www.stourweb.com/api/cms/app'));
        $stourzx=json_decode(file_get_contents('http://www.stourweb.com/api/cms/stourzx'));
        $this->assign('keyword', $_GET['keyword']);
        $this->assign('template',Common::struct_to_array($template));
        $this->assign('app',Common::struct_to_array($app));
        $this->assign('stourzx',Common::struct_to_array($stourzx));
        $this->display('stourtravel/quickmenu/search');
    }

    //ajax获取查询结果
    function action_ajax_search()
    {
        $keyword = $_POST['keyword'];
        $page = array('current' => $_POST['page'], 'size' => 15, 'total' => 0);
        $obj = DB::select()->from('menu_new')->where('title', 'like', '%'.$keyword.'%')->and_where('level','>',0);
        $roldArr = DB::select('roleid')->from('admin')->where('id', '=', $this->_adminId)->execute()->current();
        if ($roldArr['roleid'] > 1)
        {
            $menuArr = DB::select('menuid')->from('role_right')->where('roleid', '=', $roldArr['roleid'])->and_where('right', '=', 1)->execute()->as_array();
            if ($menuArr)
            {
                $id=array();
                foreach($menuArr as $v){
                    $id[]=$v['menuid'];
                }
                $obj->and_where('id', 'in', $id);
            }
            else
            {
                $obj->and_where('id', '<', 0);
            }
        }
        $page['total'] = count($obj->execute()->as_array());
        $list = $obj->limit($page['size'])->offset($page['size'] * ($page['current'] - 1))->execute()->as_array();
        foreach($list as &$v){
            $parents = Model_Quickmenu::menu_parent($this->_menu, $v['id']);
            $nameArr = Model_Quickmenu::menu_title($this->_menu, $parents);
            $v['searchtitle']=str_replace($keyword,"<em>{$keyword}</em>",$v['title']);
            $v['path']=implode(' > ',array_reverse($nameArr));
            $v['url']=Model_Quickmenu::menu_title($this->_menu, $parents[0], 'url');
        }
        echo json_encode(array('page' => $page, 'list' => $list));
    }

    /**
     * @function 根式化快捷菜单
     * @param $result
     * @return array
     */
    function _data_format($result)
    {
        $quickMenu = array();
        foreach ($result as $k => $item)
        {
            $parents = Model_Quickmenu::menu_parent($this->_menu, $item);
            $url = Model_Quickmenu::menu_title($this->_menu, $parents[0], 'url');
            if(!$url){
                continue;
            }
            $nameArr = Model_Quickmenu::menu_title($this->_menu, $parents);
            $quickMenu[] = array('menu' => array($parents[0], array_shift($nameArr), $url[0]), 'path' => implode('/', array_reverse($nameArr)));
        }
        return $quickMenu;
    }
}