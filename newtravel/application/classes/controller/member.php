<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Member extends Stourweb_Controller{

    /*
     * 会员配置总控制器
     *
     */
    public function before()
    {

        parent::before();
        $action = $this->request->action();

        if($action == 'index')
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
                Common::getUserRight('member',$user_action);


        }
        if($action == 'add')
        {
            Common::getUserRight('member','sadd');
        }
        if($action == 'edit')
        {
            Common::getUserRight('member','smodify');
        }
        if($action == 'ajax_save')
        {
            Common::getUserRight('member','smodify');
        }
        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);


    }

    public function action_index()
    {

        $action=$this->params['action'];
        if(empty($action))  //显示列表
        {
            $this->display('stourtravel/member/list');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $keyword=Arr::get($_GET,'keyword');
            $sort = json_decode(Arr::get($_GET, 'sort'));
            $sex = Arr::get($_GET,'sex');
            $verifystatus = Arr::get($_GET,'verifystatus');
            if(is_null($sort)){
                $order='order by a.jointime desc';
            }else{
                $order="order by a.".$sort[0]->property." ".$sort[0]->direction;
            }
            if(!empty($keyword))
            {
                $w ="where (a.nickname like '%{$keyword}%' or a.email like '%{$keyword}%' or a.mobile like '%{$keyword}%')";
            }
            else
            {
                $w = 'where a.mid>0';
            }
            if($verifystatus==1)
            {
                $w .= " and a.verifystatus in (0,1,3)";
            }
            elseif($verifystatus==2)
            {
                $w .= " and a.verifystatus=2";
            }
           if($sex)
           {
               $w .= " and a.sex='$sex'";
           }
			//虚拟会员过滤
			$virtual = Arr::get($_GET, 'virtual');
           if($virtual)
           {
               $w .= " and a.virtual='$virtual'";
           }
            $sql="select a.*  from sline_member as a $w $order limit $start,$limit";
            //echo $sql;
            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_member a " . $w)->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            $new_list=array();
            foreach($list as $k=>$v)
            {
                $v['logintime'] = Common::myDate('Y-m-d',$v['logintime']);
                $v['id'] = $v['mid'];
                $new_list[] = $v;
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

            if(is_numeric($id)) //
            {
                $model=ORM::factory('member',$id);
                $model->delete_clear();
                //会员附件表
                DB::delete('member_third')->where("mid={$id}")->execute();
            }
        }
        else if($action=='update')//更新某个字段
        {
            $id=Arr::get($_POST,'id');
            $field=Arr::get($_POST,'field');
            $val=Arr::get($_POST,'val');

            $value_arr[$field] = $val;
            $isupdated = DB::update('member')->set($value_arr)->where('mid','=',$id)->execute();
            if($isupdated)
                echo 'ok';
            else
                echo 'no';
        }
    }

    /*
     * 添加会员
     * */
    public function action_add()
    {
        $info['litpic'] =  $GLOBALS['cfg_public_url'] . 'images/member_nopic.png';
        $this->assign('info',$info);
        $this->assign('action','add');
        if($this->params['virtual'] == 1)
        {
            $this->display('stourtravel/member/add_virtual');
        }
        else
        {
            $this->display('stourtravel/member/add');
        }
    }
    /*
     * 修改会员
     * */
    public function action_edit()
    {
        $mid = $this->params['mid'];//会员id.
        $this->assign('action','edit');
        $info = DB::select()->from('member')->where('mid','=',$mid)->execute()->current();
        if(empty($info['litpic']))
        {
            $info['litpic'] =  $GLOBALS['cfg_public_url'] . 'images/member_nopic.png';
        }

        $menu_id = DB::select('id')->from('menu_new')->where('title','=','实名认证')->execute()->get('id');
        $this->assign('meunid',$menu_id);
        $info['grade'] = Common::member_rank($mid,array('return'=>'rankname'));
        $info['birth_date'] = explode('/',$info['birth_date']);
        if(St_Functions::is_normal_app_install('system_guide'))
        {
            $check_is_guide = DB::select('id')->from('guide')->where('mid','=',$mid)->execute()->current();
            if($check_is_guide)
            {
                $guide_menuid = DB::select('id')->from('menu_new')->where('title','=','导游列表')->execute()->get('id');
                $this->assign('guide_menuid',$guide_menuid);
                $info['is_guide'] = 1;//是导游
                $info['guide_id'] = $check_is_guide['id'];
            }
            else
            {
                $info['is_guide'] = 2;//不是导游
            }
        }
        $this->assign('info',$info);
        $this->display('stourtravel/member/edit');
    }
    /*
     * 保存
     * */
    public function action_ajax_save()
    {
        $action = Arr::get($_POST,'action');//当前操作
        $id = Arr::get($_POST,'mid');
        $status = false;
        //添加操作
        if($action == 'add' && empty($id))
        {
            $model = ORM::factory('member');
            $model->jointime = time();
            $model->email = Arr::get($_POST,'email');
            $model->mobile = Arr::get($_POST,'mobile');
        }
        else
        {
            $model = ORM::factory('member')->where('mid','=',$id)->find();
        }
        $pwd = Arr::get($_POST,'password');
        if($pwd)
        {

            $model->pwd = md5(Arr::get($_POST,'password'));
        }

		$model->logintime = time();
		$model->truename =Arr::get($_POST,'truename');
        $model->nickname = Arr::get($_POST,'nickname');
        $model->sex = Arr::get($_POST,'sex');
        $bairth_year = Arr::get($_POST,'bairth_year');
        $bairth_month = Arr::get($_POST,'bairth_month');
        $bairth_day = Arr::get($_POST,'bairth_day');
        $birth_date = $bairth_year.'/'.$bairth_month.'/'.$bairth_day;
        $model->birth_date = $birth_date;
        $model->constellation = Arr::get($_POST,'constellation');
        $model->wechat = Arr::get($_POST,'wechat');
        $model->qq = Arr::get($_POST,'qq');
        $model->native_place = Arr::get($_POST,'native_place');
        $model->signature = Arr::get($_POST,'signature');
        if($action=='add' && empty($id))
        {
            $model->create();
        }
        else
        {
            $model->update();
        }

        if($model->saved())
        {
            if($action=='add')
            {
                $productid = $model->mid; //插入的产品id
            }
            else
            {
                $productid =null;
            }

            $status = true;
        }
        echo json_encode(array('status'=>$status,'productid'=>$productid));

    }

	/**
     *  添加虚拟用户
     */
    public function action_ajax_save_virtual()
    {

        $num = Arr::get($_POST, 'num');

        //号码段
        $mobile_port = array("134", "135", "136", "137", "138", "139", "147", "150", "151", "152", "157", "158", "159", "187", "188", "130", "131", "132", "155", "156", "186", "133", "153", "180","182", "189");
        //密码
        $password = md5('stourweb_cms');
        //随机数组
		for($i=0;$i<$num;$i++)
		{
			$rand_num[$i] = array_rand($mobile_port, 1);
		}

		if(is_array($rand_num))
		{
			//$model = ORM::factory('member');
			foreach ($rand_num as $k)
			{
				$time = time();
				$name = $mobile_port[$k] . rand(10, 99) . '***';
				$insert_arr = array(
					'nickname' => $name,
					'pwd' => $password,
					'truename' => $name,
					'litpic' => self::rand_litpic(),
					'virtual' => 2,
					'jointime' => $time,
					'logintime' => $time,
				);
				
				DB::insert('member', array_keys($insert_arr))->values(array_values($insert_arr))->execute();

			}
		}
		else
		{
			$time = time();
			$name = $mobile_port[$rand_num] . rand(10, 99) . '***';
			$insert_arr = array(
				'nickname' => $name,
				'pwd' => $password,
				'truename' => $name,
				'litpic' => self::rand_litpic(),
				'virtual' => 2,
				'jointime' => $time,
				'logintime' => $time,
			);
				
			DB::insert('member', array_keys($insert_arr))->values(array_values($insert_arr))->execute();

		}
        echo json_encode(array('status'=>true));
    }
	
	/**
	 *	随机封面图
	 */
	public function rand_litpic()
	{
		$imgList='';

		$cacheFile = APPPATH . 'cache/headFile.php';
		if(file_exists($cacheFile))
		{
			$imgList = require($cacheFile);
		}
		else
		{
			$imgFolder = '../uploads/head/';
			mt_srand((double)microtime()*1000);
			
			$imgs = dir($imgFolder);

			while ($file = $imgs->read()) 
			{
				if (eregi("gif", $file) || eregi("jpg", $file) || eregi("png", $file))
				{
					$imgList .= "$file ";
				}
			} 
			
			closedir($imgs->handle);
			
			$imgList = explode(" ", $imgList);

			$imgList = array_filter($imgList);

			$fileData = var_export($imgList, 1);
			file_put_contents($cacheFile, "<?php\r\nreturn " . $fileData . ';');
		}

		$no = count($imgList)-1;
		
		$random = mt_rand(0, $no);
		$image = $imgList[$random];


		return '/uploads/head/' . $image;
	}

    /*
     * ajax检测是否存在
     * */
    public function action_ajax_check()
    {
        $field = $this->params['type'];
        $val = Arr::get($_POST,'val');//值
        $mid = Arr::get($_POST,'mid');//会员id
        $flag = Model_Member::checkExist($field,$val,$mid);
        echo $flag;
    }

   /*
    * 会员订单查看
    * */
    public function action_vieworder()
    {
        $mid = $this->params['mid'];
        $list = DB::select()->from('member_order')->where('memberid','=',$mid)->execute()->as_array();

        $this->assign('orderlist',$list);
        $this->display('stourtravel/member/orderlist');
        
    }


    /**
     * 注册协议
     */
    public function action_agreement()
    {
        $fields = array('cfg_member_agreement_open','cfg_member_agreement_title','cfg_member_agreement');
        $config = Model_Sysconfig::get_configs(0,$fields);
        $this->assign('config',$config);
        $this->display('stourtravel/member/agreement');
    }

    /**
     * 弹出框选择会员
     */
    public function action_dialog_member_list()
    {
        $action=$this->params['action'];
        if(empty($action))  //显示列表
        {
            $this->assign('virtual',$this->params['virtual']);
            $this->display('stourtravel/member/dialog_member_list');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $keyword=Arr::get($_GET,'keyword');
            $sort = json_decode(Arr::get($_GET, 'sort'));
            if(is_null($sort)){
                $order='order by a.jointime desc';
            }else{
                $order="order by a.".$sort[0]->property." ".$sort[0]->direction;
            }
            $w='where a.mid>0 ';
            if(!empty($keyword))
            {
                $w .="and (a.nickname like '%{$keyword}%' or a.email like '%{$keyword}%' or a.mobile like '%{$keyword}%')";
            }
            if(isset($_GET['virtual']) && $_GET['virtual']==0){
                $w .= " and a.virtual!=2 ";
            }

            $sql="select a.*  from sline_member as a $w $order limit $start,$limit";
            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_member a $w")->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            $new_list=array();
            foreach($list as $k=>$v)
            {
                $v['logintime'] = Common::myDate('Y-m-d',$v['logintime']);
                $v['id'] = $v['mid'];
                $new_list[] = $v;
            }
            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$new_list;
            $result['success']=true;
            echo json_encode($result);
        }

    }


    /**
     * @function  会员实名认证列表
     */
    public function action_verifystatus_list()
    {

        $menu_id = DB::select('id')->from('menu_new')->where('title','=','实名认证')->execute()->get('id');
        $this->assign('meunid',$menu_id);
        $action=$this->params['action'];
        if(empty($action))  //显示列表
        {
            $this->display('stourtravel/member/verifystatus/verifystatus_list');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $keyword=Arr::get($_GET,'keyword');
            $sort = json_decode(Arr::get($_GET, 'sort'));
            $sex = Arr::get($_GET,'sex');
            $verifystatus = Arr::get($_GET,'verifystatus');
            if(is_null($sort)){
                $order='order by a.jointime desc';
            }else{
                $order="order by a.".$sort[0]->property." ".$sort[0]->direction;
            }
            if(!empty($keyword))
            {
                $w ="where (a.nickname like '%{$keyword}%' or a.email like '%{$keyword}%' or a.mobile like '%{$keyword}%')";
            }
            else
            {
                $w = 'where a.mid>0';
            }
            if(!is_null($verifystatus) && $verifystatus!='all'){
                $w .= " and a.verifystatus=".$verifystatus;
            }
            if($sex)
            {
                $w .= " and a.sex='$sex'";
            }
            $sql="select a.*  from sline_member as a $w $order limit $start,$limit";
            //echo $sql;
            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_member a $w")->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            $new_list=array();
            foreach($list as $k=>$v)
            {
                $v['logintime'] = Common::myDate('Y-m-d',$v['logintime']);
                $v['id'] = $v['mid'];
                $new_list[] = $v;
            }
            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$new_list;
            $result['success']=true;

            echo json_encode($result);
        }
        else if($action=='check')   //审核
        {
            $mid = Arr::get($_GET,'mid');
            $info = DB::select()->from('member')->where('mid','=',$mid)->execute()->current();
            $info['idcard_pic'] = (array)json_decode($info['idcard_pic']);
            $this->assign('info',$info);
            $this->display('stourtravel/member/verifystatus/verifystatus_check');
        }
        else if($action=='do_check')
        {
            $mid = Arr::get($_GET,'mid');
            $status = Arr::get($_POST,'status');
            if($status==1)
            {

                $cardid = DB::select('cardid')->from('member')->where('mid','=',$mid)->execute()->get('cardid');
                $cardinfo = Common::check_idcard($cardid);
                if(!$cardinfo)
                {
                    echo json_encode(array('status'=>0));
                    return false;
                }
                else
                {
                    $cardinfo['verifystatus'] = 2;
                    DB::update('member')->set($cardinfo)->where('mid','=',$mid)->execute();
                    echo json_encode(array('status'=>1,'type'=>2));
                }

            }
            else
            {
                DB::update('member')->set(array('verifystatus'=>3))->where('mid','=',$mid)->execute();
                echo json_encode(array('status'=>1,'type'=>3));
            }

        }
        else if($action=='modify'||$action=='show') //认证详情
        {
            $mid = Arr::get($_GET,'mid');
            $info = DB::select()->from('member')->where('mid','=',$mid)->execute()->current();
            $info['idcard_pic'] = (array)json_decode($info['idcard_pic']);
            $this->assign('info',$info);
            $this->assign('action',$action);
            $this->display('stourtravel/member/verifystatus/verifystatus_edit');
        }
        else if($action=='do_modify')
        {
            $status = false;
            $mid = Arr::get($_POST,'mid');
            $verifystatus = Arr::get($_POST,'verifystatus');
            $truename = Arr::get($_POST,'truename');
            $cardid = Arr::get($_POST,'cardid');
            $front_pic = Arr::get($_POST,'front_pic');
            $verso_pic = Arr::get($_POST,'verso_pic');
            $uptate_arr = array(
                'verifystatus'=>$verifystatus,
                'cardid'=>$cardid,
                'truename'=>$truename,
                'idcard_pic'=>json_encode(array('front_pic'=>$front_pic,'verso_pic'=>$verso_pic)),
            );

            $rsn = DB::update('member')->set($uptate_arr)->where('mid','=',$mid)->execute();

            if($rsn!==false)
            {
                $status = true;
            }
            echo json_encode(array('status'=>$status));
            exit;

        }


    }




    /**
     * 上传图片
     */
    public function action_ajax_upload_picture()
    {
        //if(!$this->request->is_ajax())exit;
        $filedata = Arr::get($_FILES, 'filedata');
        $storepath = UPLOADPATH . '/member/';
        if (!file_exists($storepath))
        {
            mkdir($storepath);
        }
        $filename = uniqid();
        $out = array();
        $ext = end(explode('.', $filedata['name']));

        if (move_uploaded_file($filedata['tmp_name'], $storepath . $filename . '.' . $ext))
        {
            $out['status'] = 1;
            $out['litpic'] = '/uploads/member/' . $filename . '.' . $ext;
        }
        echo json_encode($out);
    }

/*
     * 提现列表
     */
    public function action_withdraw()
    {
        $action = $this->params['action'];
        if (empty($action))  //显示列表
        {
            $this->display('stourtravel/member/withdraw');
        }
        else if ($action == 'read')    //读取列表
        {
            $start = Arr::get($_GET, 'start');
            $limit = Arr::get($_GET, 'limit');
            $keyword = Arr::get($_GET, 'keyword');

            $order = 'order by a.addtime desc';
            $sort = json_decode($_GET['sort'], true);
            if(!empty($sort[0]))
            {
                $order = ' order by a.'.$sort[0]['property'].' '.$sort[0]['direction'].' ,a.addtime desc';
            }

            $w=" where a.id is not null ";
            if (!empty($keyword))
            {
                $w.= " and (a.bankcardnumber='{$keyword}' || a.bankaccountname like '%{$keyword}%' || b.nickname like '%{$keyword}%' || b.mobile='{$keyword}' || b.email='{$keyword}') ";
            }

            $sql = "select a.*,b.mobile,b.email,b.nickname from sline_member_withdraw a inner join sline_member b on a.memberid=b.mid {$w} {$order} limit {$start},{$limit}";
            $total  = DB::query(Database::SELECT, "select count(*) as num from sline_member_withdraw a inner join sline_member b on a.memberid=b.mid {$w}")->execute()->get('num');
            $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $new_list = array();
            foreach ($list as $k => $v)
            {
                $v['account'] = empty($v['mobile'])?$v['email']:$v['mobile'];
                $v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
                $v['finishtime'] =!empty($v['finishtime'])? date('Y-m-d H:i:s',$v['finishtime']):'';
                $v['status_name'] = Model_Member_Withdraw::get_status_name($v['status']);
                $new_list[] = $v;
            }
            $result['total'] = $total;
            $result['lists'] = $new_list;
            $result['success'] = true;

            echo json_encode($result);
        }

        else if ($action == 'delete') //删除某个记录
        {

        }
        else if ($action == 'update')//更新某个字段
        {

        }
    }
    /*
     * 交易记录
     */
    public function action_cashlog()
    {

        $action = $this->params['action'];
        if (empty($action))  //显示列表
        {
            $this->display('stourtravel/member/cashlog');
        }
        else if ($action == 'read')    //读取列表
        {
            $start = Arr::get($_GET, 'start');
            $limit = Arr::get($_GET, 'limit');
            $keyword = Arr::get($_GET, 'keyword');

            $order = 'order by a.addtime desc';
            $sort = json_decode($_GET['sort'], true);
            if(!empty($sort[0]))
            {
               $order = ' order by a.'.$sort[0]['property'].' '.$sort[0]['direction'].' ,a.addtime desc';
            }

            $w=" where a.id is not null ";
            if (!empty($keyword))
            {
                $w.= " and (b.nickname like '%{$keyword}%' || b.mobile='{$keyword}' || b.email='{$keyword}') ";
            }

            $sql = "select a.*,b.mobile,b.email,b.nickname from sline_member_cash_log a inner join sline_member b on a.memberid=b.mid {$w} {$order} limit {$start},{$limit}";
            $total  = DB::query(Database::SELECT, "select count(*) as num from sline_member_cash_log a inner join sline_member b on a.memberid=b.mid {$w}")->execute()->get('num');
            $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $new_list = array();
            foreach ($list as $k => $v)
            {
                $v['account'] = empty($v['mobile'])?$v['email']:$v['mobile'];
                $v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
                $new_list[] = $v;
            }
            $result['total'] = $total;
            $result['lists'] = $new_list;
            $result['success'] = true;

            echo json_encode($result);
        }

        else if ($action == 'delete') //删除某个记录
        {

        }
        else if ($action == 'update')//更新某个字段
        {

        }
    }
    /*
     * 提现审核
     */
    public function action_withdraw_edit()
    {
        $id = $this->params['id'];
        $way_names = array('bank'=>'银行卡','alipay'=>'支付宝','weixin'=>'微信');
        $model = ORM::factory('member_withdraw',$id);
        $info = $model->as_array();

        $member_info = DB::select()->from('member')->where('mid','=',$info['memberid'])->execute()->current();
        $info['account'] = empty($member_info['mobile'])?$member_info['email']:$member_info['mobile'];
        $info['nickname'] = $member_info['nickname'];
        $info['way_name'] = $way_names[$info['way']];


        $this->assign('info',$info);
        $this->display('stourtravel/member/withdraw_edit');
    }
    /*
     * 审核保存
     */
    public function action_ajax_withdraw_save()
    {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $audit_description = $_POST['audit_description'];

        $curtime = time();
        $db = Database::instance();
        $db->begin();
        try {
            $model = ORM::factory('member_withdraw', $id);
            if(!$model->loaded())
            {
                throw new Exception('提现不存在');
            }
            if ($model->status != 0) {
                throw new Exception('已审核过，不能重复审核');
            }
            if($status==$model->status)
            {
                throw new Exception('未改变审核状态');
            }

            $model->status = $status;
            $model->audit_description = $audit_description;
            $model->finishtime = $curtime;
            $model->save();
            if (!$model->saved())
            {
                throw new Exception('审核未成功，请重试');
            }
            $member_model = ORM::factory('member',$model->memberid);
            if(!$member_model->loaded())
            {
                throw new Exception('会员不存在');
            }

            $withdrawamount = floatval($model->withdrawamount);
            $money = floatval($member_model->money);
            $money_frozen = floatval($member_model->money_frozen);
            if($model->status==1)
            {
                if($withdrawamount>$money)
                {
                    throw new Exception('提现金额高于存款总额');
                }
                if($withdrawamount>$money_frozen)
                {
                    throw new Exception('提现金额高于冻结总额');
                }
                $member_model->money-=$withdrawamount;
                $member_model->money_frozen-=$withdrawamount;
                $member_model->save();
                if(!$member_model->saved())
                {
                    throw new Exception('审核未成功，请重试');
                }
                $log_des = '提现审核完成，解冻并扣除'.$withdrawamount.'元';
                $log_result = Model_Member_Cash_Log::add_log($member_model->mid,1,$withdrawamount,$log_des,array('withdrawid'=>$model->id));
                if(!$log_result)
                {
                    throw new Exception('审核未成功，请重试');
                }
            }
            else if($model->status==2)
            {
                if($withdrawamount>$money_frozen)
                {
                    throw new Exception('提现金额高于冻结总额');
                }
                $member_model->money_frozen-=$withdrawamount;
                $member_model->save();
                if(!$member_model->saved())
                {
                    throw new Exception('审核未成功，请重试');
                }
                $log_des = '提现审核未通过，解冻'.$withdrawamount.'元';
                $log_result = Model_Member_Cash_Log::add_log($member_model->mid,3,$withdrawamount,$log_des,array('withdrawid'=>$model->id));
                if(!$log_result)
                {
                    throw new Exception('审核未成功，请重试');
                }

            }

            $db->commit();
            echo json_encode(array('status'=>true,'msg'=>'审核完成'));

        }catch (Exception $excep)
        {
            $db->rollback();
            echo json_encode(array('status'=>false,'msg'=>$excep->getMessage()));
        }

    }




}