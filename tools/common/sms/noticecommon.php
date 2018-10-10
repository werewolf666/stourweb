<?php defined('SYSPATH') or die('No direct script access.');
require_once TOOLS_COMMON . 'functions.php';

/**
 * 邮件与短信通知发送公共类.
 * User: Administrator
 * Date: 16-6-17
 * Time: 下午4:49
 */
class NoticeCommon
{
    const MEMBER_REG_MSGTAG = "reg";
    const MEMBER_REG_CODE_MSGTAG = "reg_msgcode";
    const MEMBER_FINDPWD_CODE_MSGTAG = "reg_findpwd";

    const PRODUCT_ORDER_UNPROCESSING_MSGTAG = "order_msg1";
    const PRODUCT_ORDER_PROCESSING_MSGTAG = "order_msg2";
    const PRODUCT_ORDER_PAYSUCCESS_MSGTAG = "order_msg3";
    const PRODUCT_ORDER_CANCEL_MSGTAG = "order_msg4";

    const EMAIL_MSGTYPE = "email";
    const SMS_MSGTYPE = "sms";

    const EMAIL_NOSEND_FLAG = "xxxx@xxxx.xxx";
    const SMS_NOSEND_FLAG = "00000000000";


    public static function create_product_order_msgtag_summary($product_order_msgtag, $model_pinyin, $product_id = "")
    {
        $result = array();

        $msgtypelist = array();
        if (empty($product_id))
        {
            $msgtypelist["会员"] = "{$model_pinyin}_{$product_order_msgtag}";
            $msgtypelist["供应商"] = "supplier_{$model_pinyin}_{$product_order_msgtag}";
            $msgtypelist["管理员"] = "administrator_{$model_pinyin}_{$product_order_msgtag}";
        } else
        {
            $msgtypelist["会员"] = "{$model_pinyin}_{$product_id}_{$product_order_msgtag}";
            $msgtypelist["供应商"] = "supplier_{$model_pinyin}_{$product_id}_{$product_order_msgtag}";
            $msgtypelist["管理员"] = "administrator_{$model_pinyin}_{$product_id}_{$product_order_msgtag}";
        }

        if (self::PRODUCT_ORDER_UNPROCESSING_MSGTAG == $product_order_msgtag)
            $result['订单未处理'] = $msgtypelist;
        if (self::PRODUCT_ORDER_PROCESSING_MSGTAG == $product_order_msgtag)
            $result['订单处理中'] = $msgtypelist;
        if (self::PRODUCT_ORDER_PAYSUCCESS_MSGTAG == $product_order_msgtag)
            $result['订单付款成功'] = $msgtypelist;
        if (self::PRODUCT_ORDER_CANCEL_MSGTAG == $product_order_msgtag)
            $result['订单取消'] = $msgtypelist;

        return $result;
    }

    public static function create_member_msgtag_summary()
    {
        $result = array();

        $result["注册成功"] = self::MEMBER_REG_MSGTAG;
        $result["注册验证码"] = self::MEMBER_REG_CODE_MSGTAG;
        $result["找回密码"] = self::MEMBER_FINDPWD_CODE_MSGTAG;

        return $result;
    }

    public static function get_system_model($typeid)
    {
        $sql = "select * from sline_model where id='{$typeid}'";
        $model_arr = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($model_arr) <= 0)
        {
            return false;
        }

        $model_info = $model_arr[0];
        if (empty($model_info['id']) || empty($model_info['pinyin']))
        {
            return false;
        }
        return $model_info;
    }

    public static function summary_price_number($orderinfo)
    {
        $result = array(
            'totalNumber' => 0,
            'totalPrice' => 0,
            'numberDescript' => '',
            'priceDescript' => ''
        );

        if (is_array($orderinfo))
        {
            $sql = "select id from sline_member_order where ordersn='{$orderinfo['ordersn']}' order by id DESC limit 1 ";
            $orderid = DB::query(Database::SELECT, $sql)->execute()->current();
            if (is_array($orderid))
            {
                $totalPrice = Model_Member_Order::order_total_payprice($orderid['id']);
                $result['totalPrice'] = sprintf("%.2f", $totalPrice);
                $totalNumber = $orderinfo['dingnum'] + $orderinfo['childnum'] + $orderinfo['oldnum'];
                $result['totalNumber'] = $totalNumber;

                if (!empty($orderinfo['childnum']) || !empty($orderinfo['oldnum']))
                {
                    $priceDescript = '';
                    $numberDescript = '';
                    if (!empty($orderinfo['dingnum']))
                    {
                        $priceDescript = $priceDescript . sprintf("%.2f", $orderinfo['price']) . '(成)';
                        $numberDescript = $numberDescript . $orderinfo['dingnum'] . '(成)';
                    }
                    if (!empty($orderinfo['childnum']))
                    {
                        $priceDescript = $priceDescript . sprintf("%.2f", $orderinfo['childprice']) . '(小)';
                        $numberDescript = $numberDescript . $orderinfo['childnum'] . '(小)';
                    }
                    if (!empty($orderinfo['oldnum']))
                    {
                        $priceDescript = $priceDescript . sprintf("%.2f", $orderinfo['oldprice']) . '(老)';
                        $numberDescript = $numberDescript . $orderinfo['oldnum'] . '(老)';
                    }
                    $result['priceDescript'] = $priceDescript;
                    $result['numberDescript'] = $numberDescript;
                } else
                {
                    $result['priceDescript'] = sprintf("%.2f", $orderinfo['price']);
                    $result['numberDescript'] = $orderinfo['dingnum'];
                }
            }
        }
        return $result;
    }


    /*
    *@function 生成会员特定事件消息内容
    *@param string $msgtag,会员短信事件类型，NoticeCommon::MEMBER_REG_MSGTAG：会员注册成功后；NoticeCommon::MEMBER_REG_CODE_MSGTAG：会员注册验证码；NoticeCommon::MEMBER_FINDPWD_CODE_MSGTAG：会员找回密码验证码；
    *@param string $msgtype,消息类型，NoticeCommon::EMAIL_MSGTYPE：email消息；NoticeCommon::SMS_MSGTYPE：sms消息
    *@param string $phone,会员手机号
    *@param string $phone,会员email
    *@param string $member_account,会员帐户，在会员注册成功事件中传递.
    *@param string $member_password,会员帐户，在会员注册成功事件中传递.
    *@param string $code,验证码.
    *@return array 例如：array('member'=>array('isopen'=>1,'recipient'=>'13888888888','content'=>'您的验证码为：05841'))
    * */
    public static function generate_member_msg_content($msgtag, $msgtype, $phone, $email, $member_account, $member_password, $code)
    {
        $result = array(
            'member' => array('isopen' => 1, 'recipient' => '', 'content' => '')
        );

        $result["member"]["recipient"] = ($msgtype == NoticeCommon::EMAIL_MSGTYPE ? $email : $phone);

        $sql = "select msg,isopen from sline_{$msgtype}_msg where msgtype='{$msgtag}'";
        $config_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($config_data) > 0)
        {
            if ($config_data[0]["isopen"] == "0")
            {
                $result["member"]["isopen"] = 0;
            }

            if (!empty($config_data[0]["msg"]))
            {
                $content = $config_data[0]["msg"];
                $cfg_webname = functions::get_sys_para("cfg_webname");
                $content = str_ireplace('{#WEBNAME#}', $cfg_webname, $content);
                $content = str_ireplace('{#PHONE#}', $phone, $content);
                $content = str_ireplace('{#EMAIL#}', $email, $content);
                $content = str_ireplace('{#LOGINNAME#}', $member_account, $content);
                $content = str_ireplace('{#PASSWORD#}', $member_password, $content);
                $content = str_ireplace('{#CODE#}', $code, $content);

                $result["member"]["content"] = $content;
            }

        }

        return $result;

    }

    /*
	 *@function 生成产品订单特定事件消息内容
	 *@param string $msgtag,产品订单特定事件类型，NoticeCommon::PRODUCT_ORDER_UNPROCESSING_MSGTAG：订单未处理；NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG：订单处理中；NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG：订单处理中支付成功；NoticeCommon::PRODUCT_ORDER_CANCEL_MSGTAG：订单被取消；
	 *@param string $msgtype,消息类型，NoticeCommon::EMAIL_MSGTYPE：email消息；NoticeCommon::SMS_MSGTYPE：sms消息
     *@param array $order_info,订单详细信息.
     *@return array 例如：array('member'=>array('isopen'=>1, 'recipient' => '13888888888','content'=>'您的存在未处理的订单：512505841'),'supplier'=>array('isopen'=>1, 'recipient' => '','content'=>''),'administrator'=>array('isopen'=>1, 'recipient' => '','content'=>''))
	 * */
    public static function generate_product_order_msg_content($msgtag, $msgtype, array $order_info)
    {
        $result = array(
            'member' => array('isopen' => 1, 'recipient' => '', 'content' => ''),
            'supplier' => array('isopen' => 1, 'recipient' => '', 'content' => ''),
            'administrator' => array('isopen' => 1, 'recipient' => '', 'content' => '')
        );

        $model_info = NoticeCommon::get_system_model($order_info["typeid"]);
        if ($model_info === false)
        {
            return $result;
        }

        $order_price_num_summary = NoticeCommon::summary_price_number($order_info);
        $cfg_webname = functions::get_sys_para("cfg_webname");

        $product_order_msgtag_summary_result = NoticeCommon::create_product_order_msgtag_summary($msgtag, $model_info['pinyin']);
        $product_order_productid_msgtag_summary_result = NoticeCommon::create_product_order_msgtag_summary($msgtag, $model_info['pinyin'], $order_info['productautoid']);
        foreach ($product_order_msgtag_summary_result as $product_order_msgtag_summary_item => $product_order_msgtag_list)
        {
            foreach ($product_order_msgtag_list as $product_order_msgtag_name => $product_order_msgtag_value)
            {
                $recipients_tag = explode("_", $product_order_msgtag_value);
                $recipients_tag = $recipients_tag[0];
                $recipient = "";
                if ($recipients_tag == "supplier")
                {
                    if (!empty($order_info["productautoid"]))
                    {
                        $table = 'sline_' . $model_info['maintable'];

                        $sql = "show columns from `{$table}` like 'supplierlist'";
                        $supplierlist_columns = DB::query(Database::SELECT, $sql)->execute()->as_array();
                        if (count($supplierlist_columns) > 0)
                        {
                            $sql = "SELECT supplierlist FROM {$table} where id='{$order_info["productautoid"]}'";
                            $product_rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
                            if (count($product_rows) > 0 && !empty($product_rows[0]['supplierlist']))
                            {
                                $supplierid = $product_rows[0]['supplierlist'];
                                $sql = "SELECT * FROM `sline_supplier` WHERE id='{$supplierid}'";
                                $supplier_rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
                                if (count($supplier_rows) > 0)
                                {
                                    $recipient = (NoticeCommon::EMAIL_MSGTYPE == $msgtype ? $supplier_rows[0]["email"] : $supplier_rows[0]["mobile"]);
                                }
                            }
                        }
                    }

                } elseif ($recipients_tag == "administrator")
                {
                    $recipient = (NoticeCommon::EMAIL_MSGTYPE == $msgtype ? functions::get_sys_para("cfg_webmaster_email") : functions::get_sys_para("cfg_webmaster_phone"));
                } else
                {
                    $recipients_tag = "member";
                    //如果支付总价为0，不给会员发送订单处理中（提醒支付）消息
                    if ($msgtag == NoticeCommon::PRODUCT_ORDER_PROCESSING_MSGTAG && $order_price_num_summary['totalPrice'] <= 0)
                    {
                        $recipient = (NoticeCommon::EMAIL_MSGTYPE == $msgtype ? NoticeCommon::EMAIL_NOSEND_FLAG : NoticeCommon::SMS_NOSEND_FLAG);
                    } else
                    {
                        $recipient = (NoticeCommon::EMAIL_MSGTYPE == $msgtype ? $order_info['linkemail'] : $order_info['linktel']);
                    }
                }

                $result[$recipients_tag]["recipient"] = $recipient;

                $config_data = array();
                $product_order_productid_msgtag_value = $product_order_productid_msgtag_summary_result[$product_order_msgtag_summary_item][$product_order_msgtag_name];
                if (!empty($product_order_productid_msgtag_value))
                {
                    $sql = "select msg,isopen from sline_{$msgtype}_msg where msgtype='{$product_order_productid_msgtag_value}'";
                    $config_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
                }
                if (count($config_data) <= 0 || ($config_data[0]["isopen"] != "0" && empty($config_data[0]["msg"])))
                {
                    $sql = "select msg,isopen from sline_{$msgtype}_msg where msgtype='{$product_order_msgtag_value}'";
                    $config_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
                }

                if (count($config_data) > 0)
                {
                    if ($config_data[0]["isopen"] == "0")
                    {
                        $result[$recipients_tag]["isopen"] = 0;
                    }

                    if (!empty($config_data[0]["msg"]))
                    {
                        $content = $config_data[0]["msg"];
                        $content = str_ireplace('{#WEBNAME#}', $cfg_webname, $content);
                        $content = str_ireplace('{#PHONE#}', $order_info['linktel'], $content);
                        $content = str_ireplace('{#MEMBERNAME#}', $order_info['linkman'], $content);
                        $content = str_ireplace('{#PRODUCTNAME#}', $order_info["productname"], $content);
                        $content = str_ireplace('{#PRICE#}', $order_price_num_summary['priceDescript'], $content);
                        $content = str_ireplace('{#NUMBER#}', $order_price_num_summary['numberDescript'], $content);
                        $content = str_ireplace('{#TOTALPRICE#}', $order_price_num_summary['totalPrice'], $content);
                        $content = str_ireplace('{#ORDERSN#}', $order_info['ordersn'], $content);
                        $content = str_ireplace('{#ETICKETNO#}', $order_info['eticketno'], $content);
                        $content = str_ireplace('{#USEDATE#}', $order_info['usedate'], $content);
                        $content = str_ireplace('{#DEPARTDATE#}', $order_info['departdate'], $content);

                        $result[$recipients_tag]["content"] = $content;
                    }
                }

            }
        }

        return $result;

    }
} 