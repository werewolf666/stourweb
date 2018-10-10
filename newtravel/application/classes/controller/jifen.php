<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Jifen extends Stourweb_Controller
{


    public function before()
    {
        parent::before();
    }
    /*
     * 积分策略列表
     * */
    public function action_index()
    {
        $action = $this->params['action'];
        $typeid = $this->params['typeid'];
        $this->assign('typeid', $typeid);
        if (empty($action))  //显示列表
        {
            $products = DB::query(Database::SELECT,"select distinct a.id,a.modulename,b.shortname from sline_model a left join sline_nav b on a.id=b.typeid and b.issystem=1 and b.webid=0 where a.isopen=1 and (is_commentable=1 or is_publishable=1 or is_orderable=1)")->execute()->as_array();
            foreach($products as &$product)
            {
                $product['modulename'] = !empty($product['shortname'])?$product['shortname']:$product['modulename'];
            }

            $this->assign('products',$products);
            $this->display('stourtravel/jifen/index');
        }
        else if ($action == 'read')    //读取列表
        {
            $start = Arr::get($_GET, 'start');
            $limit = Arr::get($_GET, 'limit');
            $keyword = Arr::get($_GET, 'keyword');
            $sort = json_decode($_GET['sort'], true);
            $issystem = $_GET['issystem'];
            $section = $_GET['section'];
            $rewardway = $_GET['rewardway'];
            $isopen = $_GET['isopen'];
            $typeid = $_GET['typeid'];


            $order = " order by addtime desc ";

             ///需要恢复的地方
            $w = " where id is not null and  label!='sys_member_sign'";//and (issystem!=1 or section not in (0,3)) and typeid!=106 ";

            $install_typeids=$this->get_installed_typeids();
            if(!empty($install_typeids))
            {
                $install_typeids_str=implode(',',$install_typeids);
                $w.=" and case when issystem=1 && section in (1,2,3) && typeid not in ({$install_typeids_str}) then 0 else 1 end";
            }

            if(!empty($typeid) && $typeid!=-1)
            {
                $w.=" and find_in_set({$typeid},typeid) ";
            }

            if (isset($issystem) && $issystem != -1)
            {
                $w.=" and issystem={$issystem}";
            }
            if (isset($section) && $section != -1)
            {
                $w.=" and section={$section}";
            }
            if (isset($rewardway) && $rewardway != -1)
            {
                $w.=" and rewardway={$rewardway}";
            }
            if (isset($isopen) && $isopen != -1)
            {
                $w.=" and isopen={$isopen}";
            }

            if(!empty($keyword))
            {
                $w.=" and (title like '%{$keyword}%' or label like '%{$keyword}%')";
            }

            if(!empty($sort[0]))
            {
                $order = " order by ".$sort[0]['property'].' '.$sort[0]['direction'];
            }

            $sql = "select * from sline_jifen {$w} {$order} limit {$start},{$limit}";

            $sql_num = "select count(*) as num from sline_jifen {$w}";
            $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
            $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
            foreach($list as &$v)
            {
                $v['section_name'] = Model_Jifen::$field_section_names[$v['section']];
                $v['typeid_names'] = $this->get_typeid_names($v['typeid']);
                //$v['issystem'] = Model_Jifen::$field_issystem_names[$v['issystem']];
            }

            $result['total'] = $num;
            $result['lists'] = $list;
            $result['success']=true;
            echo json_encode($result);
        }
        else if ($action == 'save')   //保存字段
        {

        }
        else if ($action == 'delete') //删除某个记录
        {

            $rawdata=file_get_contents('php://input');
            $data=json_decode($rawdata,true);
            $id=$data['id'];

            if(is_numeric($id)) //
            {
                $model=ORM::factory('jifen',$id);
                $model->delete_clear();
            }
        }
        else if ($action == 'update')//更新某个字段
        {
            $id = Arr::get($_POST, 'id');
            $field = Arr::get($_POST, 'field');
            $val = Arr::get($_POST, 'val');
            $model = ORM::factory('jifen',$id);
            $model->$field = $val;
            $model->save();
            echo 'ok';
        }
    }
    /*
     * 积分抵现策略列表
     */
    public function action_jifentprice()
    {
        $action = $this->params['action'];
        $typeid = $this->params['typeid'];
        $this->assign('typeid', $typeid);
        if (empty($action))  //显示列表
        {
            $products = DB::query(Database::SELECT,"select distinct a.id,a.modulename,b.shortname from sline_model a left join sline_nav b on a.id=b.typeid and b.issystem=1 and b.webid=0 where a.isopen=1 and is_orderable=1")->execute()->as_array();
            foreach($products as &$product)
            {
                $product['modulename'] = !empty($product['shortname'])?$product['shortname']:$product['modulename'];
            }
            $this->assign('products',$products);
            $this->display('stourtravel/jifen/jifentprice');
        }
        else if ($action == 'read')    //读取列表
        {
            $start = Arr::get($_GET, 'start');
            $limit = Arr::get($_GET, 'limit');
            $keyword = Arr::get($_GET, 'keyword');
            $sort = json_decode($_GET['sort'], true);
            $issystem = $_GET['issystem'];
            $isopen = $_GET['isopen'];
            $typeid = $_GET['typeid'];


            $order = " order by addtime desc ";
            ///需要恢复的地方
            $w = " where id is not null ";

            $install_typeids=$this->get_installed_typeids();
            if(!empty($install_typeids))
            {
                $install_typeids_str=implode(',',$install_typeids);
                $w.=" and case when issystem=1 && typeid not in ({$install_typeids_str}) then 0 else 1 end";
            }

            if(!empty($typeid) && $typeid!=-1)
            {
                $w.=" and find_in_set({$typeid},typeid) ";
            }

            if (isset($issystem) && $issystem != -1)
            {
                $w.=" and issystem={$issystem}";
            }
            if (isset($isopen) && $isopen != -1)
            {
                $w.=" and isopen={$isopen}";
            }

            if(!empty($keyword))
            {
                $w.=" and (title like '%{$keyword}%' or label like '%{$keyword}%')";
            }

            if(!empty($sort[0]))
            {
                $order = " order by ".$sort[0]['property'].' '.$sort[0]['direction'];
            }


            $sql = "select * from sline_jifen_price {$w} {$order} limit {$start},{$limit}";

            $sql_num = "select count(*) as num from sline_jifen_price {$w}";
            $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
            $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
            foreach($list as &$v)
            {
               // $v['issystem'] = Model_Jifen::$field_issystem_names[$v['issystem']];
                $v['starttime'] = empty($v['starttime'])?'':date('Y-m-d',$v['starttime']);
                $v['endtime'] = empty($v['endtime'])?'': date('Y-m-d',$v['endtime']);
                $v['typeid_names'] = $this->get_typeid_names($v['typeid']);
            }
            $result['total'] = $num;
            $result['lists'] = $list;
            $result['success']=true;
            echo json_encode($result);
        }
        else if ($action == 'save')   //保存字段
        {

        }
        else if ($action == 'delete') //删除某个记录
        {
            $rawdata=file_get_contents('php://input');
            $data=json_decode($rawdata,true);
            $id=$data['id'];

            if(is_numeric($id)) //
            {
                $model=ORM::factory('jifen_price',$id);
                $model->delete_clear();
            }
        }
        else if ($action == 'update')//更新某个字段
        {
            $id = Arr::get($_POST, 'id');
            $field = Arr::get($_POST, 'field');
            $val = Arr::get($_POST, 'val');
            $model = ORM::factory('jifen_price',$id);
            $model->$field = $val;
            $model->save();
            echo 'ok';
        }
    }
    /*
     * 积分设置
     */
    public function action_config()
    {
        $this->display('stourtravel/jifen/config');
    }

    /*
     * 添加积分策略
     */
    public function action_add()
    {
        $orderable_products = $this->get_orderable_products();

        $this->assign('orderable_products',$orderable_products);
        $this->display('stourtravel/jifen/edit');
    }
    /*
     * 编辑积分策略
     */
    public function action_edit()
    {
        $id = $this->params['id'];
        $info = ORM::factory('jifen',$id)->as_array();
        $info['disable_fields'] = explode(',',$info['disable_fields']);
        $info['typeid_arr'] = explode(',',$info['typeid']);
        $info['typeid_names'] = $this->get_typeid_names($info['typeid']);
        $info['section_name'] = Model_Jifen::$field_section_names[$info['section']];
        $info['frequency_type_exclude'] = empty($info['frequency_type_exclude'])?'':explode(',',$info['frequency_type_exclude']);
        //$info['label'] = $info['issystem']==0?substr($info['label'],5):$info['label'];
        $orderable_products = $this->get_orderable_products();
        $commentable_products = $this->get_commentable_products();
        $member_products = $this->get_member_products();
        $this->assign('orderable_products',$orderable_products);
        $this->assign('commentable_products',$commentable_products);
        $this->assign('member_products',$member_products);
        $this->assign('info',$info);
        $this->display('stourtravel/jifen/edit');
    }

    /*
     * 保存积分策略
     */
    public function action_ajax_save()
    {

        $id = $_POST['id'];
        $title = $_POST['title'];
        $isopen = empty($_POST['isopen']) ? 0 : $_POST['isopen'];

        $section = empty($_POST['section']) ? 0 : $_POST['section'];
        $typeid = empty($_POST['typeid']) ? 0 : $_POST['typeid'];
        $typeid = implode(',',$typeid);
        $rewardway = empty($_POST['rewardway']) ? 0 : $_POST['rewardway'];
        $value = empty($_POST['value']) ? 0 : $_POST['value'];
        $frequency_type = empty($_POST['frequency_type']) ? 0 : $_POST['frequency_type'];
        $frequency = empty($_POST['frequency_'.$frequency_type]) ? 0 : $_POST['frequency_'.$frequency_type];
        $curtime = time();

        $model = ORM::factory('jifen', $id);
        $org_typeid = $model->typeid;
        $is_section_changed = false;
        if ($model->issystem == 1) {
            $model->isopen = $isopen;
            $model->value = $value;
            $disable_fields = explode(',',$model->disable_fields);
            if(!in_array('rewardway',$disable_fields))
            {
                $model->rewardway = $rewardway;
            }
            if(!in_array('frequency_type',$disable_fields))
            {
                $model->frequency_type = $frequency_type;
            }
            $model->frequency = $frequency;
        } else {
            if($model->loaded() && $model->section != $section)
            {
                $is_section_changed=true;
            }
            if(!$model->loaded())
            {
                $model->label = 'user_'.time().mt_rand(10,99);
            }
            $model->title = $title;
            $model->isopen = $isopen;
            $model->section = $section;
            $model->typeid = $typeid;
            $model->rewardway = $rewardway;
            $model->value = $value;
            $model->issystem = 0;
            $model->frequency_type = $frequency_type;
            if($model->section==1)
            {
                $model->frequency_type = 0;
            }
            $model->frequency = $frequency;
            $model->addtime = $curtime;
        }
        $model->save();
        if ($model->saved())
        {
            if(($org_typeid!=$typeid || $is_section_changed) && $model->section!=3)
            {
                Model_Jifen::clear_all_jifenbook($org_typeid,$model->id);
            }
            echo json_encode(array('status'=>true,'msg'=>'','id'=>$model->id));
        }
        else
        {
            echo json_encode(array('status' => false));
        }
    }
    /*
     * 添加积分抵现策略
     */
    public function action_add_tprice()
    {
        $orderable_products = $this->get_orderable_products();
        $this->assign('orderable_products',$orderable_products);
        $this->display('stourtravel/jifen/edit_tprice');
    }
    /*
     * 编辑积分抵现策略
     */
    public function action_edit_tprice()
    {
        $orderable_products = $this->get_orderable_products();
        $id = $this->params['id'];
        $info = ORM::factory('jifen_price',$id)->as_array();
       // $info['label'] = $info['issystem']==0?substr($info['label'],5):$info['label'];
        $info['starttime'] = date('Y-m-d',$info['starttime']);
        $info['endtime'] = date('Y-m-d',$info['endtime']);
        $info['typeid_arr'] = explode(',',$info['typeid']);
        $info['typeid_names'] = $this->get_typeid_names($info['typeid']);
        $this->assign('orderable_products',$orderable_products);
        $this->assign('info',$info);
        $this->display('stourtravel/jifen/edit_tprice');
    }
    /*
     * 积分抵现策略保存
     */
    public function action_ajax_tprice_save()
    {
        $id = $_POST['id'];

        $title = $_POST['title'];
        $isopen = empty($_POST['isopen']) ? 0 : $_POST['isopen'];
        $issystem = empty($_POST['issystem']) ? 0 : $_POST['issystem'];
        $typeid = empty($_POST['typeid']) ? 0 : $_POST['typeid'];
        $typeid = implode(',',$typeid);
        $toplimit = empty($_POST['toplimit']) ? 0 : $_POST['toplimit'];
        $expiration_type = empty($_POST['expiration_type']) ? 0 : $_POST['expiration_type'];
        $starttime = empty($_POST['starttime'])?0:strtotime($_POST['starttime']);
        $endtime = empty($_POST['endtime_'.$expiration_type])?0:strtotime($_POST['endtime_'.$expiration_type]);
        $curtime=time();


        $model = ORM::factory('jifen_price', $id);
        $org_typeid = $model->typeid;
        if ($model->issystem == 1) {
            $model->isopen = $isopen;
            $model->toplimit = $toplimit;
            //$model->expiration_type= $expiration_type;
            //$model->starttime = $starttime;
            //$model->endtime = $endtime;
        } else {
            if(!$model->loaded())
            {
                $model->label = 'user_'.time().mt_rand(10,99);
            }
            $model->title = $title;
            $model->isopen = $isopen;
            $model->typeid = $typeid;
            $model->issystem = 0;
            $model->toplimit = $toplimit;
            $model->expiration_type= $expiration_type;
            $model->starttime = $starttime;
            $model->endtime = $endtime;
            $model->addtime = $curtime;
        }
        $model->save();
        if ($model->saved())
        {
            if($org_typeid!=$typeid)
            {
                Model_Jifen_Price::clear_all_jifentprice($org_typeid,$model->id);
            }
            echo json_encode(array('status'=>true,'msg'=>'','id'=>$model->id));
        }
        else
        {
            echo json_encode(array('status' => false));
        }
    }

    /*
     * 验证积分策略的调用标识
     */
    public function action_ajax_check_label()
    {
        $id = $_POST['id'];
        $label = 'user_'.$_POST['label'];
        $num = DB::query(Database::SELECT,"select count(*) as num from sline_jifen where label='{$label}' and id!='{$id}'")->execute()->get('num');
        if($num>0)
            echo json_encode(false);
        else
            echo json_encode(true);

    }
    /*
     * 验证积分抵现的调用标识
     */
    public function action_ajax_check_tprice_label()
    {
        $id = $_POST['id'];
        $label = 'user_'.$_POST['label'];
        $num = DB::query(Database::SELECT,"select count(*) as num from sline_jifen_price where label='{$label}' and id!='{$id}'")->execute()->get('num');
        if($num>0)
            echo json_encode(false);
        else
            echo json_encode(true);
    }
    /*
     * 选择积分策略产品的对话框
     */
    public function action_dialog_get_products()
    {
        $jifenid = $this->params['jifenid'];
        $typeid = $this->params['typeid'];
        $this->assign('jifenid',$jifenid);
        $this->assign('typeid',$typeid);
        $this->display('stourtravel/jifen/dialog_get_products');
    }
    /*
     * 选择积分抵现产品的对话框
     */
    public function action_dialog_get_tprice_products()
    {
        $jifenid = $this->params['jifenid'];
        $typeid = $this->params['typeid'];
        $this->assign('jifenid',$jifenid);
        $this->assign('typeid',$typeid);
        $this->display('stourtravel/jifen/dialog_get_tprice_products');
    }

    /*
     * 选择预订积分策略
     */
    public function action_dialog_choose_jifenbook()
    {
        $jifenid = $_GET['jifenid'];
        $typeid = $_GET['typeid'];
        $selector = urldecode($_GET['selector']);
        $this->assign('jifenid',$jifenid);
        $this->assign('typeid',$typeid);
        $this->assign('selector',$selector);
        $this->display('stourtravel/jifen/dialog_choose_jifenbook');
    }
    /*
     * dialog_choose_bookjifen的数据源
     */
    public function action_ajax_choose_jifenbook()
    {
        $typeid = $_POST['typeid'];
        $keyword = $_POST['keyword'];
        $page = $_POST['page'];
        $pagesize = 8;
        $params = array('typeid'=>$typeid,'keyword'=>$keyword,'issystem'=>0,'isopen'=>1,'section'=>1);
        $result = Model_Jifen::search_result($params,$page,$pagesize);
        echo json_encode(array('status'=>true,'result'=>$result));
    }
    /*
    * 选择预订积分抵现策略
    */
    public function action_dialog_choose_jifentprice()
    {
        $jifenid = $_GET['jifenid'];
        $typeid = $_GET['typeid'];
        $selector = urldecode($_GET['selector']);
        $this->assign('jifenid',$jifenid);
        $this->assign('typeid',$typeid);
        $this->assign('selector',$selector);
        $this->display('stourtravel/jifen/dialog_choose_jifentprice');
    }
    /*
     * dialog_choose_bookjifen的数据源
     */
    public function action_ajax_choose_jifentprice()
    {
        $typeid = $_POST['typeid'];
        $keyword = $_POST['keyword'];
        $page = $_POST['page'];
        $pagesize = 8;
        $params = array('typeid'=>$typeid,'keyword'=>$keyword,'issystem'=>0,'isopen'=>1,'section'=>1);
        $result = Model_Jifen_Price::search_result($params,$page,$pagesize);
        echo json_encode(array('status'=>true,'result'=>$result));
    }


    /*
     * 获取用于积分策略的产品列表
     */
    public function action_ajax_get_products()
    {
        $typeid = $_POST['typeid'];
        $jifenid = $_POST['jifenid'];
        $keyword = $_POST['keyword'];
        $keyword = Common::getKeyword($keyword);
        $page = intval($_POST['page']);
        $page = $page<1?1:$page;
        $pagesize = 10;
        $offset = $pagesize*($page-1);

        $info = Model_Model::get_module_info($typeid);
        $table = 'sline_'.$info['maintable'];
        $w = " where id is not null ";
        if(!empty($jifenid))
        {
           $w.=" and jifenbook_id!={$jifenid}";
        }
        if(!empty($keyword))
        {
            $w.=" and title like '%{$keyword}%' or id='{$keyword}'";
        }
        if($info['maintable']=='model_archive')
        {
            $w.=" and typeid={$typeid} ";
        }
        $sql = "select id,webid,aid,title from {$table} {$w} order by modtime desc limit {$offset},{$pagesize}";
        $sql_num = "select count(*) as num from {$table} {$w}";
        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');

        $path = empty($info['correct'])?$info['pinyin']:$info['correct'];
        $infoModel = 'Model_' . $info['maintable'];
        foreach($list as &$v)
        {
            $v['series'] =St_Product::product_series($v['id'], $typeid);//编号
            if(!is_callable(array($infoModel, 'custom_info'))){
                $v['url'] = Common::getBaseUrl($v['webid']) . '/'.$path.'/show_' . $v['aid'] . '.html';
            }else{
                $customInfo=$infoModel::custom_info($v['id']);
                $v['url'] =$customInfo['url'];  //模型自定义url
            }
        }
        echo json_encode(array('list'=>$list,'pagesize'=>$pagesize,'page'=>$page,'total'=>$num));
    }
    /*
     * 获取用于积分抵现的产品列表
     */
    public function action_ajax_get_tprice_products()
    {
        $typeid = $_POST['typeid'];
        $jifenid = $_POST['jifenid'];
        $keyword = $_POST['keyword'];
        $keyword = Common::getKeyword($keyword);
        $page = intval($_POST['page']);
        $page = $page<1?1:$page;
        $pagesize = 10;
        $offset = $pagesize*($page-1);

        $info = Model_Model::get_module_info($typeid);
        $table = 'sline_'.$info['maintable'];
        $w = " where id is not null ";
        if(!empty($jifenid))
        {
            $w.=" and jifentprice_id!={$jifenid}";
        }
        if(!empty($keyword))
        {
            $w.=" and title like '%{$keyword}%' or id='{$keyword}'";
        }
        if($info['maintable']=='model_archive')
        {
            $w.=" and typeid={$typeid} ";
        }
        $sql = "select id,webid,aid,title from {$table} {$w} order by modtime desc limit {$offset},{$pagesize}";
        $sql_num = "select count(*) as num from {$table} {$w}";
        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
        $path = empty($info['correct'])?$info['pinyin']:$info['correct'];
        $infoModel = 'Model_' . $info['maintable'];
        foreach($list as &$v)
        {
            $v['series'] =St_Product::product_series($v['id'], $typeid);//编号
            if(!is_callable(array($infoModel, 'custom_info'))){
                $v['url'] = Common::getBaseUrl($v['webid']) . '/'.$path.'/show_' . $v['aid'] . '.html';
            }else{
                $customInfo=$infoModel::custom_info($v['id']);
                $v['url'] =$customInfo['url'];  //模型自定义url
            }
        }
        echo json_encode(array('list'=>$list,'pagesize'=>$pagesize,'page'=>$page,'total'=>$num));
    }
    /*
     * 获取已设置某积分策略的所有产品
     */
    public function action_ajax_get_jifen_products()
    {
        $typeid = $_POST['typeid'];
        $jifenid = $_POST['jifenid'];
        $keyword = $_POST['keyword'];
        $keyword = Common::getKeyword($keyword);
        $page = intval($_POST['page']);
        $page = $page<1?1:$page;
        $pagesize = 8;
        $offset = $pagesize*($page-1);
        $info = Model_Model::get_module_info($typeid);
        if(empty($info['maintable']) || empty($jifenid))
        {
            echo json_encode(array('list'=>null,'pagesize'=>0,'page'=>0,'total'=>0));
            return;
        }
        $table = 'sline_'.$info['maintable'];
        $w = " where jifenbook_id='{$jifenid}'";

        if(!empty($keyword))
        {
            $w.=" and title like '%{$keyword}%' or id='{$keyword}'";
        }

        $sql = "select id,webid,aid,title from {$table} {$w} order by modtime desc limit {$offset},{$pagesize}";
        $sql_num = "select count(*) as num from {$table} {$w}";
        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
        $path = empty($info['correct'])?$info['pinyin']:$info['correct'];
        $infoModel = 'Model_' . $info['maintable'];
        foreach($list as &$v)
        {
            $v['series'] =St_Product::product_series($v['id'], $typeid);//编号
            if(!is_callable(array($infoModel, 'custom_info'))){
                $v['url'] = Common::getBaseUrl($v['webid']) . '/'.$path.'/show_' . $v['aid'] . '.html';
            }else{
                $customInfo=$infoModel::custom_info($v['id']);
                $v['url'] =$customInfo['url'];  //模型自定义url
            }
            $v['typeid'] = $typeid;
        }
        echo json_encode(array('list'=>$list,'pagesize'=>$pagesize,'page'=>$page,'total'=>$num));
    }
    /*
     * 获取已设置某积分抵现的所有产品
     */
    public function action_ajax_get_jifentprice_products()
    {
        $typeid = $_POST['typeid'];
        $jifenid = $_POST['jifenid'];
        $keyword = $_POST['keyword'];
        $keyword = Common::getKeyword($keyword);
        $page = intval($_POST['page']);
        $page = $page<1?1:$page;
        $pagesize = 8;
        $offset = $pagesize*($page-1);
        $info = Model_Model::get_module_info($typeid);
        if(empty($info['maintable']) || empty($jifenid))
        {
            echo json_encode(array('list'=>null,'pagesize'=>0,'page'=>0,'total'=>0));
            return;
        }
        $table = 'sline_'.$info['maintable'];
        $w = " where jifentprice_id='{$jifenid}'";

        if(!empty($keyword))
        {
            $w.=" and title like '%{$keyword}%' or id='{$keyword}'";
        }

        $sql = "select id,webid,aid,title from {$table} {$w} order by modtime desc limit {$offset},{$pagesize}";
        $sql_num = "select count(*) as num from {$table} {$w}";
        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
        $path = empty($info['correct'])?$info['pinyin']:$info['correct'];
        $infoModel = 'Model_' . $info['maintable'];
        foreach($list as &$v)
        {
            $v['series'] =St_Product::product_series($v['id'], $typeid);//编号
            if(!is_callable(array($infoModel, 'custom_info'))){
                $v['url'] = Common::getBaseUrl($v['webid']) . '/'.$path.'/show_' . $v['aid'] . '.html';
            }else{
                $customInfo=$infoModel::custom_info($v['id']);
                $v['url'] =$customInfo['url'];  //模型自定义url
            }
            $v['typeid'] = $typeid;
        }
        echo json_encode(array('list'=>$list,'pagesize'=>$pagesize,'page'=>$page,'total'=>$num));
    }

    /*
     * 设置多个产品的积分策略ID
     */
    public function action_ajax_set_jifenids ()
    {
        $typeid = $_POST['typeid'];
        $productids = $_POST['productids'];
        $jifenid = $_POST['jifenid'];
        $productids_arr = explode(',',$productids);
        Model_Jifen::set_jifenbook_id($typeid,$jifenid,$productids_arr);
        echo json_encode(array('status'=>true));
    }
    /*
     * 设置多个产品的积分抵现策略ID
     */
    public function action_ajax_set_tprice()
    {
        $typeid = $_POST['typeid'];
        $productids = $_POST['productids'];
        $jifenid = $_POST['jifenid'];
        $productids_arr = explode(',',$productids);
        Model_Jifen_Price::set_tprice_id($typeid,$jifenid,$productids_arr);
        echo json_encode(array('status'=>true));
    }

    public function action_dialog_settypeids()
    {
       $id  = $_GET['id'];
       $products = $this->get_orderable_products();
       $info = DB::select()->from('jifen')->where('id','=',$id)->execute()->current();
       $typeids = explode(',',$info['typeid']);
       $this->assign('id',$id);
       $this->assign('typeids',$typeids);
       $this->assign('products',$products);
       $this->display('stourtravel/jifen/dialog_settypeids');
    }

    public function action_dialog_jifentprice_settypeids()
    {
        $id  = $_GET['id'];
        $products = $this->get_orderable_products();
        $info = DB::select()->from('jifen_price')->where('id','=',$id)->execute()->current();
        $typeids = explode(',',$info['typeid']);
        $this->assign('id',$id);
        $this->assign('typeids',$typeids);
        $this->assign('products',$products);
        $this->display('stourtravel/jifen/dialog_jifentprice_settypeids');
    }
    /*
     * 移除某个产品的积分策略
     */
    public function action_ajax_remove_jifen()
    {
        $typeid = $_POST['typeid'];
        $jifenid = $_POST['jifenid'];
        $productid = $_POST['productid'];
        Model_Jifen::clear_jifenbook($typeid,$jifenid,$productid);
        echo json_encode(array('status'=>true));
    }

    public function action_ajax_remove_tprice()
    {
        $typeid = $_POST['typeid'];
        $jifenid = $_POST['jifenid'];
        $productid = $_POST['productid'];
        Model_Jifen_Price::clear_jifentprice($typeid,$jifenid,$productid);
        echo json_encode(array('status'=>true));
    }
    /*
     * 获取可预订的产品模块列表
     */
    private function get_orderable_products()
    {
        $list = DB::select()->from('model')->execute()->as_array();
        foreach($list as $k=>&$v)
        {
             if(!Model_Model::is_orderable($v['id']) || !Model_Model::is_standard_product($v['id']) || $v['id']=='109')
             {
                 unset($list[$k]);
             }
            $nav_name = DB::select('shortname')->from('nav')->where('issystem','=',1)->and_where('typeid','=',$v['id'])->and_where('webid','=',0)->execute()->get('shortname');
            $v['modulename'] = !empty($nav_name)?$nav_name:$v['modulename'];
        }
        return $list;
    }
    /*
     * 获取可评论的产品模块列表
     */
    private function get_commentable_products()
    {
        $list = DB::select()->from('model')->execute()->as_array();
        foreach($list as $k=>&$v)
        {
            if(!Model_Model::is_commentable($v['id'])|| $v['id']=='109')
            {
                unset($list[$k]);
            }
            $nav_name = DB::select('shortname')->from('nav')->where('issystem','=',1)->and_where('typeid','=',$v['id'])->and_where('webid','=',0)->execute()->get('shortname');
            $v['modulename'] = !empty($nav_name)?$nav_name:$v['modulename'];
        }
        return $list;
    }
    /*
     * 获取可发布的产品模块列表
     */
    private function get_member_products()
    {
        $list = DB::select()->from('model')->execute()->as_array();
        foreach($list as $k=>&$v)
        {
            if(!Model_Model::is_member_product($v['id']))
            {
                unset($list[$k]);
            }
            $nav_name = DB::select('shortname')->from('nav')->where('issystem','=',1)->and_where('typeid','=',$v['id'])->and_where('webid','=',0)->execute()->get('shortname');
            $v['modulename'] = !empty($nav_name)?$nav_name:$v['modulename'];
        }
        return $list;
    }

    public function action_gene_data()
    {
        //预订积分策略
        $orderable_products = $this->get_orderable_products();
        foreach($orderable_products as $row) {
            $label = 'sys_book_' . $row['pinyin'];
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen')->where('label', '=', $label)->execute()->get('num');
            if ($num > 0)
                continue;
            $title = $row['modulename'] . '产品预订(全局)';
            $model = ORM::factory('jifen');
            $model->issystem = 1;
            $model->isopen = 0;
            $model->typeid = $row['id'];
            $model->label = $label;
            $model->title = $title;
            $model->rewardway = 0;
            $model->section = 1;
            $model->frequency_type = 0;
            $model->disable_fields='frequency_type';
            $model->save();
        }

        //评论积分策略
        $comment_products = $this->get_commentable_products();
        foreach($comment_products as $row) {
            $label = 'sys_comment_' . $row['pinyin'];
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen')->where('label', '=', $label)->execute()->get('num');
            if ($num > 0)
                continue;
            $title = $row['modulename'] . '产品评论(全局)';
            $model = ORM::factory('jifen');
            $model->issystem = 1;
            $model->isopen = 0;
            $model->typeid = $row['id'];
            $model->label = $label;
            $model->title = $title;
            $model->rewardway = 0;
            $model->section = 2;
            $model->frequency_type = 0;
            $model->disable_fields='rewardway,frequency_type';
            $model->save();
        }
        //发布产品
        $member_products = $this->get_member_products();
        foreach($member_products as $row) {
            $label = 'sys_write_' . $row['pinyin'];
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen')->where('label', '=', $label)->execute()->get('num');
            if ($num > 0)
                continue;
            $title = $row['modulename'] . '产品发布(全局)';
            $model = ORM::factory('jifen');
            $model->issystem = 1;
            $model->isopen = 0;
            $model->typeid = $row['id'];
            $model->label = $label;
            $model->title = $title;
            $model->rewardway = 0;
            $model->section = 3;
            $model->frequency_type = 0;
            $model->disable_fields='rewardway';
            $model->frequency_type_exclude='1,3';
            $model->save();
        }

        //其他策略
        $other_arr=array(
            array('title'=>'会员注册','label'=>'sys_member_register','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'frequency_type,rewardway'),
            array('title'=>'会员登录','label'=>'sys_member_login','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'rewardway'),
            array('title'=>'会员签到','label'=>'sys_member_sign','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'rewardway'),
            array('title'=>'会员上传头像','label'=>'sys_member_upload_head','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'rewardway'),
            array('title'=>'会员绑定手机','label'=>'sys_member_bind_phone','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'frequency_type,rewardway'),
            array('title'=>'会员绑定邮箱','label'=>'sys_member_bind_email','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'frequency_type,rewardway'),
            array('title'=>'会员绑定QQ','label'=>'sys_member_bind_qq','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'frequency_type,rewardway'),
            array('title'=>'会员绑定新浪微博','label'=>'sys_member_bind_sina_weibo','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'frequency_type,rewardway'),
            array('title'=>'会员绑定微信','label'=>'sys_member_bind_weixin','section'=>0,'isopen'=>0,'frequency_type'=>1,'disable_fields'=>'frequency_type,rewardway')
        );
        foreach($other_arr as $row)
        {
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen')->where('label', '=', $row['label'])->execute()->get('num');
            if ($num > 0)
                continue;

            $model = ORM::factory('jifen');
            $model->issystem = 1;
            $model->title = $row['title'];
            $model->label = $row['label'];
            $model->section = $row['section'];
            $model->isopen = $row['isopen'];
            $model->frequency_type=$row['frequency_type'];
            $model->disable_fields=$row['disable_fields'];
            $model->save();
        }

        //积分抵现
        foreach($orderable_products as $row) {
            $label = 'sys_tprice_' . $row['pinyin'];
            $num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen_price')->where('label', '=', $label)->execute()->get('num');
            if ($num > 0)
                continue;
            $title = $row['modulename'] . '产品预订(全局)';
            $model = ORM::factory('jifen_price');
            $model->issystem = 1;
            $model->isopen = 0;
            $model->typeid = $row['id'];
            $model->label = $label;
            $model->title = $title;
            $model->expiration_type = 0;
            $model->toplimit=0;
            $model->save();
        }

        exit('ok');

    }

    //获取已卸载的产品ID数组
    public function get_installed_typeids()
    {
        $ids_array=array();
        $models = DB::select()->from('model')->execute()->as_array();
        foreach($models as $model)
        {
           if($model['maintable']!='model_archive' && !St_Functions::is_system_app_install($model['id']) && $model['id']!=10)
           {
              continue;
           }
           $ids_array[] = $model['id'];
        }
        return $ids_array;

    }

    //获取应用产品的名称
    private function get_typeid_names($typeids,$seperator=',')
    {
        if(empty($typeids))
        {
            return '';
        }
        $models = DB::query(Database::SELECT,"select * from sline_model where id in ({$typeids})")->execute()->as_array();
        $name_arr = array();
        foreach($models as $model)
        {

            $nav_name = DB::select('shortname')->from('nav')->where('issystem','=',1)->and_where('typeid','=',$model['id'])->and_where('webid','=',0)->execute()->get('shortname');
            $nav_name = !empty($nav_name)?$nav_name:$model['modulename'];
            $name_arr[] = $nav_name;
        }
        return implode($seperator,$name_arr);

    }
}