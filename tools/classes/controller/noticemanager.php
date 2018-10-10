<?php defined('SYSPATH') or die('No direct script access.');
require_once TOOLS_COMMON . 'sms/noticecommon.php';

class Controller_NoticeManager extends Stourweb_Controller
{
    /*
     * 通知系统基础类
     * */
    public function before()
    {
        parent::before();
    }

    protected function save_msg_config($msgtable)
    {
        foreach ($_POST as $config_key => $config_value)
        {
            $config_key_arr = explode("_", $config_key);
            $isopen_config_item = ($config_key_arr[count($config_key_arr) - 1] == "isopen");
            if ($isopen_config_item)
                unset($config_key_arr[count($config_key_arr) - 1]);

            $currmsgtype = implode("_", $config_key_arr);

            $model = ORM::factory($msgtable)->where('msgtype', '=', $currmsgtype)->find();
            $model->msgtype = $currmsgtype;
            if ($isopen_config_item)
                $model->isopen = $config_value;
            else
                $model->msg = $config_value;
            if (!$model->save())
                return false;
        }

        return true;
    }

    protected function get_msg_config($msgtable, array $msgtypelist)
    {
        $msg_arr = ORM::factory($msgtable)->get_all();

        $msg_config_data = array();
        foreach ($msgtypelist as $msgtype)
        {
            $msg_config_data[$msgtype] = array('templet' => '', 'is_open' => 0);
            foreach ($msg_arr as $msg_row)
            {
                if ($msg_row['msgtype'] == $msgtype)
                {
                    $msg_config_data[$msgtype] = array('templet' => $msg_row['msg'], 'is_open' => $msg_row['isopen']);
                    break;
                }
            }
        }

        return $msg_config_data;
    }

    protected function get_order_msgtaglist($model_info, $product_id = "")
    {
        $result = NoticeCommon::create_product_order_msgtag_summary(NoticeCommon::PRODUCT_ORDER_UNPROCESSING_MSGTAG, $model_info['pinyin'], $product_id);
        $result = array_merge($result, NoticeCommon::create_product_order_msgtag_summary(NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG, $model_info['pinyin'], $product_id));
        $result = array_merge($result, NoticeCommon::create_product_order_msgtag_summary(NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG, $model_info['pinyin'], $product_id));
        $result = array_merge($result, NoticeCommon::create_product_order_msgtag_summary(NoticeCommon::PRODUCT_ORDER_CANCEL_MSGTAG, $model_info['pinyin'], $product_id));

        return $result;
    }

    //订单短信模板配置页
    public function action_order_sms()
    {
        $typeid = $this->params['typeid'];
        $model_info = NoticeCommon::get_system_model($typeid);
        if ($model_info === false)
            exit("不正确的typeid：{$typeid}");

        $product_id = $this->params['productid'];
        $msg_config_data = $this->get_order_msgtaglist($model_info, $product_id);
        $msgtypelist = array();
        foreach ($msg_config_data as $order_status_msgtypelist)
        {
            foreach ($order_status_msgtypelist as $order_status_msgtype)
            {
                $msgtypelist[] = $order_status_msgtype;
            }
        }

        $msgtypelist = $this->get_msg_config("sms_msg", $msgtypelist);
        foreach ($msg_config_data as &$order_status_msgtypelist)
        {
            foreach ($order_status_msgtypelist as &$order_status_msgtype)
            {
                $order_status_msgtype = array_merge($msgtypelist[$order_status_msgtype], array('msgtype' => $order_status_msgtype));
            }
        }

        $this->assign("msg_config_data", $msg_config_data);

        $this->display("noticemanager/sms/order_sms");
    }

    //会员相关短信模板配置页
    public function action_member_sms()
    {
        $msg_config_data = NoticeCommon::create_member_msgtag_summary();
        $msgtypelist = array();
        foreach ($msg_config_data as $member_msgtype)
        {
            $msgtypelist[] = $member_msgtype;
        }

        $msgtypelist = $this->get_msg_config("sms_msg", $msgtypelist);
        foreach ($msg_config_data as &$member_msgtype)
        {
            $member_msgtype = array_merge($msgtypelist[$member_msgtype], array('msgtype' => $member_msgtype));
        }

        $this->assign("msg_config_data", $msg_config_data);

        $this->display("noticemanager/sms/member_sms");
    }

    //自定义短信模板配置页（为子类重写而提供）
    public function action_custom_sms()
    {

    }

    /*保存短信模板配置*/
    public function action_ajax_save_sms_msg()
    {
        echo json_encode(array('status' => $this->save_msg_config("sms_msg")));
    }


    //订单邮件模板配置页
    public function action_order_email()
    {
        $typeid = $this->params['typeid'];
        $model_info = NoticeCommon::get_system_model($typeid);
        if ($model_info === false)
            exit("不正确的typeid：{$typeid}");

        $product_id = $this->params['productid'];
        $msg_config_data = $this->get_order_msgtaglist($model_info, $product_id);
        $msgtypelist = array();
        foreach ($msg_config_data as $order_status_msgtypelist)
        {
            foreach ($order_status_msgtypelist as $order_status_msgtype)
            {
                $msgtypelist[] = $order_status_msgtype;
            }
        }

        $msgtypelist = $this->get_msg_config("email_msg", $msgtypelist);
        foreach ($msg_config_data as &$order_status_msgtypelist)
        {
            foreach ($order_status_msgtypelist as &$order_status_msgtype)
            {
                $order_status_msgtype = array_merge($msgtypelist[$order_status_msgtype], array('msgtype' => $order_status_msgtype));
            }
        }

        $this->assign("msg_config_data", $msg_config_data);

        $this->display("noticemanager/email/order_email");
    }

    //会员相关邮件模板配置页
    public function action_member_email()
    {
        $msg_config_data = NoticeCommon::create_member_msgtag_summary();
        $msgtypelist = array();
        foreach ($msg_config_data as $member_msgtype)
        {
            $msgtypelist[] = $member_msgtype;
        }

        $msgtypelist = $this->get_msg_config("email_msg", $msgtypelist);
        foreach ($msg_config_data as &$member_msgtype)
        {
            $member_msgtype = array_merge($msgtypelist[$member_msgtype], array('msgtype' => $member_msgtype));
        }

        $this->assign("msg_config_data", $msg_config_data);

        $this->display("noticemanager/email/member_email");
    }

    //自定义邮件模板配置页（为子类重写而提供）
    public function action_custom_email()
    {

    }

    /*保存邮件模板配置*/
    public function action_ajax_save_email_msg()
    {
        echo json_encode(array('status' => $this->save_msg_config("email_msg")));
    }


}