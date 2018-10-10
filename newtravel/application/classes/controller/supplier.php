<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Supplier extends Stourweb_Controller
{
    /*
     * 供应商总控制器
     *
     */
    public function before()
    {
        parent::before();
        $action = $this->request->action();
        if ($action == 'index')
        {
            $param = $this->params['action'];
            $right = array(
                'read' => 'slook',
                'save' => 'smodify',
                'delete' => 'sdelete',
                'update' => 'smodify'
            );
            $user_action = $right[$param];
            if (!empty($user_action))
                Common::getUserRight('supplier', $user_action);
        }
        if ($action == 'add')
        {
            Common::getUserRight('supplier', 'sadd');
        }
        if ($action == 'edit')
        {
            Common::getUserRight('supplier', 'smodify');
        }
        if ($action == 'ajax_save')
        {
            Common::getUserRight('supplier', 'smodify');
        }
        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
    }

    public function action_index()
    {
        $action = $this->params['action'];
        if (empty($action))  //显示列表
        {
            $kindmenu= ORM::factory("supplier_kind")->get_all();
            $this->assign('kindmenu',$kindmenu);
            $this->display('stourtravel/supplier/list');
        }
        else if ($action == 'read')    //读取列表
        {
            $start = Arr::get($_GET, 'start');
            $limit = Arr::get($_GET, 'limit');
            $keyword = Arr::get($_GET, 'keyword');
            $kindid = Arr::get($_GET, 'kindid');
            $verifystatus = Arr::get($_GET, 'verifystatus');
            $sort = json_decode(Arr::get($_GET, 'sort'), true);
            $w=' where 1 ';
            if ($sort[0]['property'])
            {
                $order = 'order by a.'.$sort[0]['property'] . ' ' . $sort[0]['direction'] . ',a.addtime desc';
            }

            if ($keyword!=='')
            {
                $w.= "and (a.suppliername like '%{$keyword}%' or a.telephone like '%{$keyword}%' or a.mobile like '%{$keyword}%')";
            }
            if ($kindid!=='')
            {
                $w.= "and kindid={$kindid} ";
            }
            if ($verifystatus!=='')
            {
                $w.= "and verifystatus={$verifystatus} ";
            }

            $sql = "select a.*  from sline_supplier as a $w $order limit $start,$limit";
            //echo $sql;
            $totalcount_arr = DB::query(Database::SELECT, "select count(*) as num from sline_supplier a ")->execute()->as_array();
            $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $new_list = array();
            foreach ($list as $k => $v)
            {
                $new_list[] = $v;
            }
            $result['total'] = $totalcount_arr[0]['num'];
            $result['lists'] = $new_list;
            $result['success'] = true;
            echo json_encode($result);
        }
        else if ($action == 'save')   //保存字段
        {
        }
        else if ($action == 'delete') //删除某个记录
        {
            $rawdata = file_get_contents('php://input');
            $data = json_decode($rawdata);
            $id = $data->id;
            if (is_numeric($id)) //
            {
                $model = ORM::factory('supplier', $id);
                $model->delete();
            }
        }
        else if ($action == 'update')//更新某个字段
        {
            $id = Arr::get($_POST, 'id');
            $field = Arr::get($_POST, 'field');
            $val = Arr::get($_POST, 'val');

            if ($field == 'displayorder')  //如果是排序
            {
                $val = empty($val) ? 9999 : $val;
            }
            $value_arr[$field] = $val;
            $isupdated = DB::update('supplier')->set($value_arr)->where('id','=',$id)->execute();
            if($isupdated)
                echo 'ok';
            else
                echo 'no';
        }
    }

    /*
     * 添加
     * */
    public function action_add()
    {
        $this->assign('action', 'add');
        $this->assign('kind',$this->_supplier_kind());
        $this->display('stourtravel/supplier/edit');
    }

    /*
     * 修改
     * */
    public function action_edit()
    {
        $id = $this->params['id'];//会员id.
        $this->assign('action', 'edit');
        $info = ORM::factory('supplier', $id)->as_array();
        $info['piclist_arr'] =  json_encode(Common::getUploadPicture($info['piclist']));//图片数组
        $info['kindlist_arr'] =  Model_Destinations::getKindlistArr($info['kindlist']);//目的地数组
        $qua = unserialize($info['qualification']);
        //$qua['kindtype'] = ORM::factory('supplier_kind',$qua['kindid'])->get('kindname');
        $product_list =  ORM::factory('model')->where('isopen=1 and id not in(4,6,7,10,11,14)')->get_all();
        foreach($product_list as $k=>$v)
        {
            $pinyin = $v['maintable']=='model_archive'?'common':$v['pinyin'];
            if((!St_Functions::is_system_app_install($v['id']) || !St_Functions::is_normal_app_install('supplier'.$pinyin.'manage')) && $v['maintable']!="model_archive")
            {
                unset($product_list[$k]);
            }
            if( $v['maintable']=="model_archive" && !St_Functions::is_normal_app_install('supplier'.$pinyin.'manage'))
            {
                unset($product_list[$k]);
            }
        }
        if(!empty($qua['apply_kind']))
        {
            $apply_product = ORM::factory('supplier_kind')->where("id in(".$qua['apply_kind'].")")->get_all();
            $this->assign('apply_product',$apply_product);
        }

        $this->assign('product_list',$product_list);

        $this->assign('info', $info);
        $this->assign('qua',$qua);
        $this->assign('kind',$this->_supplier_kind());
        $this->display('stourtravel/supplier/edit');
    }

    /**
     * 供应商分类
     */
    private function _supplier_kind()
    {
        $kind = DB::query(Database::SELECT, "select *,concat(path,'-',id) as level from sline_supplier_kind where isopen=1 order by level asc,displayorder asc")->execute()->as_array();
        return $kind;
    }
    /*
     * 保存
     * */
    public function action_ajax_save()
    {
        $action = ARR::get($_POST, 'action');//当前操作
        $id = ARR::get($_POST, 'id');
        $status = false;
        $kindlist = Arr::get($_POST,'kindlist');


        //添加操作
        if ($action == 'add' && empty($id))
        {
            $model = ORM::factory('supplier');
            $model->addtime = time();
        }
        else
        {
            $model = ORM::factory('supplier')->where('id', '=', $id)->find();
        }
        if(!empty($model->account))
        {
            $tempModel=ORM::factory('supplier')->where('account','=',$_POST['account'])->find();
            if($tempModel->loaded()&&$tempModel->id!=$id)
            {
                echo json_encode(array('status' => false, 'msg'=>'账号已经存在'));
                return;
            }
            $model->account=$_POST['account'];
        }
        if(!empty($_POST['password']))
        {
            $model->password = md5($_POST['password']);
        }

        $imagestitle = Arr::get($_POST,'imagestitle');
        $images = Arr::get($_POST,'images');
        $imgheadindex = Arr::get($_POST,'imgheadindex');

        //图片处理
        $piclist ='';
        $litpic = $images[$imgheadindex];
        for($i=1;isset($images[$i]);$i++)
        {
            $desc = isset($imagestitle[$i]) ? $imagestitle[$i] : '';
            $pic = !empty($desc) ? $images[$i].'||'.$desc : $images[$i];
            $piclist .= $pic.',';

        }
        $piclist =strlen($piclist)>0 ? substr($piclist,0,strlen($piclist)-1) : '';//图片

        $model->suppliername = ARR::get($_POST, 'suppliername');
        $model->suppliertype = ARR::get($_POST, 'suppliertype');//供应商类型
        $model->linkman = ARR::get($_POST, 'linkman');
        $model->mobile = ARR::get($_POST, 'mobile');
        $model->telephone = ARR::get($_POST, 'telephone');
        $model->address = ARR::get($_POST, 'address');
        $model->email = Arr::get($_POST,'email');
        $model->litpic = $litpic;
        $model->piclist = $piclist;
        $model->fax = ARR::get($_POST, 'fax');
        $model->qq = ARR::get($_POST, 'qq');
        $model->kindid = ARR::get($_POST, 'kindid');
        $model->modtime = time();

        $model->verifystatus = Arr::get($_POST,'verifystatus');
        $model->content = Arr::get($_POST,'content');
        $model->lng = Arr::get($_POST,'lng');
        $model->lat = Arr::get($_POST,'lat');
        $model->kindlist = implode(',',$kindlist);//所属目的地
        $model->content = Arr::get($_POST,'content');//供应商介绍
        $model->finaldestid=empty($_POST['finaldestid'])?Model_Destinations::getFinaldestId(explode(',',$model->kindlist)):$_POST['finaldestid'];

        if ($action == 'add' && empty($id))
        {
            $model->create();
        }
        else
        {
            //这里添加供应商审核功能
            $vstatus =Arr::get($_POST,'vstatus');
            //通过审核
            if($vstatus==3||$vstatus==2)
            {
                $qua = unserialize($model->qualification);
                if(!empty($qua)&&$vstatus==3)
                {
                    $model->verifystatus = $vstatus;
                    $model->reprent = $qua['reprent'];
                    $model->address = $qua['address'];
                    $model->suppliername = $qua['suppliername'];
                    $model->authorization = implode(',',$_POST['authorization']);

                }
                else if($vstatus==2)
                {
                   $model->verifystatus = $vstatus;
                   $model->reason = Arr::get($_POST,'reason');
                }



            }

            $model->update();
        }
        if ($model->saved())
        {
            if ($action == 'add')
            {
                $productid = $model->id; //插入的产品id
            }
            else
            {
                $productid = $model->id;
            }
            $status = true;
        }
        echo json_encode(array('status' => $status, 'productid' => $productid));
    }

    /*
      以json方式返回供应商列表
   */
    public function action_ajax_supplier_list()
    {
        $model =ORM::factory('supplier');
        $list = $model->get_all();
        echo json_encode($list);
    }
    /*
          以json方式返回供应商列表
       */
    public function action_ajax_supplier_kindid()
    {
		
		$pid = Arr::get($_POST,'pid')?Arr::get($_POST,'pid'):0;
	  
        $sql= "SELECT * FROM sline_supplier  where  `kindid`={$pid}  ORDER BY  CONVERT(suppliername USING gbk) ASC";
       
        $list =DB::query(Database::SELECT,$sql)->execute()->as_array();;
        echo json_encode(array('nextlist'=>$list));
    }
    /*
      设置产品供应商
    */
    public function action_ajax_set_supplier()
    {
        $product_arr = array(
            1 => 'line',
            2 => 'hotel',
            3 => 'car',
            4 => 'article',
            5 => 'spot',
            6 => 'photo',
            8 => 'visa',
            13 => 'tuan'
        );
        $typeid = ARR::get($_POST, 'typeid');
        $productid = ARR::get($_POST, 'productid');
        $supplierids = ARR::get($_POST, 'supplierids');
        $model = ORM::factory($product_arr[$typeid], $productid);
        $is_success = 'ok';
        $productid_arr = explode('_', $productid);
        foreach ($productid_arr as $k => $v)
        {

            $value_arr['supplierlist'] = $supplierids;
            $isupdated = DB::update($product_arr[$typeid])->set($value_arr)->where('id','=', $v)->execute();
            if(!$isupdated)
                $is_success = 'no';
        }
        echo $is_success;
    }

    /*
     * ajax检测是否存在
     * */
    public function action_ajax_check()
    {
        $field = $this->params['type'];
        $val = ARR::get($_POST, 'val');//值
        $mid = ARR::get($_POST, 'mid');//会员id
        $flag = Model_Member::checkExist($field, $val, $mid);
        echo $flag;
    }

    public function action_dialog_set()
    {
        $suppliers = $_GET['suppliers'];
        $id = $_GET['id'];
        $typeid = $_GET['typeid'];
        $selector = urldecode($_GET['selector']);
        $supplierArr = explode(',', $suppliers);
        $supplierList = ORM::factory('supplier')->get_all();
        $kind=$this->_supplier_kind();
        array_unshift($kind,array('id'=>0,'kindname'=>'默认'));
        $column=array();
        foreach($supplierList as $v){
            array_push($column,$v['kindid']);
        }
        $count=array_count_values($column);
        foreach($kind as &$v){
            if(!empty($count[$v['id']])){
                $v['count']=$count[$v['id']];
            }
        }
        $this->assign('supplierArr', implode(',',$supplierArr));
        $this->assign('selector', $selector);
        $this->assign('kind',$kind);
        $this->display('stourtravel/supplier/dialog_set');
    }

    /**
     * 分类列表视图
     */
    public function action_kind()
    {
        //栏目深度
        $level = 0;
        $parent = ($node = Arr::get($_GET, 'node')) == 'root' ? 0 : $node;
        $table = 'supplier_kind';
        $action = $this->params['action'];
        $model = ORM::factory($table);
        switch ($action)
        {
            case 'read':
                $path = 0;
                $list = $model->where("pid={$parent}")->get_all();
                foreach ($list as $k => $v)
                {
                    $list[$k]['allowDrag'] = false;
                    $list[$k]['leaf'] = substr_count($list[$k]['path'], '-') < $level ? false : true;
                }
                $list[] = array(
                    'leaf' => true,
                    'id' => "{$parent}add",
                    'kindname' => "<button class=\"dest-add-btn df-add-btn\" onclick=\"addSub('{$parent}','{$path}')\">添加</button>",
                    'allowDrag' => false,
                    'allowDrop' => false,
                    'displayorder' => 'add'
                );
                echo json_encode(array('success' => true, 'text' => '', 'children' => $list));
                break;
            case 'addsub':
                $pid = Arr::get($_POST, 'pid');
                $model->pid = $pid;
                $model->kindname = "未命名";
                $model->path = Arr::get($_POST, 'path');
                $model->save();
                if ($model->saved())
                {
                    $model->reload();
                    $data = $model->as_array();
                    $data['leaf'] = true;
                    echo json_encode($data);
                }
                break;
            case 'save':
                $rawdata = file_get_contents('php://input');
                $field = Arr::get($_GET, 'field');
                $data = json_decode($rawdata);
                $id = $data->id;
                if ($field)
                {
                    $model = ORM::factory($table, $id);
                    if ($model->id)
                    {
                        $model->$field = $data->$field;
                        $model->save();
                        if ($model->saved())
                            echo 'ok';
                        else
                            echo 'no';
                    }
                }
                break;
            case 'update':
                $id = Arr::get($_POST, 'id');
                $field = Arr::get($_POST, 'field');
                $val = Arr::get($_POST, 'val');

                $value_arr[$field] = $val;
                $isupdated = DB::update($table)->set($value_arr)->where('id','=',$id)->execute();
                if($isupdated)
                    echo 'ok';
                else
                    echo 'no';






                break;
            case 'delete':
                $rawdata = file_get_contents('php://input');
                $data = json_decode($rawdata);
                $id = $data->id;
                if (!is_numeric($id))
                {
                    echo json_encode(array('success' => false));
                    exit;
                }
                $model = ORM::factory($table, $id);
                $model->delete();
                break;
            default:
                $this->display('stourtravel/supplier/kind');
        }
    }

    /**
     * @function 供应商设置
     */
    public function action_config()
    {
        $config = DB::select()->from('sysconfig')
            ->where('varname','=','cfg_supplier_display_status')
            ->execute()->current();

        $this->assign('config',$config);
        $this->display('stourtravel/supplier/config');
    }

    /**
     * @function 保存供应商配置
     */
    public function action_ajax_save_config()
    {
        $display = $_POST['display'];
        $data = array(
            'value'=>$display
        );
        $config = DB::select('id')->from('sysconfig')
            ->where('varname','=','cfg_supplier_display_status')
            ->execute()->get('id');
        if($config)
        {
            DB::update('sysconfig')->set($data)
                ->where('varname','=','cfg_supplier_display_status')->execute();
        }
        else
        {
            $data['webid'] = 0 ;
            $data['varname'] = 'cfg_supplier_display_status' ;
            DB::insert('sysconfig',array_keys($data))->values(array_values($data))->execute();
        }
        echo  json_encode(array('status'=>1));
    }

}