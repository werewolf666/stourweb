<?php defined('SYSPATH') or die('No direct script access.');
class Controller_User extends Stourweb_Controller{

    public function before()
    {
        parent::before();
        $session = Session::instance();
        $roleid = $session->get('roleid');
        if($roleid != 1)
        {
            exit(__('onlysys'));
        }
        $this->assign('cmsurl', URL::site());
        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);
        $this->assign('weblist',Common::getWebList());
    }

    public function action_list()
    {
        $action=$this->params['action'];
        if(empty($action))
        {
            $roles=ORM::factory('role')->get_all();
            $this->assign('roles',json_encode($roles));
            $this->display('stourtravel/user/list');
        }
        else if($action=='read')
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $sort=json_decode(Arr::get($_GET,'sort'));

            $w="a.id is not null";
            $sql="select a.*,b.rolename from sline_admin a left join sline_role b on a.roleid=b.roleid where $w order by a.roleid limit $start,$limit ";
            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_admin a  where $w")->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();

            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$list;
            $result['success']=true;
            echo json_encode($result);
        }
        else if($action=='update')
        {
            $id=Arr::get($_POST,'id');
            $field=Arr::get($_POST,'field');
            $val=Arr::get($_POST,'val');

            $value_arr[$field] = $val;
            $isupdated = DB::update('admin')->set($value_arr)->where('id','=',$id)->execute();
            if($isupdated)
                echo 'ok';
            else
                echo 'no';
        }
        else if($action=='delete') //删除某个记录
        {

            $rawdata=file_get_contents('php://input');
            $data=json_decode($rawdata);
            $id=$data->id;
            if(is_numeric($id))
            {
                $model=ORM::factory('admin',$id);
                $model->deleteClear();
            }
        }
    }

    /*
     * 管理用户添加
     */
    public function action_ajax_save()
    {
        $id=Arr::get($_POST,'id');
        $pwd=$_POST['password'];
        if($id)
        {
           $model=ORM::factory('admin',$id);
            if(!empty($pwd))
                $model->password=md5($pwd);
        }
        else
        {
            $model=ORM::factory('admin');
            $model->username=Arr::get($_POST,'username');
            $model->password=md5($pwd);
        }
        $roleid = Arr::get($_POST,'roleid');
        if($roleid)
        {

            $model->roleid=Arr::get($_POST,'roleid');
        }
        $model->beizu=Arr::get($_POST,'beizu');
        $model->litpic=Arr::get($_POST,'pic_upload');
        $model->save();
        if($model->saved())
        {
            $model->reload();
            $_arr=$model->as_array();
            echo json_encode(array('status'=>true,'msg'=>'保存成功'));
        }
        else
        {
            echo json_encode(array('status'=>false,'msg'=>'保存失败'));
        }
    }
    public function action_ajax_checkuser()
    {
        $username=Arr::get($_POST,'username');
        $model=ORM::factory('admin')->where("username='$username'")->find();
        if($model->id)
            echo 'false';
        else
            echo 'true';
    }
    /*
     * 权限管理
     */
    public function action_right()
    {
        $action=$this->params['action'];
        if(empty($action))
        {
            $model=ORM::factory('role');
            $list=$model->get_all();
            $this->assign('list',$list);
            $this->display('stourtravel/user/rightlist');
        }
        else if($action=='save')
        {
            $rolename_arr=Arr::get($_POST,'rolename');
            $description_arr=Arr::get($_POST,'description');
            foreach($rolename_arr as $k=>$v)
            {
                $model=ORM::factory('role',$k);
                if($model->roleid)
                {
                    $model->rolename=$v;
                    $model->description=$description_arr[$k];
                    $model->save();
                }
            }
            echo 'ok';
        }
        else if($action=='add')
        {
            $model=ORM::factory('role');
            $model->rolename='自定义';
            $model->create();
            $model->reload();
            echo $model->roleid;

        }
        else if($action=='del')
        {
            $id=Arr::get($_POST,'id');
            if($id==1)
            {
                echo 'no';
            }
            else
            {
                $model=ORM::factory('role',$id);
                $model->delete();
                echo 'ok';
            }
        }
    }
    /*
     * 设置权限
     */
    public function action_setright()
    {
       $action=$this->params['action'];
       if(empty($action))
       {
           $roleid=$this->params['roleid'];
           if(empty($roleid))
           {
               exit('权限ID错误');
           }
           $list=array();
           $this->assign('roleid',$roleid);
           $this->assign('list',$list);
           $this->display('stourtravel/user/rightset');
       }
       else if($action=='update')
        {
            $field=Arr::get($_POST,'field');
            $menuid=Arr::get($_POST,'menuid');
            $value=Arr::get($_POST,'value');
            $roleid=Arr::get($_POST,'roleid');
            Model_Role_Right::set_right($roleid,$menuid,$field,$value);
        }
       else if($action=='read')
       {
           $node = Arr::get($_GET, 'node');
           $roleid = $this->params['roleid'];

           if ($node == 'root')//属性组根
           {
                $pid = 0;
                $list = $this->_get_menu_child($pid,$roleid);
           }
           else //子级
           {
               $list = $this->_get_menu_child($node,$roleid);

           }
           echo json_encode(array('success' => true, 'text' => '', 'children' => $list));

       }

    }



    public function action_dialog_edit()
    {
        $id=$_GET['id'];
        if(!empty($id))
        {
            $model=ORM::factory('admin',$id);
            if($model->loaded())
            {
                $info=$model->as_array();
                $this->assign('info',$info);
            }
        }
        $roles=ORM::factory('role')->get_all();
        $this->assign('roles',$roles);
        $this->display('stourtravel/user/dialog_edit');
    }

    public function action_dialog_help()
    {
        include(Kohana::find_file('data', 'license'));
        $url = "http://www.stourweb.com/api/cms/cmshelp";
        $label = $_GET['label'];
        $post_data = array ('host'=>$_SERVER['HTTP_HOST'],'code' => $SerialNumber,'cmslabel' =>$label);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        $info = json_decode($output,true);
        $this->assign('info',$info);
        $this->display('stourtravel/user/dialog_help');
    }


    /**
     * @function 获取子级
     * @param $pid
     * @return array
     */
    private function _get_menu_child($pid,$roleid)
    {
        $menu = DB::select()->from('menu_new')->where('pid','=',$pid)->and_where('isshow','=','1')->execute()->as_array();
        $children=array();

        foreach($menu as $v)
        {
            $new_child=array();

            $right = $this->_get_right_by_method($v['id'],$roleid);
            $new_child['key'] = $v['controller'].'/'.$v['method'];
            $new_child['right'] = $right;
            $new_child['text'] = $v['title'];
            if(!$this->_is_has_child($v['id']))
            {

                $new_child['leaf'] = true;
            }
            else
            {
                $new_child['isparent'] = 1;
            }
            $new_child['id'] = $v['id'];
            $children[]=$new_child;
        }
        return $children;
    }

    /**
     * 根据当前的菜单id获取权限
     */
    private function _get_right_by_method($menuid,$roleid)
    {
        $right = DB::select()->from('role_right')->where('roleid','=',$roleid)->and_where('menuid','=',$menuid)->execute()->get('right');
        return $right;

    }

    /**
     * @function 检测是否有子级
     * @param $pid
     * @return mixed
     */
    private function _is_has_child($pid)
    {
        $flag = DB::select('id')->from('menu_new')->where('pid','=',$pid)->execute()->count();
        return $flag;
    }









}