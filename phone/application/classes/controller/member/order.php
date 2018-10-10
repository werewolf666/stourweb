<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Order
 * 订单管理
 */
class Controller_Member_Order extends Stourweb_Controller
{
    /**
     * 订单管理前置操作
     */
    public function before()
    {

        parent::before();
        $this->member = Common::session('member');
        $order_query_token = Common::session('order_query_token');
        Common::session('order_query_token','');//重置查询
        if (empty($this->member) && empty($order_query_token))
        {
            Common::message(array('message' => __('unlogin'), 'jumpUrl' => $this->cmsurl . 'member/login'));
        }

    }

    /*
     * 订单查询页面
     * */
    public function action_query()
    {
        $this->assign('title', '订单查询');
        $this->display('member/order/query');
    }

    /**
     * 查询订单
     */
    public function action_ajax_query()
    {
        $data['status'] = 0;
        $code = Common::remove_xss($_POST['code']);
        $msgcode = Common::remove_xss($_POST['msg']);
        $mobile = Common::remove_xss($_POST['mobile']);


        if (!Captcha::valid($code))
        {
            $data['msg'] = '验证码错误';
            echo json_encode($data);
            return;
        }

        if (Common::session('msg_code') != $msgcode)
        {
            $data['msg'] = '短信动态码错误';
            echo json_encode($data);
            return;
        }

        Common::session('msg_code', null);
        Common::session('captcha_response', null);
        $data['status'] = 1;
        $data['url'] = 'member/order/query_result';
        Common::session('order_query_mobile', $mobile);
        echo json_encode($data);
    }


    /**
     * 查询结果页面
     */
    public function action_query_result()
    {

        $mobile = Common::session('order_query_mobile');
        $this->assign('mobile', $mobile);
        $this->display('member/order/query_result');
    }

    /**
     * 订单列表
     */
    public function action_list()
    {

        $type = intval(Arr::get($_GET,'type'));
        $row = $this->get_list();
        $this->assign('title', '订单中心');
        $this->assign('data', $row);
        $this->assign('type',$type);
        $this->display('member/order/list');

    }

    /**
     * 获取订单列表
     * @return mixed
     */
    private function get_list()
    {

        $row = Model_Member_Order::order_list_mobile($this->member['mid']);
        $row = self::get_data_initialization($row);
        return $row;
    }

    /**
     * 订单列表 查看更多
     */
    public function action_ajax_order_more()
    {
        $page =intval($_GET['page']);
        $page = $page < 1 ? 1 : $page;
        $param['type'] = Common::remove_xss(intval($_GET['type']));
        if (isset($_GET['mobile']))
        {
            $param['isquery'] = Common::remove_xss($_GET['mobile']);
        }
        $row = Model_Member_Order::order_list_mobile($this->member['mid'], $page, $param);
        $row = self::get_data_initialization($row);
        echo(Product::list_search_format($row, $page));
    }

    /**
     * 订单列表数据处理
     * @param $data
     * @return mixed
     */
    private function get_data_initialization($data)
    {
        foreach ($data as &$v)
        {
            $info = Model_Member_Order::info($v['ordersn']);
            //订单详情
            $v['url'] = Common::get_web_url($v['webid']) . "/member/order/show?id={$v['id']}";
            //支付url
            $v['payurl'] = Common::get_main_host() . "/payment/?ordersn={$v['ordersn']}";
            //评论url
            $v['commenturl'] = Common::get_web_url($v['webid']) . "/member/comment/index?id={$v['id']}";
            //取消订单
            $v['cancel_order'] = false;
            if ($v['status'] < 2)
            {
                $v['cancel_order'] = true;
            }
            //产品缩略图
            $v['litpic'] = Common::img($v['litpic'], 258, 175);
            //下单时间
            $v['addtime'] = date('Y-m-d H:i', $v['addtime']);

            $v['is_commentable'] = Model_Model::is_commentable($v['typeid']);
            $v['is_standard_product'] =  Model_Model::is_standard_product($v['typeid']);
            $v['statusname'] = Model_Member_Order::get_status_name($v['status']);
            //分割订单产品名称
            $tempArr = array_filter(preg_split('`[\(\)]`', $v['productname']));
            $v['subname'] = count($tempArr) > 1 ? $tempArr[count($tempArr) - 1] : '';
            $v['productname'] = str_replace("({$v['subname']})", '', $v['productname']);
            //全额支付
            $v['price'] = $info['total'];//$v['price'] * $v['dingnum'] + $v['childprice'] * $v['childnum'] + $v['oldprice'] * $v['oldnum'];

//            if(St_Functions::is_normal_app_install('coupon'))
//            {
//
//                $couponinfo = Model_Coupon::order_view($v['ordersn']);
//                if($couponinfo['cmoney'])
//                {
//                    $v['price'] -= $couponinfo['cmoney'];
//                }
//            }

            //支付方式
            switch ($v['paytype'])
            {
                case '1':
                    $v['paytype'] = '全款支付';
                    break;
                case '2':
                    $v['paytype'] = '定金支付';
                    $v['price'] = ($v['dingnum'] + $v['childnum'] + $v['oldnum']) * $v['dingjin'];
                    break;
                default:
                    $v['paytype'] = '线下支付';
                    break;
            }
            //1元积分兑换
            $v['exchange'] = $GLOBALS['cfg_exchange_jifen'];
        }
        return $data;
    }

    /**
     * 取消订单
     */
    public function action_ajax_cancel(){
        $flag = false;
        $orderId = Common::remove_xss(Arr::get($_POST, 'id'));
        $m = ORM::factory('member_order')->where("memberid={$this->member['mid']} and id={$orderId} and status < 2")->find();
        if ($m->loaded())
        {
            $orgstatus = $m->status;
            $m->status = 3;//取消订单
            $m->where("memberid={$this->member['mid']}");
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



                Model_Member_Order::back_order_status_changed($orgstatus, $m->as_array(), $class_name);
                $flag = true;
            }
        }
        echo json_encode(array('status' => $flag,'data'=>array('id'=>$orderId)));
    }
    /**
     * 订单详情
     */
    public function action_show()
    {

        $id = Common::remove_xss($_GET['id']);
        $row = Model_Member_Order::get_order_detail($id,$this->member['mid']);

        //邮轮订单,单独处理
        if($row['typeid']==104)
        {
           // $this->request->redirect('ship/order_show/?id='.$id,'301');
        }
		if($row['typeid']==107)
        {
            $this->request->redirect('integral/order_show/?id='.$id,'301');
        }
        if(empty($row)){
            $this->request->redirect('pub/404');
        }

        $model_info=Model_Model::get_module_info($row['typeid']);
        $model_path = empty($model_info['correct'])?$model_info['pinyin']:$model_info['correct'];
        $pinyin = $model_info['pinyin']=='ship_line'?'ship':$model_info['pinyin'];
        $mobile_order_member_class='Controller_Mobile_'.ucfirst($pinyin).'_Member';
        if(class_exists($mobile_order_member_class))
        {
            $this->request->redirect($model_path.'/member/orderview?ordersn='.$row['ordersn']);
            return;
        }



        $row = Model_Member_Order::order_info($row['ordersn'],$this->member['mid']);
        //分割订单产品名称
        $tempArr = array_filter(preg_split('`[\(\)]`', $row['productname']));
        $row['subname'] = count($tempArr) > 1 ? $tempArr[count($tempArr) - 1] : '';
        $row['productname'] = str_replace("({$row['subname']})", '', $row['productname']);
        $row['url'] = $this->ger_product_url($row['typeid'],$row['productautoid']);

        $num = array();
        if ($row['dingnum'] > 0)
        {
            array_push($num, "成人{$row['dingnum']}个");
        }
        if ($row['childnum'] > 0)
        {
            array_push($num, "小孩{$row['childnum']}个");
        }
        if ($row['oldnum'] > 0)
        {
            array_push($num, "老人{$row['oldnum']}个");
        }
        $row['num'] = implode('，', $num);
        //评论
        $comment = Model_Comment::get_comment($row['id']);
        if (!empty($comment))
        {
            $comment['score'] = ($comment['level'] * 20) . '%';
        }

        $row['payurl'] = Common::get_main_host() . "/payment/?ordersn={$row['ordersn']}";

			//封面图
			$model = ORM::factory('model',$row['typeid']);
			$table = $model->maintable;
			 if($table)
            {
                $info = ORM::factory($table,$row['productautoid'])->as_array();
                $out = $info;
				$row['litpic'] = Common::img($out['litpic']);
            }
        $status=DB::select()->from('tongyong_order_status')->where('status','=',$info['status'])->and_where('is_show','=',1)->execute()->current();
        $info['statusname']=$status['status_name'];

        if($row['receiver_address_id'])
        {
            $receiver_address = DB::select()->from('member_address')->where('id','=',$row['receiver_address_id'])->execute()->current();
            $this->assign('receiver_address', $receiver_address);
        }
        $row = Pay_Online_Refund::get_refund_info($row);
        $this->assign('info', $row);
        $this->assign('comment', $comment);
        $this->assign('member', $this->member);
        $this->display('member/order_show');


    }


    /**
     * 发送短信S
     */
    public function action_ajax_send_message()
    {
        $data['status'] = false;
        $mobile = Common::remove_xss($_POST['mobile']);
        if (!preg_match('~[0-9]{11}~', $mobile))
        {
            $data['msg'] = '手机号不正确';
            exit(json_encode($data));
        }
        if (!Captcha::valid(Common::remove_xss($_POST['code'])))
        {
            $data['msg'] = '验证码不正确';
            exit(json_encode($data));
        }
        $member=DB::select()->from('member')->where("mobile={$mobile}")->execute()->as_array();
        $memberThird=DB::select()->from('member_order')->where("linktel={$mobile}")->execute()->as_array();
        //查询联系方式存在
        if (!empty($member)|| !empty($memberThird))
        {

            $code = rand(100000, 999999);
            $status = St_SMSService::send_msg($mobile, $GLOBALS['cfg_webname'], "尊敬的会员,您的订单查询验证码为：{$code}");
            $status = json_decode($status);
            if ($status->Success)
            {
                Common::session('msg_code', $code);
            }
            else
            {
                $data['msg'] = $status->Message;
            }
            $data['status'] = $status->Success;
        }
        else
        {
            $data['msg'] = '手机号不存在';
        }
        echo json_encode($data);
    }

    private function ger_product_url($typeid,$id)
    {
        //定义有手机端的typeid数组
        $mobile_ids = array(1, 2, 3, 5, 8, 11, 13, 105);
        $model = ORM::factory('model', $typeid);
        $table = $model->maintable;
        $pinyin = $model->pinyin;

        if ($table != 'model_archive' && !in_array($typeid, $mobile_ids)) {
            return '';
        }
        if (!class_exists('Model_' . $table)) {
            return '';
        }
        $info = ORM::factory($table, $id)->as_array();
        $py = empty($model->correct) ? $pinyin : $model->correct;
        $url = St_Functions::get_web_url($info['webid']) . "/{$py}/show_{$info['aid']}.html";
        return $url;
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

        $info = Model_Member_Order::order_info($ordersn,$this->member['mid']);
        if($info['status']!=2)
        {
            exit();
        }
        $online_transaction_no = json_decode($info['online_transaction_no'],true);
        if(!empty($online_transaction_no))
        {
            $info['refund_auto'] = 1 ;
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
        $m = ORM::factory('member_order')->where("memberid={$this->member['mid']} and ordersn='$ordersn' and status < 2")->find();

        $model_info = Model_Model::get_module_info($m->typeid);
        $maintable = $model_info['maintable'];
        $pieces = explode('_',$maintable);
        foreach($pieces as $key=>$piece)
        {
            $pieces[$key] = ucfirst($piece);
        }
        $class_name = 'Model_'.implode('_',$pieces);
        $result = Pay_Online_Refund::apply_order_refund($_POST,$this->member['mid'],$class_name);
        echo json_encode($result);
    }


    //退款撤回
    public function action_ajax_order_refund_back()
    {
        $ordersn = Common::remove_xss($_POST['ordersn']);
        $m = ORM::factory('member_order')->where("memberid={$this->member['mid']} and ordersn='$ordersn' and status < 2")->find();

        $model_info = Model_Model::get_module_info($m->typeid);
        $maintable = $model_info['maintable'];
        $pieces = explode('_',$maintable);
        foreach($pieces as $key=>$piece)
        {
            $pieces[$key] = ucfirst($piece);
        }
        $class_name = 'Model_'.implode('_',$pieces);
        $ordersn = Common::remove_xss(Arr::get($_POST, 'ordersn'));
        $result = Pay_Online_Refund::order_refund_back($ordersn,$this->member['mid'],$class_name);
        echo json_encode($result);


    }
}