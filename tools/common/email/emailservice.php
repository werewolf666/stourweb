<?php defined('SYSPATH') or die('No direct script access.');
require_once TOOLS_COMMON . 'sms/noticecommon.php';

/**
 * 邮件发送公共类.
 * User: Administrator
 * Date: 16-6-17
 * Time: 下午4:49
 */
class EmailService
{
    /*
     *@function 直接给某个email发送任意内容邮件
     *@param string $maillto,接收email
     *@param string $title,邮件标题
     *@param string $content,邮件内容.
     *@return bool
     * */
    public static function send_email($maillto, $title, $content)
    {
        if (empty($maillto) || empty($title))
            return false;

        //标记为不发送
        if ($maillto == NoticeCommon::EMAIL_NOSEND_FLAG)
        {
            return true;
        }

        //如果没有自定义SMTP配置
        $smtp_config = self::get_email_config();
        $cfg_mail_smtp = ($smtp_config['cfg_mail_smtp'] == '' ? "smtp.163.com" : $smtp_config['cfg_mail_smtp']);
        $cfg_mail_port = ($smtp_config['cfg_mail_port'] == '' ? 25 : $smtp_config['cfg_mail_port']);
        $cfg_mail_securetype = $smtp_config['cfg_mail_securetype'];
        if ($smtp_config['cfg_mail_user'] == '')
        {
            $cfg_mail_user = "Stourweb@163.com";
            $cfg_mail_pass = "kelly12345";
        } else
        {
            $cfg_mail_user = $smtp_config['cfg_mail_user'];
            $cfg_mail_pass = $smtp_config['cfg_mail_pass'];
        }
        $smtpserver = $cfg_mail_smtp; //SMTP服务器
        $smtpserverport = $cfg_mail_port; //SMTP服务器端口
        $smtpserversecuretype = $cfg_mail_securetype; //SMTP服务器加密安全类型：开放（无），ssl,tls
        $smtpemailto = $maillto; //发送给谁
        $smtpuser = $cfg_mail_user; //SMTP服务器的用户帐号
        $smtppass = $cfg_mail_pass; //SMTP服务器的用户密码
        //$mailtype = "HTML"; //邮件格式（HTML/TXT）,TXT为文本邮件

        require_once dirname(__FILE__) . '/class.phpmailer.php';
        try
        {
            $mail = new PHPMailer(true); //建立邮件发送类
            $mail->CharSet = "UTF-8"; //设置信息的编码类型
            $mail->IsSMTP(); // 使用SMTP方式发送
            $mail->Host = $smtpserver; //使用163邮箱服务器
            $mail->Port = intval($smtpserverport); //邮箱服务器端口号
            $mail->SMTPAuth = true; // 启用SMTP验证功能
            if (!empty($smtpserversecuretype))
            {
                $mail->SMTPSecure = $smtpserversecuretype;
            }
            $mail->Username = $smtpuser; //你的163服务器邮箱账号
            $mail->Password = $smtppass; // 163邮箱密码

            $mail->From = $smtpuser; //邮件发送者email地址
            $sender = explode("@", $smtpuser);
            $mail->FromName = $sender[0]; //发件人名称
            if (strpos($smtpemailto, ",") !== false)
            {
                $smtpemailto_arr = explode(",", $smtpemailto);
                foreach ($smtpemailto_arr as $smtpemailto_item)
                {
                    if (!empty($smtpemailto_item))
                    {
                        $mail->AddAddress($smtpemailto_item); //收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
                    }
                }
            } else
            {
                $mail->AddAddress($smtpemailto); //收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
            }
            //$mail->AddAttachment("D:\abc.txt"); // 添加附件(注意：路径不能有中文)
            $mail->IsHTML(true); //是否使用HTML格式
            $mail->Subject = $title; //邮件标题
            $mail->Body = $content; //邮件内容，上面设置HTML，则可以是HTML

            return $mail->Send();
        } catch (Exception $ex)
        {
            return false;
        }
    }

    /*
	 *@function 会员特定事件邮件发送
	 *@param string $maillto,接收email
	 *@param string $msgtag,会员短信事件类型，NoticeCommon::MEMBER_REG_MSGTAG：会员注册成功后；NoticeCommon::MEMBER_REG_CODE_MSGTAG：会员注册验证码；NoticeCommon::MEMBER_FINDPWD_CODE_MSGTAG：会员找回密码验证码；
     *@param string $member_password,会员密码，在会员注册成功事件中传递.
     *@param string $code,验证码.
     *@return bool
	 * */
    public static function send_member_email($maillto, $msgtag, $member_password, $code)
    {
        $result = false;

        $content = NoticeCommon::generate_member_msg_content($msgtag, NoticeCommon::EMAIL_MSGTYPE, "", $maillto, "", $member_password, $code);
        if ($content['member']['isopen'] == 0)
        {
            return $result;
        }
        if (empty($content['member']['recipient']) || strlen($content['member']['content']) <= 0)
        {
            return $result;
        }

        $cfg_webname = functions::get_sys_para("cfg_webname");
        $title = (empty($code) ? "会员注册成功" : "验证码") . '[' . $cfg_webname . ']';
        return self::send_email($content['member']['recipient'], $title, $content['member']['content']); //发送

    }

    /*
	 *@function 产品订单特定事件邮件发送
	 *@param string $msgtag,产品订单特定事件类型，NoticeCommon::PRODUCT_ORDER_UNPROCESSING_MSGTAG：订单未处理；NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG：订单处理中；NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG：订单处理中支付成功；NoticeCommon::PRODUCT_ORDER_CANCEL_MSGTAG：订单被取消；
	 *@param array $order_info,订单详细信息.
     *@return bool
	 * */
    public static function send_product_order_email($msgtag, array $order_info)
    {
        $result = true;

        $cfg_webname = functions::get_sys_para("cfg_webname");
        $content = NoticeCommon::generate_product_order_msg_content($msgtag, NoticeCommon::EMAIL_MSGTYPE, $order_info);
        foreach ($content as $content_recipient => $content_item)
        {
            if ($content_item['isopen'] == 0)
            {
                $result = false;
                continue;
            }
            if (empty($content_item['recipient']) || strlen($content_item['content']) <= 0)
            {
                $result = false;
                continue;
            }

            $title = "预定" . $order_info['productname'] . '[' . $cfg_webname . ']';
            if (!self::send_email($content_item["recipient"], $title, $content_item['content'])) //发送
            {
                $result = false;
            }

        }

        return $result;

    }

    /*
   *@function 自定义模板email发送
   *@param string $maillto,接收邮件地址
   *@param string $mailltitle,邮件标题
   *@param string $msgtag,自定义模板名称（msgtype）
   *@param array $msg_placeholder_list,自定义模板中占位符替换值列表.
   *@return bool
   * */
    public static function send_custom_email($maillto, $mailltitle, $msgtag, array $msg_placeholder_list)
    {
        $sql = "select msg,isopen from sline_email_msg where msgtype='{$msgtag}'";
        $config_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($config_data) <= 0 || empty($config_data[0]["msg"]))
            return false;

        if ($config_data[0]["isopen"] == "0")
            return false;

        $content = $config_data[0]["msg"];
        foreach ($msg_placeholder_list as $msg_placeholder_name => $msg_placeholder_value)
        {
            $content = str_ireplace("{#{$msg_placeholder_name}#}", $msg_placeholder_value, $content);
        }

        return self::send_email($maillto, $mailltitle, $content); //发送
    }


    public static function get_email_config()
    {
        $sql = "select varname,value from sline_sysconfig where
         (varname='cfg_mail_smtp' or
         varname='cfg_mail_port' or
         varname='cfg_mail_securetype' or
         varname='cfg_mail_user' or
         varname='cfg_mail_pass')
         and webid=0";

        $config_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        $result = array(
            'cfg_mail_smtp' => "",
            'cfg_mail_port' => "",
            'cfg_mail_securetype' => "",
            'cfg_mail_user' => "",
            'cfg_mail_pass' => ""
        );

        foreach ($config_data as $config_data_item)
        {
            if ($config_data_item['varname'] == 'cfg_mail_smtp')
                $result['cfg_mail_smtp'] = $config_data_item['value'];

            if ($config_data_item['varname'] == 'cfg_mail_port')
                $result['cfg_mail_port'] = $config_data_item['value'];

            if ($config_data_item['varname'] == 'cfg_mail_securetype')
                $result['cfg_mail_securetype'] = $config_data_item['value'];

            if ($config_data_item['varname'] == 'cfg_mail_user')
                $result['cfg_mail_user'] = $config_data_item['value'];

            if ($config_data_item['varname'] == 'cfg_mail_pass')
                $result['cfg_mail_pass'] = $config_data_item['value'];
        }

        return $result;

    }

    public static function set_email_config(array $config_data)
    {
        foreach ($config_data as $key => $val)
        {
            $sql = "select * from sline_sysconfig where webid=0 and varname='{$key}'";
            $result = DB::query(Database::SELECT, $sql)->execute()->as_array();
            if (count($result) <= 0)
                $sql = "insert into sline_sysconfig(webid,
                        varname,
                        value
                        ) values (
                        '0',
                        '{$key}',
                        '{$val}'
                        )";
            else
                $sql = "update sline_sysconfig set value='{$val}' where webid=0 and varname='{$key}'";


            DB::query(Database::UPDATE, $sql)->execute();
        }
    }
} 