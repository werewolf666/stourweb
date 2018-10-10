<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Order
 * 订单总控制器
 */
class Controller_Member_Order extends Stourweb_Controller
{


    private $mid = null;
    private $refer_url = null;

    public function before()
    {
        parent::before();
        $this->refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $GLOBALS['cfg_cmsurl'];
        $this->assign('backurl', $this->refer_url);
        /*$this->mid = Cookie::get('st_userid') ?  Cookie::get('st_userid') :  0;
        if(empty($this->mid))
        {
            $this->request->redirect('member/login');
        }
        $this->assign('mid',$this->mid);*/
        $user = Model_Member::check_login();
        if (!empty($user['mid']))
        {
            $this->mid = $user['mid'];
        }
        else
        {
            $this->request->redirect('member/login');
        }

        $this->assign('mid', $this->mid);
    }

    //全部订单
    public function action_all()
    {
        $typeId = 0;
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,

        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        $list = $out['list'];
        foreach($list as &$v)
        {
            $v['is_commentable'] = Model_Model::is_commentable($v['typeid']);
            $v['is_standard_product'] =  Model_Model::is_standard_product($v['typeid']);
            if($v['typeid']==107){
                $v['producturl'] = St_Functions::get_web_url($v['webid']) . "/integral/show_{$v['productautoid']}.html";
            }
        }

        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $list);
        $this->assign('ordertype', $orderType);
        $this->display('member/order/all');

    }

    //线路订单
    public function action_line()
    {
        $typeId = 1;
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,

        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('ordertype', $orderType);
        $this->display('member/order/line');

    }

    //酒店订单
    public function action_hotel()
    {
        $typeId = 2;
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,

        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('ordertype', $orderType);
        $this->display('member/order/hotel');
    }

    //租车订单
    public function action_car()
    {
        $typeId = 3;
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,

        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('ordertype', $orderType);
        $this->display('member/order/car');

    }

    //景点订单
    public function action_spot()
    {
        $typeId = 5;
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,

        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('ordertype', $orderType);
        $this->display('member/order/spot');

    }

    //签证订单
    public function action_visa()
    {
        $typeId = 8;
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,

        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('ordertype', $orderType);
        $this->display('member/order/visa');

    }

    //通用模块订单
    public function action_tongyong()
    {
        $typeId = intval(Arr::get($_GET, 'typeid'));
        $moduleName = ORM::factory('model', $typeId)->get('modulename');
        $modulePinyin = ORM::factory('model', $typeId)->get('pinyin');
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,
        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('ordertype', $orderType);
        $this->assign('modulename', $moduleName);
        $this->assign('modulepinyin', $modulePinyin);
        $this->assign('typeid', $typeId);
        $this->display('member/order/tongyong');

    }

    //团购订单
    public function action_tuan()
    {
        $typeId = 13;
        $pageSize = 10;
        $orderType = Common::remove_xss($this->request->param('ordertype'));
        $orderType = $orderType ? $orderType : 'all';
        $page = intval($this->request->param('p'));
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'ordertype' => $orderType,

        );

        $out = Model_Member_Order::order_list($typeId, $this->mid, $orderType, $page);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'route', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pageSize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('ordertype', $orderType);
        $this->display('member/order/tuan');

    }

    //订单详情
    public function action_view()
    {

        $orderSn = Arr::get($_GET, 'ordersn');
        $typeid = DB::select('typeid')->from('member_order')->where('ordersn','=',$orderSn)->and_where('memberid','=',$this->mid)->execute()->get('typeid');
        $model= ORM::factory('model',$typeid);
        if(!$model->loaded())
            exit('paramaters error');

        $moduleinfo = $model->as_array();
        $moduleinfo['path'] = empty($moduleinfo['correct'])?$moduleinfo['pinyin']:$moduleinfo['correct'];
        $this->assign('ordersn',$orderSn);
        $this->assign('moduleinfo',$moduleinfo);

        if(Model_Model::is_install_model($typeid))
        {

            $this->display('member/order/plugin_detail');
        }
        else
        {

            $info = Model_Member_Order::order_info($orderSn, $this->mid);


            if(St_Functions::is_normal_app_install('coupon'))
            {

                $info['iscoupon'] = Model_Coupon::order_view($orderSn);
             //   $info['payprice'] -= $info['iscoupon']['cmoney'];

            }

            if($info['receiver_address_id'])
            {
                $address_info = Model_Member_Address::get_address_info($info['receiver_address_id']);
                //$receiver_address = $address_info['province'].$address_info['city'].$address_info['address'].' &nbsp;&nbsp;('.$address_info['receiver'].') '.$address_info['phone'];
                $this->assign('address_info',$address_info);
            }

            $status=DB::select()->from('tongyong_order_status')->where('status','=',$info['status'])->and_where('is_show','=',1)->execute()->current();
            $info['statusname']=$status['status_name'];
            //当前版块是否是系统版块.
            $issystem =$moduleinfo['issystem'];
            $info = Pay_Online_Refund::get_refund_info($info);
            $this->assign('info', $info);
            $this->assign('issystem', $issystem);
            $this->display('member/order/view');
        }
    }

    //订单点评
    public function action_pinlun()
    {

        $orderSn = Common::remove_xss(Arr::get($_GET, 'ordersn'));
        $info = Model_Member_Order::order_info($orderSn);

        //如果评论了则跳转至上一页
        if ($info['ispinlun'] == 1)
        {
            $this->request->redirect($this->request->referrer());
        }
        $productinfo = Model_Member_Order::get_product_info($info['typeid'], $info['productautoid']);
        $info['product'] = $productinfo;
        $code = md5(time());
        Common::session('pl_crsfcode', $code);
        $this->assign('info', $info);
        $this->assign('frmcode', $code);
        $this->display('member/order/pinlun');
    }

    //发布订单评论
    public function action_ajax_save_pinlun()
    {
        $orderid = Common::remove_xss(Arr::get($_POST, 'orderid'));
        $frmcode = Common::remove_xss(Arr::get($_POST, 'frmcode'));
        $content = Common::remove_xss(Arr::get($_POST, 'plcontent'));
        $level = Common::remove_xss(Arr::get($_POST, 'level')); //评分
        $piclist = Common::remove_xss(Arr::get($_POST,'piclist'));
        if (empty($orderid))
        {
            echo json_encode(array('status' => 0, 'msg' => '错误请求'));
            exit;
        }
        $orderInfo = ORM::factory('member_order', $orderid)->as_array();


        //安全校验码验证
        $orgCode = Common::session('pl_crsfcode');
        if ($orgCode != $frmcode)
        {
            echo json_encode(array('status' => 0, 'msg' => '校验码错误'));
            exit;
        }
        $arr = array();
        $arr['memberid'] = $this->mid;
        $arr['content'] = $content;
        $arr['orderid'] = $orderid;

        $arr['articleid'] = $orderInfo['productautoid'];
        $arr['level'] = $level;
        $arr['typeid'] = $orderInfo['typeid'];
        $arr['addtime'] = time();
        $arr['piclist']=$piclist;
        $model = ORM::factory('comment');
        foreach ($arr as $key => $value)
        {
            $model->$key = $value;
        }
        $model->save();
        if ($model->saved())
        {

            $order_model = ORM::factory('member_order', $orderid);
            $order_model->ispinlun = 1;
            $order_model->save();


            //点评积分
           /* $jifencomment = $orderInfo['jifencomment'];

            if (!empty($jifencomment))
            {

                $sql = "UPDATE `sline_member` SET jifen=jifen+{$jifencomment} WHERE mid='" . $this->mid . "'";
                $flag = DB::query(Database::UPDATE, $sql)->execute();
                if($flag)
                {
                    Product::add_jifen_log($this->mid,"评论赠送积分{$jifencomment}",$jifencomment,2);
                }
            }*/
            $status = 1;
            echo json_encode(array('status' => $status));
        }
        else
        {
            echo json_encode(array('status' => 0, 'msg' => __("save_failure")));
        }
        exit();

    }

    //取消订单
    public function action_ajax_order_cancel()
    {
        $flag = 0;
        $orderId = Common::remove_xss(Arr::get($_GET, 'orderid'));
        $m = ORM::factory('member_order')->where("memberid={$this->mid} and id={$orderId} and status < 2")->find();
        if ($m->loaded())
        {
            $orgstatus = $m->status;
            $m->status = 3;//取消订单
            $m->where("memberid={$this->mid}");
            $m->update();
            if ($m->saved())
            {
                $model_info = Model_Model::get_module_info($m->typeid);
                $maintable = $model_info['maintable'];
                $pieces = explode('_',$maintable);
                foreach($pieces as $key=>$piece)
                {
                    $pieces[$key] = ucfirst($piece);
                }
                $class_name = 'Model_'.implode('_',$pieces);

                Model_Member_Order::back_order_status_changed($orgstatus,$m->as_array(),$class_name);
                $flag = 1;
            }
        }
        echo json_encode(array('status' => $flag));
    }

    //订单列表
    public function action_plugin_list()
    {
        $typeid = $_GET['typeid'];
        $ordertype = $_GET['ordertype'];
        $ordertype = St_Validate::is_letter($ordertype)?$ordertype:'';

        $model= ORM::factory('model',$typeid);
        if(!$model->loaded())
            exit('paramaters error');
        $info = $model->as_array();
        $info['path'] = empty($info['correct'])?$info['pinyin']:$info['correct'];
        $url = "/{$info['path']}/member/orderlist";
        $url.= !empty($ordertype)? '?ordertype='.$ordertype:'';

        $info['url'] = $url;
        $this->assign('info',$info);
        $this->display('member/order/plugin_list');
    }
    //订单详情
    public function action_plugin_detail()
    {
        $ordersn = $_GET['ordersn'];
        $typeid = DB::select('typeid')->from('member_order')->where('ordersn','=',$ordersn)->execute()->get('typeid');
            $model= ORM::factory('model',$typeid);
        if(!$model->loaded())
            exit('paramaters error');
        $info = $model->as_array();
        $info['path'] = empty($info['correct'])?$info['pinyin']:$info['correct'];
        $this->assign('ordersn',$ordersn);
        $this->assign('info',$info);
        $this->display('member/order/plugin_detail');
    }


    /**
     * @function  退款页面
     */
    public function action_order_refund()
    {
        $ordersn = Common::remove_xss($_GET['ordersn']);
        if(!$ordersn)
        {
            exit();
        }
        $info = Model_Member_Order::order_info($ordersn,$this->_mid);

        $online_transaction_no = json_decode($info['online_transaction_no'],true);
        if(!empty($online_transaction_no))
        {
            $info['refund_auto'] = 1 ;
        }
        if(!$ordersn)
        {
            exit();
        }
        $this->assign('info',$info);
        $this->display('member/order/refund');
    }

    /**
     * @function 退款
     */
    public function action_ajax_order_refund()
    {

        $ordersn = Common::remove_xss($_POST['ordersn']);
        $m = ORM::factory('member_order')->where("memberid={$this->mid} and ordersn='$ordersn' and status < 2")->find();

        $model_info = Model_Model::get_module_info($m->typeid);
        $maintable = $model_info['maintable'];
        $pieces = explode('_',$maintable);
        foreach($pieces as $key=>$piece)
        {
            $pieces[$key] = ucfirst($piece);
        }
        $class_name = 'Model_'.implode('_',$pieces);
        $result = Pay_Online_Refund::apply_order_refund($_POST,$this->mid,$class_name);
        echo json_encode($result);
    }


    //退款撤回
    public function action_ajax_order_refund_back()
    {
        $ordersn = Common::remove_xss($_POST['ordersn']);
        $m = ORM::factory('member_order')->where("memberid={$this->mid} and ordersn='$ordersn' and status < 2")->find();

        $model_info = Model_Model::get_module_info($m->typeid);
        $maintable = $model_info['maintable'];
        $pieces = explode('_',$maintable);
        foreach($pieces as $key=>$piece)
        {
            $pieces[$key] = ucfirst($piece);
        }
        $class_name = 'Model_'.implode('_',$pieces);
        $ordersn = Common::remove_xss(Arr::get($_POST, 'ordersn'));
        $result = Pay_Online_Refund::order_refund_back($ordersn,$this->mid,$class_name);
        echo json_encode($result);


    }
}
