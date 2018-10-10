<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Friendlink  extends Stourweb_Controller{

    public function before()
    {
        parent::before();
        $action = $this->request->action();
        if($action == 'list')
        {
            $param = $this->params['action'];
            $right = array(
                'read'=>'slook',
                'save'=>'smodify',
                'delete'=>'sdelete',
                'update'=>'smodify',
                'create'=>'sadd'
            );
            $user_action = $right[$param];
            if(!empty($user_action))
                Common::getUserRight('flink',$user_action);


        }
        if($action == 'add')
        {
            Common::getUserRight('flink','sadd');
        }
        if($action == 'edit')
        {
            Common::getUserRight('flink','smodify');
        }
        if($action == 'ajax_save')
        {
            Common::getUserRight('flink','smodify');
        }
        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);
        $this->assign('weblist',Common::getWebList());

    }
     /*
	链接列表  
	 */
	public function action_list()
	{
		$action=$this->params['action'];
		if(empty($action))  //显示线路列表页
		{

            $posArr=$this->geneHtmlPosarr();
            $this->assign('posArr',$posArr);
		    $this->display('stourtravel/friendlink/list');
		}
		else if($action=='read')    //读取列表
		{
			$start=Arr::get($_GET,'start');
			$limit=Arr::get($_GET,'limit');
			$kindid=Arr::get($_GET,'typeid');
            $webid = !isset($_GET['webid']) ? '-1' : $_GET['webid'];
            $keyword = Arr::get($_GET,'keyword');
			$sort=json_decode(Arr::get($_GET,'sort'));
			if($sort[0]->property)
			{
				if($sort[0]->property=='displayorder')
				{
					$order='order by displayorder '.$sort[0]->direction;
				}
				else if($sort[0]->property=='addtime')
				{
					$order='order by addtime '.$sort[0]->direction;
				}
			}
			$w="id is not null";
            $w.=$webid=='-1' ? '' : " and webid=$webid";
			$w.=empty($typeid)?'':" and find_in_set($typeid,address)";
            $w.=empty($keyword)?'':" and sitename like '%$keyword%'";
			$sql="select *,ifnull(displayorder,9999) as displayorder from sline_yqlj where $w $order limit $start,$limit ";
			$totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_yqlj  where $w")->execute()->as_array();
			$list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            foreach($list as &$row)
            {
                $row['check_time'] = !empty($row['check_time'])?date('Y-m-d'):'';
            }
			
			$result['total']=$totalcount_arr[0]['num'];
			$result['lists']=$list;
			$result['success']=true;
			echo json_encode($result);
		}
		else if($action=='save')   //保存字段
		{
		   
		}
		else if($action=='create')
		{
		   $model=ORM::factory('yqlj');
		   $siteurl=Arr::get($_POST,'siteurl');
		   if(strpos($siteurl,St_Functions::get_http_prefix())===FALSE)
		      $siteurl=St_Functions::get_http_prefix().trim($siteurl);
		   $model->sitename=Arr::get($_POST,'sitename');
		   $model->siteurl=$siteurl;
		   $model->address="0";
		   $model->webid=Arr::get($_POST,'webid');
		   $model->save();
		   $model->reload();
		   $res=array();
		   $res['success'] = true;
           $res['message'] = "Created new User"; 
           $res['data']=$model->as_array();
		   echo json_encode($res);
		}
		else if($action=='delete') //删除某个记录
		{
		   
		   $rawdata=file_get_contents('php://input');
		   $data=json_decode($rawdata);
		   $id=$data->id;   
		   if(is_numeric($id)) 
		   {
		    $model=ORM::factory('yqlj',$id);
		    $model->delete();
		   }
		   
		}
		else if($action=='update')//更新某个字段
		{
			
			$id=Arr::get($_POST,'id');
			$field=Arr::get($_POST,'field');
			$val=Arr::get($_POST,'val');
			
			$model=ORM::factory('yqlj',$id);
			$model->$field=$val;
			$model->save();
			if($model->saved())
            {
                $check_time = time();
                if($field=='anti_link_status')
                {
                    $model->check_time=$check_time;
                    $model->save();
                }
                echo json_encode(array('status'=>true,'check_time'=>date('Y-m-d',$check_time)));
            }
			else
            {
                echo json_encode(array('status'=>false));
            }
			
			
		}

	}
    public function action_dialog_addlink()
    {
        $weblist=Common::getWebList();
        $this->assign('weblist',$weblist);
        $this->display('stourtravel/friendlink/dialog_addlink');
    }
    public function action_dialog_setpos()
    {
        $id=$_GET['id'];
        $types=$_GET['types'];
        $typeArr=$types!==null&&$types!==''?explode(',',$types):array();
        $extendArr=$this->getAllTypes();

        $this->assign('id',$id);
        $this->assign('typeArr',$typeArr);
        $this->assign('posArr',$extendArr);
        $this->display('stourtravel/friendlink/dialog_setpos');
    }

    //反链检查
    public function action_ajax_check_anti_link()
    {
        $id = $_POST['id'];
        $url = $_POST['url'];

        $main_host = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'];

        $webid = DB::select('webid')->from('yqlj')->where('id','=',$id)->execute()->get('webid');
        $web_host = '';
        if($webid==0)
        {
            $web_host = DB::select('weburl')->from('weblist')->where('webid','=',0)->execute()->get('weburl');
        }
        else
        {
            $web_host = DB::select('weburl')->from('destinations')->where('id','=',$webid)->execute()->get('weburl');
        }
        $web_host = empty($web_host)?$main_host:$web_host;


        if(empty($url))
        {
            echo json_encode(array('status'=>false,'msg'=>'链接不能为空'));
            return;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close($ch);
        if($output)
        {
            $pattern = '/<a(.*?)href="(.*?)"(.*?)>.*?<\/a>/i';
            $matches=array();
            preg_match_all($pattern,$output,$matches);
            $link_status=3;
           foreach($matches[2] as $key=>$row)
           {

               if($row==$web_host)
               {
                   $link_status=1;
                   if(strpos($matches[1][$key],'rel="nofollow"')!==FALSE ||strpos($matches[1][$key],"rel='nofollow'")!==FALSE || strpos($matches[3][$key],'rel="nofollow"')!==FALSE || strpos($matches[3][$key],"rel='nofollow'")!==FALSE)
                   {
                       $link_status=2;
                   }
                   break;
               }
           }
           echo json_encode(array('status'=>true,'link_status'=>$link_status,'msg'=>'检测完成'));
        }
        else
        {
            echo json_encode(array('status'=>false,'msg'=>'对方禁止检测'));
        }

    }


    private function getAllTypes()
    {
        $list=ORM::factory('model')->get_all();
        $arr=array("0"=>"首页","12"=>"目的地",'9'=>'机票');
        foreach($list as $v)
        {
            $arr[$v['id']]=$v['modulename'];
        }
        return $arr;
    }
	private function geneHtmlPosarr(){
        $extendArr=$this->getAllTypes();
        $list=array();
        foreach($extendArr as $k=>$v)
        {
            $list['type'.$k]=$v;
        }
        return $list;
    }


}