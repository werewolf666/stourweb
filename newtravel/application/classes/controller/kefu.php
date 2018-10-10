<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Kefu extends Stourweb_Controller{

   private  $parentkey = null;
   private  $itemid = null;
   private  $pc_version = 0;
   private  $kefu_file = NULL;
   public function before()
   {
       parent::before();

       Common::getUserRight('kefu','smodify');

       $this->assign('parentkey',$this->params['parentkey']);
       $this->assign('itemid',$this->params['itemid']);
       $this->parentkey = $this->params['parentkey'];
       $this->itemid = $this->params['itemid'];
       $weblist = Common::getWebList();
       $this->assign('weblist',$weblist);
       $this->assign('helpico',Common::getIco('help'));
       $m = new Model_Sysconfig();
       $configInfo = $m->getConfig(0);
       if($configInfo['cfg_pc_version']==5)
       {
           $this->pc_version = 5;//5.0版本
           $this->kefu_file =  BASEPATH.'/data/config.qq.kefu.php';

       }
       else
       {
           $this->pc_version = 0; //4.2版本
           $this->kefu_file = BASEPATH.'/qqkefu/config.main.php';
       }
       $this->assign('pc_version',$this->pc_version);
   }
    /**
     * 客服首页
     */
    public function action_index()
    {
        $kefufile = $this->kefu_file;
        include_once($kefufile);
        $this->assign('pos',$pos);
        $this->assign('display',$display);
        $this->assign('posh',$posh);
        $this->assign('post',$post);
        $this->assign('qqcl',$qqcl);
        $this->assign('phonenum',$phonenum);
        /***免费通话短信通知开始*****/

        /***免费通话短信通知结束*****/
        $configModel=new Model_Sysconfig();
        $config=$configModel->getConfig(0);
        $this->assign('config',$config);
        $this->display('stourtravel/kefu/index');
    }

    /**
     * 免费客服
     */
    public function action_freekefu()
    {
        $action=$this->params['action'];
        if(empty($action))
        {
            $this->display('stourtravel/kefu/freekefu');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $keyword=Arr::get($_GET,'keyword');
            $status = $_GET['status'];


            $order='order by status asc,addtime desc';
            $sort=json_decode(Arr::get($_GET,'sort'),true);

            if($sort[0]['property'])
            {
                $order='order by '.$sort[0]['property'].' '.$sort[0]['direction'];
            }

            $w="where id is not null";
            $w.=empty($keyword)?'':" and phone like '%{$keyword}%'";
            if(!empty($status) || $status===0 || $status==='0')
            {
                $w.=" and status={$status} ";
            }
            $sql="select *  from sline_freekefu  $w $order limit $start,$limit";
            //echo $sql;

            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_freekefu  $w")->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            foreach($list as $k=>&$v)
            {
                $v['addtime']= !empty($v['addtime'])?date('Y-m-d H:i',$v['addtime']):'';
                $v['finishtime']= !empty($v['finishtime'])?date('Y-m-d H:i',$v['finishtime']):'';
            }
            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$list;
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

            if(is_numeric($id)) //
            {
                $model=ORM::factory('freekefu',$id);
                $model->delete();
            }
        }
        else if($action=='update')//更新某个字段
        {
            $id=Arr::get($_POST,'id');
            $field=Arr::get($_POST,'field');
            $val=Arr::get($_POST,'val');

            if(is_numeric($id))
            {
                $model=ORM::factory('freekefu',$id);
            }
            if($model->id)
            {
                $model->$field=$val;
                $model->save();
                if($model->saved())
                    echo 'ok';
                else
                    echo 'no';
            }
        }



    }

    /*
     * 来电提醒
     */
    public function action_freesms()
    {

        $free_msg_info = DB::select()->from('sms_msg')->where('msgtype','=','free_tel_msg')->execute()->current();
        $this->assign('free_tel_msg',$free_msg_info['msg']);
        $this->assign('free_tel_msg_open',$free_msg_info['isopen']);
        $this->display('stourtravel/kefu/freesms');
    }
    /**
     * 免费客服
     */
    public function action_freekefu_edit()
    {
        $id=$this->params['id'];
        $model=ORM::factory('freekefu',$id);
        $info=$model->as_array();
        $this->assign('info',$info);
        $this->display('stourtravel/kefu/freekefu_edit');
    }
    /**
     * 编辑客服
     */
    public function action_ajax_freekefu_save()
    {
        $id=$_POST['id'];
        $model=ORM::factory('freekefu',$id);
        $model->description=$_POST['description'];
        $model->status=1;
        $model->finishtime=time();
        $model->save();
    }

    /*
     * 客服电话设置
     * */
    public function action_phone()
    {
        $this->display('stourtravel/kefu/phone');
    }

    /*
     * QQ客服
     * */
    public function action_qq()
    {

        $kefufile = $this->kefu_file;
        include_once($kefufile);
        $this->assign('pos',$pos);
        $this->assign('display',$display);
        $this->assign('posh',$posh);
        $this->assign('post',$post);
        $this->assign('qqcl',$qqcl);
        $this->assign('phonenum',$phonenum);
        $this->display('stourtravel/kefu/qq');
    }

    /*
     * 第三方客服
     * */
    public function action_other()
    {
        $kefufile = $this->kefu_file;
        include_once($kefufile);
        $this->assign('pos',$pos);
        $this->assign('display',$display);
        $this->assign('posh',$posh);
        $this->assign('post',$post);
        $this->assign('qqcl',$qqcl);
        $this->assign('phonenum',$phonenum);
        $this->display('stourtravel/kefu/third');
    }

    /*
     * 保存第三方客服
     * */
    public function action_ajax_save()
    {
        $kefufile = $this->kefu_file;
        $display = Arr::get($_POST,'display');
        $pos = Arr::get($_POST,'position');
        $posh = Arr::get($_POST,'posh');
        $post = Arr::get($_POST,'post');
        $phonenum = Arr::get($_POST,'phonenum');
        $qqcl = Arr::get($_POST,'qqcl');
        $str='<?php '."\r\n";


        if(empty($pos))
            $str.='$pos="left";'."\r\n";
        else
            $str.='$pos="'.$pos.'";'."\r\n";
        if(empty($posh))
            $str.='$posh="0px";'."\r\n";
        else
        {
            if(strpos($posh,'%')===false&&strpos($posh,'px')===false)
            {
                $posh=(int)$posh.'px';
            }
            $str.='$posh="'.$posh.'";'."\r\n";
        }
        if(empty($post))
            $str.='$post="50%";'."\r\n";
        else
        {
            if(strpos($post,'%')===false&&strpos($post,'px')===false)
            {
                $post=(int)$post.'px';
            }
            $str.='$post="'.$post.'";'."\r\n";
        }
        $str.= empty($display) ? '$display=0;'."\r\n" : '$display=1;'."\r\n";
        $str.= empty($qqcl) ? '$qqcl=1;'."\r\n" : '$qqcl="'.$qqcl.'";'."\r\n";
        $str.= !empty($phonenum) ? '$phonenum="'.$phonenum.'";'."\r\n" : '';
        Common::saveToFile($kefufile,$str);
        $this->save_config($_POST);



        /***免费通话短信通知开始*****/
        $free_tel_msg =  Arr::get($_POST,'free_tel_msg');
        $free_tel_msg_open =  Arr::get($_POST,'free_tel_msg_open');
        $free_tal_arr = array(
            'msg'=>$free_tel_msg,
            'isopen'=>$free_tel_msg_open
        );
        DB::update('sms_msg')->set($free_tal_arr)->where('msgtype','=','free_tel_msg')->execute();
        /***免费通话短信通知结束*****/
        echo json_encode(array('status'=>true));
    }

    private function save_config($arr)
    {
        $newArr=array('webid'=>0);
        foreach($arr as $k=>$v)
        {
            if(strpos($k,'cfg_')===0)
                $newArr[$k]=$v;
        }
        $configModel=new Model_Sysconfig();
        $configModel->saveConfig($newArr);
    }


    /*
     * qqlist
     * */
    public function action_qqlist()
    {

        $action=$this->params['action'];

        $attrtable = 'qq_kefu';
        if($action=='read')
        {


            $node=Arr::get($_GET,'node');
            $list=array();
            if($node=='root')//属性组根
            {
                $list=ORM::factory($attrtable)->where('pid','=','0')->get_all();

                $list[]=array(
                    'leaf'=>true,
                    'id'=>'0add',
                    'qqname'=>'<button class="dest-add-btn df-add-btn" onclick="addSub(0)">添加</button>',
                    'displayorder'=>'add'
                );
            }
            else //子级
            {
                $list=ORM::factory($attrtable)->where('pid','=',$node)->get_all();
                foreach($list as $k=>$v)
                {

                    $list[$k]['leaf']=true;
                }
                $list[]=array(
                    'leaf'=>true,
                    'id'=>$node.'add',
                    'qqname'=>'<button class="dest-add-btn df-add-btn" onclick="addSub(\''.$node.'\')">添加</button>',
                    'displayorder'=>'add'
                );
            }
            echo json_encode(array('success'=>true,'text'=>'','children'=>$list));
        }
        else if($action=='addsub')//添加子级
        {
            $pid=Arr::get($_POST,'pid');

            $model=ORM::factory($attrtable);
            $model->pid=$pid;
            $model->qqname="未命名";
            $model->save();

            if($model->saved())
            {
                $model->reload();
                echo json_encode($model->as_array());
            }
        }
        else if($action=='save') //保存修改
        {
            $rawdata=file_get_contents('php://input');
            $field=Arr::get($_GET,'field');
            $data=json_decode($rawdata);
            $id=$data->id;
            if($field)
            {
                $model=ORM::factory($attrtable,$id);
                if($model->id)
                {
                    $model->$field=$data->$field;
                    $model->save();
                    if($model->saved())
                        echo 'ok';
                    else
                        echo 'no';
                }
            }

        }


        else if($action=='delete')//属性删除
        {
            $rawdata=file_get_contents('php://input');
            $data=json_decode($rawdata);
            $id=$data->id;
            if(!is_numeric($id))
            {
                echo json_encode(array('success'=>false));
                exit;
            }
            $model=ORM::factory($attrtable,$id);
            $model->delete();

        }
        else if($action=='update')//更新操作
        {
            $id=Arr::get($_POST,'id');
            $field=Arr::get($_POST,'field');
            $val=Arr::get($_POST,'val');
            $model=ORM::factory($attrtable,$id);
            if($model->id)
            {
                $model->$field=$val;
                $model->save();
                if($model->saved())
                    echo 'ok';
                else
                    echo 'no';
            }
        }

    }

   //保存系统配置
    public function action_ajax_save_free_msg()
    {
        $free_tel_msg =  $_POST['free_tel_msg'];
        $free_tel_msg_open =  $_POST['free_tel_msg_open'];
        $free_tal_arr = array(
            'msg'=>$free_tel_msg,
            'isopen'=>$free_tel_msg_open
        );
        DB::update('sms_msg')->set($free_tal_arr)->where('msgtype','=','free_tel_msg')->execute();
        echo json_encode(array('status'=>true));

    }










}