<?php defined('SYSPATH') or die('No direct script access.');
require_once TOOLS_COMMON . 'sms/noticecommon.php';

/**
 * 短信发送公共类.
 * User: Administrator
 * Date: 16-6-17
 * Time: 下午4:49
 */
class SMSService
{
    /*
	 *@function 直接给某个电话发送任意内容短消息
	 *@param string $phone,接收手机号
	 *@param string $prefix,短信签名,如"xx旅行网",短信中显示【xx旅行网】
	 *@param string $content,短信内容.
     *@return json 例如：{"Success":false,"Message":"短信帐户余额不足，可用短信0条，需要1条","Data":null}
	 * */
    public static function send_msg($phone, $prefix, $content)
    {
        $config_data = functions::get_sys_para('cfg_webname');
        if ($config_data != "")
            $prefix = $config_data;

        $result = array('Success' => false, 'Message' => '', 'Data' => null);

        if($phone == NoticeCommon::SMS_NOSEND_FLAG)
        {
            $result['Success'] = true;
            $result['Message'] = '标记为不发送';
            return json_encode($result);
        }

        $provider = self::get_open_provider();
        if ($provider == null)
        {
            $result['Message'] = '不能找到可用的短信提供商';
            return json_encode($result);
        }

        $provider_instance = self::create_provider_instance($provider);
        if (is_object($provider_instance) && method_exists($provider_instance, 'send_msg'))
        {
            if (strpos($phone, ",") !== false)
            {
                $result["Success"] = true;
                $send_result = json_encode($result);
                $phone_arr = explode(",", $phone);
                foreach ($phone_arr as $phone_item)
                {
                    if (!empty($phone_item))
                    {
                        $send_result = $provider_instance->send_msg($phone_item, $prefix, $content);
                    }
                }
                return $send_result;
            } else
            {
                return $provider_instance->send_msg($phone, $prefix, $content);
            }
        } else
        {
            $result['Message'] = '短信发送程序错误';
            return json_encode($result);
        }
    }


    /*
	 *@function 会员特定事件短信发送
	 *@param string $phone,接收手机号
	 *@param string $msgtag,会员短信事件类型，NoticeCommon::MEMBER_REG_MSGTAG：会员注册成功后；NoticeCommon::MEMBER_REG_CODE_MSGTAG：会员注册验证码；NoticeCommon::MEMBER_FINDPWD_CODE_MSGTAG：会员找回密码验证码；
	 *@param string $member_account,会员帐户，在会员注册成功事件中传递.
     *@param string $member_password,会员帐户，在会员注册成功事件中传递.
     *@param string $code,验证码.
     *@return json 例如：{"Success":false,"Message":"短信帐户余额不足，可用短信0条，需要1条","Data":null}
	 * */
    public static function send_member_msg($phone, $msgtag, $member_account, $member_password, $code)
    {
        $result = array('Success' => false, 'Message' => '', 'Data' => null);

        $content = NoticeCommon::generate_member_msg_content($msgtag, NoticeCommon::SMS_MSGTYPE, $phone, "", $member_account, $member_password, $code);
        if ($content['member']['isopen'] == 0)
        {
            $result['Message'] = "短信发送开关被关闭";
            return json_encode($result);
        }
        if (empty($content['member']['recipient']))
        {
            $result['Message'] = "接收人号码不正确";
            return json_encode($result);
        }
        if (strlen($content['member']['content']) <= 0)
        {
            $result['Message'] = "消息内容为空";
            return json_encode($result);
        }

        return self::send_msg($content['member']['recipient'], "", $content['member']['content']); //发送短信.

    }

    /*
	 *@function 产品订单特定事件短信发送
	 *@param string $msgtag,产品订单特定事件类型，NoticeCommon::PRODUCT_ORDER_UNPROCESSING_MSGTAG：订单未处理；NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG：订单处理中；NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG：订单处理中支付成功；NoticeCommon::PRODUCT_ORDER_CANCEL_MSGTAG：订单被取消；
	 *@param array $order_info,订单详细信息.
     *@return json 例如：{"Success":false,"Message":"短信帐户余额不足，可用短信0条，需要1条","Data":null}
	 * */
    public static function send_product_order_msg($msgtag, array $order_info)
    {
        $result = array('Success' => true, 'Message' => '', 'Data' => null);

        $content = NoticeCommon::generate_product_order_msg_content($msgtag, NoticeCommon::SMS_MSGTYPE, $order_info);
        foreach ($content as $content_recipient => $content_item)
        {
            if ($content_item['isopen'] == 0)
            {
                $result['Message'] .= "{$content_recipient}：短信发送开关被关闭;";
                $result['Success'] = false;
                continue;
            }
            if (empty($content_item['recipient']))
            {
                $result['Message'] .= "{$content_recipient}：接收人号码不正确;";
                $result['Success'] = false;
                continue;
            }
            if (strlen($content_item['content']) <= 0)
            {
                $result['Message'] .= "{$content_recipient}：消息内容为空;";
                $result['Success'] = false;
                continue;
            }

            $sms_send_result = json_decode(self::send_msg($content_item["recipient"], "", $content_item['content'])); //发送短信.
            if (!$sms_send_result->Success)
            {
                $result['Message'] .= "{$content_recipient}：" . $sms_send_result->Message . ';';
                $result['Success'] = false;
            }


        }

        return json_encode($result);
    }

    /*
    *@function 自定义模板短信发送
    *@param string $phone,接收手机号
    *@param string $msgtag,自定义模板名称（msgtype）
    *@param array $msg_placeholder_list,自定义模板中占位符替换值列表.
    *@return json 例如：{"Success":false,"Message":"短信帐户余额不足，可用短信0条，需要1条","Data":null}
    * */
    public static function send_custom_msg($phone, $msgtag, array $msg_placeholder_list)
    {
        $result = array('Success' => false, 'Message' => '', 'Data' => null);

        $sql = "select msg,isopen from sline_sms_msg where msgtype='{$msgtag}'";
        $config_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($config_data) <= 0 || empty($config_data[0]["msg"]))
        {
            $result['Message'] = "不能找到可用的短信模板";
            return json_encode($result);
        }

        if ($config_data[0]["isopen"] == "0")
        {
            $result["Message"] = "短信发送开关被关闭";
            return json_encode($result);
        }

        $content = $config_data[0]["msg"];
        foreach ($msg_placeholder_list as $msg_placeholder_name => $msg_placeholder_value)
        {
            $content = str_ireplace("{#{$msg_placeholder_name}#}", $msg_placeholder_value, $content);
        }

        return self::send_msg($phone, "", $content); //发送短信.

    }


    public static function get_all_provider()
    {
        $sql = "select * from sline_sms_provider";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public static function get_provider($id)
    {
        $sql = "select * from sline_sms_provider where id={$id}";
        $result = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($result) <= 0)
            return null;
        return $result[0];
    }

    public static function get_open_provider()
    {
        $sql = "select * from sline_sms_provider where isopen=1";
        $result = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($result) <= 0)
            return null;
        return $result[0];
    }

    public static function set_open_provider(array $provider)
    {
        $sql = "update sline_sms_provider set isopen=0";
        DB::query(Database::UPDATE, $sql)->execute();
        $sql = "update sline_sms_provider set isopen=1 where id={$provider['id']}";
        DB::query(Database::UPDATE, $sql)->execute();
    }

    public static function set_close_provider(array $provider)
    {
        $sql = "update sline_sms_provider set isopen=0 where id={$provider['id']}";
        DB::query(Database::UPDATE, $sql)->execute();
    }

    public static function create_provider_instance(array $provider)
    {
        require_once BASEPATH . $provider['execute_file'];
        return new $provider['execute_classname']();
    }
} 