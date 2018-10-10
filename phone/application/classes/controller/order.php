<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Line
 * 订单
 */
class Controller_Order extends Stourweb_Controller
{

    public function action_index()
    {
        $this->assign('title', '订单查询');
        $this->display('order/index');
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
            require_once TOOLS_COMMON . 'sms/smsservice.php';

            $code = rand(100000, 999999);

            $status = SMSService::send_msg($mobile, $GLOBALS['cfg_webname'], "尊敬的会员,您的订单查询验证码为：{$code}");
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

    /**
     * 查询列表
     */
    public function action_list()
    {
        $mobile = Common::session('order_query_token');
        if (empty($mobile))
        {
            $this->request->redirect('/order/index');
        }
        $this->assign('mobile', $mobile);
        $this->assign('title', '查询结果');
        $this->display('member/order_list');
    }

    public function action_ajax_login()
    {
        $data['status'] = 0;
        $code = Common::remove_xss($_POST['code']);
        $msgcode = Common::remove_xss($_POST['msg']);
        $moblie = Common::remove_xss($_POST['mobile']);

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
        $data['url'] = 'order/list';
        Common::session('order_query_token', $moblie);

        echo json_encode($data);
    }
}