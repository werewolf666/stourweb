<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Currency extends Stourweb_Controller{
    public function before()
    {
        parent::before();
        $action = $this->request->action();

        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);

    }
     /*
	文章列表  
	 */
	public function action_list()
	{
		$action=$this->params['action'];
		if(empty($action))  //显示线路列表页
		{
           $this->assign('kindmenu',Common::getConfig('menu_sub.currencykind'));//分类设置项
		   $this->display('stourtravel/currency/list');
		}
		else if($action=='read')    //读取列表
		{
			$start=Arr::get($_GET,'start');
			$limit=Arr::get($_GET,'limit');
			$keyword=Arr::get($_GET,'keyword');
            $keyword = Common::getKeyword($keyword);
			$sort=json_decode(Arr::get($_GET,'sort'),true);

			$sql="select * from sline_currency order by isopen desc limit $start,$limit";
			$totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_article")->execute()->as_array();
			$list=DB::query(Database::SELECT,$sql)->execute()->as_array();

            $codeSql="select code from sline_currency";
            $codeList=DB::query(Database::SELECT,$codeSql)->execute()->as_array();
            $codeArr=array();
            foreach($codeList as $val)
            {
                $codeArr[]=$val['code'];
            }

            foreach($list as &$v)
            {
                 $currency=Currency_St::factory($v['code']);
                 $ratioInfoArr=array();
                 foreach($codeArr as $oneCode)
                 {
                     $ratio=$currency->get_ratio_units($oneCode);
                     $ratioInfoArr[$oneCode]=$ratio;
                 }
                 $v['ratio']=$ratioInfoArr;
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
		   if(is_numeric($id)) 
		   {
		    $model=ORM::factory('currency',$id);
		    $model->deleteClear();
		   }
		  
		   
		}
        else if($action=='updateratio')
        {
            $keys=array_keys($_POST);
            $key1=$keys[0];
            $key2=$keys[1];
            $rateModel=ORM::factory('currency_rate')->where_open()->where('currencycode1','=',$key1)->and_where('currencycode2','=',$key2)->where_close()->or_where_open()->where('currencycode1','=',$key2)
                ->and_where('currencycode2','=',$key1)->or_where_close()->find();

            $rateModel->currencycode1=$key1;
            $rateModel->currencycode2=$key2;
            $rateModel->ratio1=$_POST[$key1];
            $rateModel->ratio2=$_POST[$key2];
            $rateModel->save();

        }
		else if($action=='update')//更新某个字段
		{
			$id=Arr::get($_POST,'id');
			$field=Arr::get($_POST,'field');
			$val=Arr::get($_POST,'val');
            if(is_numeric($id))
            {
                $model=ORM::factory('currency',$id);
            }
            if($model->id)
            {
                $model->$field=$val;
                if($field=='kindlist') {
                    $model->$field = implode(',', Model_Destinations::getParentsStr($val));
                }
                else if($field=='attrid')
                {
                    $model->$field=implode(',',Model_Attrlist::getParentsStr($val,4));
                }
                $model->save();
                if($model->saved())
                    echo 'ok';
                else
                    echo 'no';
            }

		}
	}
    public function action_config()
    {
        $currencyList=ORM::factory('currency')->get_all();

        $sysConfigModel=new Model_Sysconfig();
        $configs=$sysConfigModel->getConfig(0);
        $this->assign('frontcode',$configs['cfg_front_currencycode']);
        $this->assign('backcode',$configs['cfg_back_currencycode']);
        $this->assign('precise',$configs['cfg_front_currency_precise']);
        $this->assign('list',$currencyList);
        $this->display('stourtravel/currency/config');
    }
    public function action_ajax_getrate()
    {
        $frontCode=$_POST['frontcode'];
        $backCode=$_POST['backcode'];
        $currencyObj=Currency_St::factory($frontCode);
        $ratios=$currencyObj->get_ratio_units($backCode);
        echo json_encode(array('status'=>true,'data'=>$ratios));
    }
    public function action_ajax_saveconfig()
    {
        $frontCode=$_POST['cfg_front_currencycode'];
        $backCode=$_POST['cfg_back_currencycode'];
        $frontRatio=$_POST['front_ratio'];
        $backRatio=$_POST['back_ratio'];


         try {

             if ($frontCode != $backCode) {

                 if(!is_numeric($frontRatio)||!is_numeric($backRatio))
                 {
                     throw new Exception("汇率不能为空");
                 }

                 $rateModel = ORM::factory('currency_rate')->where_open()->where('currencycode1', '=', $frontCode)->and_where('currencycode2', '=', $backCode)->where_close()->or_where_open()->where('currencycode1', '=', $backCode)
                     ->and_where('currencycode2', '=', $frontCode)->or_where_close()->find();

                 $rateModel->currencycode1 = $frontCode;
                 $rateModel->currencycode2 = $backCode;
                 $rateModel->ratio1 = $frontRatio;
                 $rateModel->ratio2 = $backRatio;
                 $rateModel->save();
                 if (!$rateModel->saved() || !$rateModel->loaded()) {
                     throw new Exception("保存失败，请重试");
                 }
             }
             $currencyArr = array('webid' => 0);
             $currencyArr['cfg_front_currencycode'] = $frontCode;
             $currencyArr['cfg_back_currencycode'] = $backCode;
             $currencyArr['cfg_front_currency_precise']=intval($_POST['cfg_front_currency_precise']);
             $sysconfigModel = new Model_Sysconfig();
             $sysconfigModel->saveConfig($currencyArr);

             echo json_encode(array('status' => true, 'msg' => '保存成功'));
         }
         catch (Exception $excep)
         {
             echo json_encode(array('status' => false, 'msg' =>$excep->getMessage()));
         }



    }

}