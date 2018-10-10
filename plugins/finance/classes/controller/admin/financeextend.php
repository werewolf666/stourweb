<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Financeextend extends Stourweb_Controller
{
    public function before()
    {
        parent::before();
        //右侧导航
        $this->assign('parentkey', $this->params['parentkey']);
    }

    //财务总览
    public function action_overview()
    {
        $list = Model_Member_Order_Extend::get_overview_list();
        $withdraw = Model_Member_Order_Extend::get_withdrawed_amount(1); //已提现金额
        $withdrawing = Model_Member_Order_Extend::get_withdrawed_amount(0);//提现中金额

        $this->assign('withdraw', $withdraw);
        $this->assign('withdrawing', $withdrawing);
        $this->assign('list', $list['list']);
        $this->display('admin/finance/overview');
    }

    // 财务总览统计数据汇总，异步加载防止订单过多造成超时
    public function action_ajax_overview_summary()
    {
        $pagesize = 500;
        $pageno   = intval(Arr::get($_POST, 'pageno'));
        $rlt = Model_Member_Order_Extend::get_overview_info($pagesize, $pageno);
        echo json_encode($rlt);
        exit;
    }

    //订单统计
    public function action_ordercount()
    {
        $unorder_typeids=array(4,6,7,10,11,12,14,101);
        $modules = DB::select('id','modulename')->from('model')->where('id','not in',$unorder_typeids)->execute()
            ->as_array();

        $this->assign('modules',$modules);
        $this->assign('count_fields',Model_Member_Order_Extend::$count_fields);
        $this->display('admin/finance/ordercount');
    }

    //获取订单列表
    public function action_ajax_ordercount_list()
    {
        $category = Common::remove_xss(Arr::get($_GET, 'category'));
        $typeid = Common::remove_xss(Arr::get($_GET, 'typeid'));
        $id = Common::remove_xss(Arr::get($_GET, 'id'));

        $starttime = Common::remove_xss(Arr::get($_GET, 'starttime'));
        $starttime = !empty($starttime) ? strtotime($starttime) : null;
        $endtime = Common::remove_xss(Arr::get($_GET, 'endtime'));
        $endtime = !empty($endtime) ? (strtotime($endtime) + 24 * 60 * 60) : null;

        $settle_status = Common::remove_xss(Arr::get($_GET, 'settle_status'));
        $order_status = Common::remove_xss(Arr::get($_GET, 'order_status'));
        $pageno = Common::remove_xss(Arr::get($_GET, 'pageno'));
        $pagesize = Common::remove_xss(Arr::get($_GET, 'pagesize'));

        $info = Model_Member_Order_Extend::get_order_list($category, $id, $typeid, $starttime, $endtime, $settle_status, $order_status, $pagesize, $pageno);

        //汇总信息
        $countinfo=array(
            'total'=>0,
            'totalprice'=>0,
            'payprice'=>0,
            'jifentprice'=>0,
            'basicprice'=>0,
            'commission'=>0,
            'settle_amount'=>0
        );

        $countinfo['total'] = $info['total'];

        foreach($info['list'] as $o)
        {
            $countinfo['totalprice'] += $o['totalprice'];
            $countinfo['payprice'] += $o['payprice'];
            $countinfo['jifentprice'] += $o['jifentprice'];
            $countinfo['basicprice'] += $o['product_basicprice'];
            $countinfo['commission'] += $o['commission'];
            $countinfo['settle_amount'] += $o['settle_amount'];
        }
        //end 汇总信息

        $info['countinfo'] = $countinfo;
        echo json_encode($info);
    }


    //获取产品,供应商,分销商列表数据
    public function action_ajax_ordercount_query_list()
    {
        //1:产品列表;2:供应商;3:分销商
        $category = Arr::get($_GET, 'category');
        $typeid = Arr::get($_GET, 'typeid');
        $pagesize = 10;
        $pageno = Arr::get($_GET, 'pageno');
        $keyword = Arr::get($_GET,'keyword');

        $data = Model_Member_Order_Extend::get_query_list($category, $typeid, $keyword, $pagesize, $pageno);
        echo json_encode($data);
    }

    //订单结算导出报表
    public function action_ordercount_export_excel()
    {
        $fields = Arr::get($_GET,'fields');
        $fields = explode(',', $fields);
        $type = Arr::get($_GET,'type');
        $typeid = Arr::get($_GET,'typeid');
        $id = Arr::get($_GET,'id');
        $starttime = Arr::get($_GET,'starttime');
        $starttime = $starttime ? strtotime($starttime) : '';
        $endtime = Arr::get($_GET,'endtime');
        $endtime = $endtime ? strtotime($endtime) : '';
        $info = Model_Member_Order_Extend::get_order_list($type, $id, $typeid,$starttime,$endtime);
        Model_Member_Order_Extend::export_excel_order_count($info['list'], $fields);
    }

    //交易记录
    public function action_orderrecord()
    {
        $action = $this->params['action'];
        if($action=='read')
        {
            $keyword = Arr::get($_GET,'keyword');
            $deal_type = Arr::get($_GET,'trade_type');
            $deal_status = Arr::get($_GET,'trade_status');
            $pageno = Arr::get($_GET, 'page');
            $pagesize = Arr::get($_GET,'limit');
            $info = Model_Member_Order_Extend::get_overview_list(null, $keyword, $deal_type, $pagesize , $pageno, $deal_status);

            foreach($info['list'] as &$l)
            {
                $l['addtime']=date("Y-m-d H:i:s",$l['addtime']);
            }

            echo json_encode($info);


            exit;
        }
        $this->display('admin/finance/orderrecord');
    }

    //交易记录导出Excel
    public function action_orderrecord_export_excel()
    {
        $fields = array();
        $trade_type = Arr::get($_GET,'trade_type');
        $searchkey = Arr::get($_GET,'searchkey');

        $info = Model_Member_Order_Extend::get_overview_list(null,$searchkey,$trade_type,999999);

        Model_Member_Order_Extend::export_excel_order_record($info['list'], $fields);
    }

    //提现审核
    public function action_withdraw()
    {
        $this->request->redirect('finance/index/parentkey/finance');
    }

    //返佣栏目设置
    public function action_config_commission_type()
    {
        $productlist = Model_Member_Order_Extend::get_commission_product();
        $list=ORM::factory('supplier_commission_config')->get_all();
        $info=array();
        foreach($list as $v)
        {
            $info[$v['varname']]=$v['value'];
        }
        $this->assign('info',$info);
        $this->assign('productlist',$productlist);
        $this->display('admin/finance/commission_type');
    }

    //返佣产品类型分佣设置
    public function action_ajax_config_commission_type_save()
    {
        $productlist = Model_Member_Order_Extend::get_commission_product();
        $config = array('type', 'ratio', 'cash');
        $varname_arr    = array();
        foreach($productlist as $p)
        {
            foreach($config as $c)
            {
                $varname_arr[]  = "cfg_commission_{$c}_".$p['id'];
            }
            if($p['id'] == 1)
            {
                $varname_arr[] = 'cfg_commission_cash_1_old';
                $varname_arr[] = 'cfg_commission_cash_1_child';
            }
        }

        // 现金返佣时,佣金计算规则
        $varname_arr[] = 'cfg_commission_cash_calc_type';
        foreach($varname_arr as $varname)
        {
            $value = Arr::get($_POST, $varname);
            $value = intval($value);
            Model_Supplier_Commission_Config::save_normal_config($varname, $value);
        }
        echo json_encode(array('status' => true));
    }

    //返佣具体产品设置
    public function action_config_commission_product()
    {
        $action = $this->params['action'];
        $typeid = $this->params['typeid'];
        $menuid = $this->params['menuid'];
        if(empty($action))
        {
            $productlist = Model_Member_Order_Extend::get_commission_product();
            $this->assign('typeid',$typeid);
            $this->assign('products',$productlist);
            $this->assign('menuid',$menuid);
            if($typeid == 1)
            {
                $this->display('admin/finance/commission_product_line');
            }
            else
            {
                $this->display('admin/finance/commission_product');
            }
        }
        elseif($action == 'read')
        {

            $start = Arr::get($_GET, 'start');
            $limit = Arr::get($_GET, 'limit');
            $keyword = Arr::get($_GET, 'keyword');
            $sort = json_decode(Arr::get($_GET, 'sort'), true);
            $order = 'order by a.modtime desc,a.addtime desc';

            $keyword = Common::getKeyword($keyword);
            $table= $this->_get_table($typeid);
            $ftable='sline_'.$table;

            $fields='a.title,a.webid,a.id,a.aid,a.modtime,b.commission_type,b.commission_ratio,b.commission_cash,b.commission_cash_child,b.commission_cash_old';
            if(empty($table))
                return;
            if ($sort[0]['property'])
            {
                $property=$sort[0]['property'];
                $ratioFields=array('commission_type','commission_ratio','commission_cash');
                if(in_array($property,$ratioFields))
                    $orderStr = "b.{$property} {$sort[0]['direction']}";
                if($property=='modtime')
                    $orderStr = "a.modtime {$sort[0]['direction']}";
                $order=" order by {$orderStr},a.modtime desc,a.addtime desc";

            }
            $w = "where a.id is not null and c.suppliertype=1 "; //第三方供应商
            $w.= $table=='model_archive'?" and a.typeid={$typeid} ":"";
            $w .= !empty($keyword)? " and (a.id='{$keyword}' or a.title like '%{$keyword}%')":'';


            $sql = "select {$fields} from {$ftable} a left join sline_supplier_commission_product b on a.id=b.productid and b.typeid={$typeid} left join  sline_supplier c on a.supplierlist=c.id $w $order limit  $start,$limit";

            //echo $sql;exit;

            $totalcount_arr = DB::query(Database::SELECT, "select count(*) as num from {$ftable} a left join  sline_supplier c on a.supplierlist=c.id $w")->execute()->as_array();
            $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
            foreach($list as $k=>&$v)
            {
                $v['url']=$this->_get_url($typeid,$v['webid'],$v['aid'],$v['id']);
                $v['modtime'] = empty($v['modtime'])?'':date('Y-m-d',$v['modtime']);
            }
            $result['total'] = $totalcount_arr[0]['num'];
            $result['lists'] = $list;
            $result['success'] = true;
            echo json_encode($result);
        }
        elseif($action == 'update')
        {
            $id = $_POST['id'];
            $field = $_POST['field'];
            $val = $_POST['val'];
            $typeid = $_POST['typeid'];
            if(empty($id) || empty($typeid))
            {
                echo 'no';
                return;
            }
            $curtime=time();
            $ratioModel=ORM::factory('supplier_commission_product')->where('typeid','=',$typeid)->and_where('productid','=',$id)->find();
            $ratioModel->typeid=$typeid;
            $ratioModel->productid=$id;
            $ratioModel->$field=abs($val);
            $ratioModel->modtime=$curtime;
            $ratioModel->save();
            echo 'ok';
        }
        elseif($action == 'updatesome')
        {
            $id = $_POST['id'];
            $typeid = $_POST['typeid'];
            $commission_type = $_POST['commission_type'];
            $commission_ratio = $_POST['commission_ratio'];
            $commission_cash = $_POST['commission_cash'];
            $commission_cash_child = $_POST['commission_cash_child'];
            $commission_cash_old = $_POST['commission_cash_old'];


            if(empty($id) || empty($typeid))
            {
                echo 'no';
                return;
            }
            $curtime=time();
            $ratioModel=ORM::factory('supplier_commission_product')->where('typeid','=',$typeid)->and_where('productid','=',$id)->find();
            $ratioModel->typeid=$typeid;
            $ratioModel->productid=$id;
            $ratioModel->commission_type=abs($commission_type);
            $ratioModel->commission_ratio=abs($commission_ratio);
            $ratioModel->commission_cash=abs($commission_cash);
            $ratioModel->commission_cash_child=abs($commission_cash_child);
            $ratioModel->commission_cash_old=abs($commission_cash_old);
            $ratioModel->modtime=$curtime;
            $ratioModel->save();
            echo 'ok';
        }


    }

    //获取表名
    private function _get_table($typeid)
    {
        $model=ORM::factory('model',$typeid);
        if(!$model->loaded())
            return false;
        return $model->maintable;
    }
    //获取产品URL
    private function _get_url($typeid,$webid,$aid,$id)
    {
        $model = ORM::factory('model', $typeid);
        if (!$model->loaded())
        {
            return false;
        }
        $webid = empty($webid) ? 0 : $webid;
        $realid = $typeid == 101 ? $id : $aid;
        $url_folder = empty($model->correct) ? $model->pinyin : $model->correct;
        $url = Common::getBaseUrl($webid) . '/' . $url_folder . '/show_' . $realid . '.html';
        return $url;
    }

}