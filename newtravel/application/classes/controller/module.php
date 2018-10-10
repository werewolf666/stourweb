<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Module extends Stourweb_Controller{

    //右模块控制器
    public function before()
    {
        parent::before();
        $action = $this->request->action();

        if($action == 'store')
        {
            $param = $this->params['action'];
            $right = array(
                'read'=>'slook',
                'save'=>'smodify',
                'delete'=>'sdelete',
                'update'=>'smodify'
            );
            $user_action = $right[$param];
            if(!empty($user_action))
                Common::getUserRight('module',$user_action);


        }
        if($action == 'index')
        {
            Common::getUserRight('module','slook');
        }
        if($action == 'list')
        {
            Common::getUserRight('module','slook');
        }
        if($action == 'add')
        {
            Common::getUserRight('module','smodify');
        }
        if($action == 'ajax_add_save' || $action=='ajax_edit_save')
        {
            Common::getUserRight('module','smodify');
        }
        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);
        $weblist = Common::getWebList();
        $this->assign('weblist',$weblist);
    }

    /*
     * 模块设置页
     * */
	public function action_index()
	{
        //$addmodule = ORM::factory('model')->where("id>13")->get_all();
        //$addmodule = Model_Model::getAllModule();
        $module_list = ORM::factory('model')->get_all();
        $this->assign('module',$module_list);
        $this->display('stourtravel/module/index');
	}

    /*
     * 模块列表
     * */
    public function action_list()
    {
        $this->display('stourtravel/module/list');
    }
    /*
     * store操作
     * */
    public function action_store()
    {
        $action=$this->params['action'];
        if(empty($action))  //显示线路列表页
        {
            $this->display('stourtravel/module/list');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $webid=Arr::get($_GET,'webid');
            $version = Arr::get($_GET,'moduleversion');
            $keyword=Arr::get($_GET,'keyword');
            $w = "where id is not null";
            $w.=!empty($keyword) ? " and modulename like '%$keyword%'" : ''  ;
            $w.=$webid==-1 ? "" : " and webid='$webid'";
            $w.=!empty($version) ? " and version=$version" : '';

            $sql = "select a.id,a.aid,a.modulename,a.webid,a.issystem,a.version from sline_module_list a $w order by a.modulename asc limit $start,$limit";

            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_module_list a $w")->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            $new_list=array();
            foreach($list as $k=>$v)
            {
                $new_list[]=$v;
            }
            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$new_list;
            $result['success']=true;

            echo json_encode($result);
        }
        else if($action=='save')   //保存字段
        {

        }
        else if($action=='delete') //删除某个记录
        {
            $rawdata=file_get_contents('php://input');
            $data=json_decode($rawdata);
            $id=$data->id;
            //$id = Arr::get($_GET,'id');

            if(is_numeric($id)) //
            {
                $model=ORM::factory('module_list',$id);
                $model->delete();

            }
        }

    }

    /*
     * 添加模块
     * */
    public function action_add()
    {
        $webid = $this->params['webid'];
        $this->assign('webid',$webid);
        $this->assign('action','ajax_add_save');
        $this->display('stourtravel/module/edit');
    }
    /*
     * 添加模块保存
     * */
    public function action_ajax_add_save()
    {
        $webid = Arr::get($_POST,'webid');
        $version = Arr::get($_POST,'version');//版本
        $type = Arr::get($_POST,'moduletype');//模块类型
        $model = ORM::factory('module_list');
        $model->modulename = Arr::get($_POST,'modulename');
        $model->body = Arr::get($_POST,'body');
        $model->webid = $webid;
        $model->version = $version;
        $model->type = $type;
        $model->aid = Common::getLastAid('sline_module_list',$webid);
        $model->create();
        $flag = false;
        if($model->saved())
        {
           $flag = true;
        }
        echo json_encode(array('status'=>$flag));
    }

    /*
     * 模块修改页面
     * */
    public function action_edit()
    {
        $id = $this->params['id'];
        $info = ORM::factory('module_list',$id)->as_array();
        $this->assign('info',$info);
        $this->assign('action','ajax_edit_save');
        $this->display('stourtravel/module/edit');
    }
    /*
     * 模块修改保存
     * */
    public function action_ajax_edit_save()
    {
        $articleid = Arr::get($_POST,'articleid');
        $model = ORM::factory('module_list',$articleid);
        $model->modulename = Arr::get($_POST,'modulename');
        $model->body = Arr::get($_POST,'body');
        $model->version = Arr::get($_POST,'version');
        $model->type = Arr::get($_POST,'moduletype');
        $model->update();
        $flag = false;
        if($model->saved())
        {
            $flag = true;
        }
        echo json_encode(array('status'=>$flag));
    }
    /*
     * 获取页面
     * */

    public function action_ajax_getpagelist()
    {
        $webid = Arr::get($_POST,'webid');

        $typeid = Arr::get($_POST,'typeid');
        $model = new Model_Module_Config();
        $arr = $model->getPageList($webid,$typeid);
        echo json_encode($arr);
    }
    /*
     * 获取选择的模块列表
     * */
    public function action_ajax_getselect()
    {
        $webid = Arr::get($_POST,'webid');
        $typeid = Arr::get($_POST,'typeid');
        $shortname = Arr::get($_POST,'shortname');

        $model = new Model_Module_Config();
        //$arr = $model->getSelectItem($pageid,$webid);
        $arr = $model->get_selected_item($typeid,$webid,$shortname);
        $out = array();
        $out['selectlist'] = $arr;
        $out['typeid'] = $typeid;
        $out['shortname'] = $shortname;
        //$out['pageid'] = $pageid;
        echo json_encode($out);

    }
    /*
     *获取全部模块
     * */
    public function action_ajax_getallmodule()
    {
        $webid = Arr::get($_POST,'webid');
        $cm = new Model_Sysconfig();
        $configinfo =$cm->getConfig(0);
        //根据当前系统版本选择模块
        $version = empty($configinfo['cfg_pc_version']) ? 4 : 5;
        $arr = ORM::factory('module_list')
            ->where("webid=0 and version=$version")
            ->get_all();
        echo json_encode($arr);
    }

    /*
     * 更新排序
     * */
    public function action_ajax_updatesort()
    {
        $webid = Arr::get($_POST,'webid');
        //$pageid =Arr::get($_POST,'pageid');
        $typeid = Arr::get($_POST,'typeid');
        $shortname = Arr::get($_POST,'shortname');
        $orderlist = Arr::get($_POST,'orderlist');
        $model = ORM::factory('module_config')->where('webid','=',$webid)
                                              ->and_where('typeid','=',$typeid)
                                              ->and_where('shortname','=',$shortname)
                                              ->find();
        if(!$model->loaded())
        {
            $model = ORM::factory('module_config');
            $model->typeid = $typeid;
            $model->shortname = $shortname;
            $model->webid = $webid;
            $model->moduleids = $orderlist;
            $model->save();

        }
        else
        {
            $model->moduleids = $orderlist;
            $model->save();
        }


        echo $model->saved();


    }

}