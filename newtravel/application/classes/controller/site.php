<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Site extends Stourweb_Controller{

   /*
    * 站点管理控制器
    * */

    public function before()
    {
        parent::before();
        $action = $this->request->action();
        if($action == 'index')
        {
            Common::getUserRight('site','slook');
        }
        if($action == 'add')
        {
            Common::getUserRight('site','sadd');
        }
        if($action == 'ajax_addsave')
        {
            Common::getUserRight('site','smodify');
        }
        if($action == 'ajax_editsave')
        {
            Common::getUserRight('site','smodify');
        }
        if($action == 'ajax_del')
        {
            Common::getUserRight('site','smodify');
        }

        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);
        $this->parentkey = $this->params['parentkey'];
        $this->itemid = $this->params['itemid'];
        $weblist = Common::getWebList();
        $this->assign('weblist',$weblist);
        $this->assign('helpico',Common::getIco('help'));


    }

    public function action_index()
    {
        $data=DB::select('id')->from('menu_new')->where('title','=','我的模板')->execute()->current();

        $dest_menu_id = DB::query(Database::SELECT,"select id from sline_menu_new where level=2 and (directory is null or directory='') and controller='destination' and method='destination'")->execute()->get('id');
        $templet_menu_id = DB::query(Database::SELECT,"select id from sline_menu_new where level=1 and (directory is null or directory='') and controller='templetmall' and method='my_templet'")->execute()->get('id');
        $webname_menu_id = DB::query(Database::SELECT,"select id from sline_menu_new where level=2 and (directory is null or directory='') and controller='config' and method='base'")->execute()->get('id');

        $this->assign('dest_menu_id',$dest_menu_id);
        $this->assign('templet_menu_id',$templet_menu_id);
        $this->assign('webname_menu_id',$webname_menu_id);
        $this->assign('menu',$data);
        $this->display('stourtravel/site');


    }
    /*
   * 站点添加
   * */
    public function action_add()
    {
        $this->assign('webid',$this->params['webid']);
        $this->assign('domain',$this->getBaseUrl());
        $this->assign('action','ajax_addsave');//保存方法
        $this->display('stourtravel/site_add');
    }

    /*
     * 底部保存添加(ajax)
     * */
    public function action_ajax_addsave()
    {


        $status = '0';
        $model = ORM::factory('weblist');
        $webprefix =  Arr::get($_POST,'prefix');//*.stourweb.com,子站域名前辍
        $weburl = St_Functions::get_http_prefix().$webprefix.$this->getBaseUrl(); //子站域名全称
        if($model->checkPrefixExist($webprefix))
        {
            $status = 'repeat';

        }
        else
        {
            $webid = $model->getLastWebid();
            $model->webprefix =  $webprefix;
            $model->webname = Arr::get($_POST,'webname');
            $model->webid = $webid;
            $model->weburl = $weburl;

            $model->create();

            if($model->saved())
            {
                $status = '1';
                $model->initData($webid,$webprefix);
            }
        }




        echo json_encode(array('status'=>$status));
    }
    /*
     * 底部修改保存(ajax)
     * */
    public function action_ajax_editsave()
    {
        $navid = Arr::get($_POST,'articleid');
        $model = ORM::factory('footernav',$navid);
        $model->servername =  Arr::get($_POST,'servername');
        $model->content = Arr::get($_POST,'content');
        if($model->update())
        {
            $flag = true;
        }
        echo json_encode(array('status'=>$flag));

    }
    /*
     * 删除
     * */
    public function action_ajax_del()
    {
        $wid = Arr::get($_GET,'id');
        $model = ORM::factory('destinations',$wid)->set('iswebsite',0)->save();
        if($model->saved())
        {
            $out['status'] = true;
            Model_Web::delNav($wid);
        }

        echo json_encode($out);

    }

    /*
    * 站点获取(ajax)
    * */
    public function action_ajax_get()
    {

        $webid = Arr::get($_GET,'webid');
        $arr = DB::select_array(array('id','kindname','weburl','webroot','webprefix'))->from('destinations')->where("iswebsite=1 and isopen=1")->order_by("displayorder",'asc')->execute()->as_array();
        foreach($arr as &$row)
        {
            $row['webname'] = Model_Sysconfig::get_configs($row['id'],'cfg_webname',true);
        }
        echo json_encode(array('trlist'=>$arr));
    }
    /*
    * 网站信息保存(ajax)
    * */
    public function action_ajax_save()
    {

        $model = new Model_Destinations();
        $model->save_web($_POST);
        echo json_encode(array('status'=>true));
    }

    //分析当前域名,返回主域名
    private function getBaseUrl()
    {
        $url = $GLOBALS['cfg_basehost'];
        
        $uarr = explode('.',$url);
        $k = 0;
        foreach($uarr as $value)
        {
            $out.= $k!=0 ? $value : '';
            $out .='.';
            $k++;
        }
        $out = substr($out,0,strlen($out)-1);
        return $out;

    }




}