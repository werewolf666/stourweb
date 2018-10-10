<?php

/**
 * 公共静态类模块
 * User: Netman
 * Date: 15-09-12
 * Time: 下午14:06
 */
class Common{

    /**
     * 获取函数配置
     * @param mixed $name
     * @param mixed $value
     * @return mixed
     */
    static function C($name, $value = '')
    {
        static $_config = array();
        if (is_string($name))
        {
            if (empty($value))
            {
                $value = is_string($_config[$name]) ? trim($_config[$name]) : $_config[$name];
                return isset($_config[$name]) ? $value : null;
            }
            else
            {
                $_config[$name] = $value;
            }
        }
        if (is_array($name))
        {
            $_config = array_merge($_config, $name);
        }
    }

    /**
     * 配置文件初始化
     */
    static function init_config()
    {
        //文件配置
        $_init = array('database', 'convention');
        foreach ($_init as $v)
        {
            self::C((array)Kohana::$config->load($v));
        }
        //数据库配置
        $_init = Model_Sysconfig::payment_config(Common::C('webid'));
        if (!empty($_init))
        {
            self::C($_init);
        }
        //平台判断
        $platform = St_Client::is_mobile()?'mobile':'pc';
        self::C('platform', $platform);
    }

    /**
     * SEESION 管理
     * @param $k
     * @param string $v
     * @return $this|mixed|Session
     */
    public static function session($k, $v = '')
    {
        $session = Session::instance();
        if (empty($v))
        {
            $session = is_null($v) ? $session->delete($k) : $session->get($k);
        }
        else
        {
            $session->set($k, $v);
        }
        return $session;
    }

    /**
     * 动态口令
     * token_name 存入session
     */
    static function token($str = null)
    {
        //设置token
        $tokenName = self::C('token_name');
        $tokenOn = self::C('token_on');
        $session = Session::instance();
        if (is_null($str) && $tokenOn)
        {
            $time = md5(microtime(true));
            $session->set($tokenName, $time);
            return $time;
        }
        //获取token
        $bool = false;
        if ($tokenOn)
        {
            if ($session->get($tokenName) != $_POST[$tokenName])
            {
                $bool = true;
            }
            //删除session['__hash__']
            $session->delete($tokenName);
        }
        return $bool;
    }

    /**
     * 支付成功后，修改订单状态并跳转
     * @param $ordersn
     * @param string $payMethod
     * @param bool|false $line 是否是线下支付
     */
    static function pay_success($ordersn, $payMethod,$line = false)
    {
        St_Payment::pay_success($ordersn,$payMethod,$line);
    }

    /**
     * 支付状态
     * @param $data
     */
    static function pay_status($data)
    {
        St_Payment::pay_status($data);

    }

    /**
     * 支付失败,重新跳转到支付页面
     * @param $ordersn
     */
    static function pay_error($ordersn)
    {
        header("Location:" . Common::C('base_url') . "?ordersn={$ordersn}");
        exit;
    }

    /**
     * 验证失败，跳转404
     */
    static function verify_error()
    {
        header("Location:" . Common::C('base_url') . "/404.html");
        exit;
    }

    /**
     * 支付金额与订单金额是否相等
     * @param $ordersn
     * @param $payMoney
     * @param string $exception
     * @return bool
     */
    static function total_fee_confirm($ordersn, $payMoney, $exception = '')
    {
        return St_Payment::total_fee_confirm($ordersn,$payMoney,$exception);

    }

    /**
     *
     * @param $model_id 模型id
     * @param $product_id
     * @return mixed|string
     */
    static function show_url($model_id, $product_id)
    {
        if (empty($model_id) || empty($product_id))
        {
            return '';
        }
        $model = new Model_Model();
        $model = $model->pinyin_by_id($model_id);
        //没有相关数据
        if (empty($model))
        {
            return '';
        }
        return str_replace(array('{module}', '{aid}'), array($model, $product_id), Common::C('show_url'));
    }

    /**
     * 获取IP地址
     * @return bool
     */
    static function get_ip()
    {
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip)
            {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for ($i = 0; $i < count($ips); $i++)
            {
                if (!preg_match("^(10|172\.16|192\.168)\.", $ips[$i]))
                {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    /**
     * 产品编号 共6位,不足6位前面被0
     * @param $id
     * @param $prefixId
     * @return string
     */
    static function product_number($id, $prefixId)
    {

        $prefixId = str_pad($prefixId, 2, "0", STR_PAD_LEFT);
        $arr = array(
            'A' => '01',
            'B' => '02',
            'C' => '05',
            'D' => '03',
            'E' => '08',
            'G' => '13',
            'H' => '14',
            'I' => '15',
            'J' => '16',
            'K' => '17',
            'L' => '18',
            'M' => '19',
            'N' => '20',
            'O' => '21',
            'P' => '22',
            'Q' => '23',
            'R' => '24',
            'S' => '25',
            'T' => '26'
        );
        return array_search($prefixId, $arr) . str_pad($id, 5, "0", STR_PAD_LEFT);
    }

    /**
     * 参数有效性验证
     * @param $param
     * @param null $token
     * @return bool|string
     */
    static function url_verify($param, $token = null)
    {
        $org = md5($param . var_export(Common::C('default'), true));
        if (is_null($token))
        {
            return $param . '&token=' . $org;
        }
        return $org == $token ? true : false;
    }

    /**
     * 邮件通知
     * @param $orderInfo 订单详情
     */
    static function send_email_message($orderInfo)
    {
        require_once TOOLS_COMMON . 'email/emailservice.php';
        EmailService::send_product_order_email(NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG, $orderInfo);
    }

    /**
     * 短信通知
     * @param $orderInfo 订单详情
     */
    static function send_sms_message($orderInfo)
    {
        require_once TOOLS_COMMON . 'sms/smsservice.php';
        SMSService::send_product_order_msg(NoticeCommon::PRODUCT_ORDER_PAYSUCCESS_MSGTAG, $orderInfo);
    }

    /**
     * 通知信息模板替换
     * @param $content
     * @param $orderInfo
     * @return mixed
     */
    static function sms_message_replace($content, $orderInfo, $sendmember=true)
    {
        $content = str_replace('{#PRODUCTNAME#}', $orderInfo['productname'], $content);
        $content = str_replace('{#PRICE#}', $orderInfo['price'], $content);
        $content = str_replace('{#PHONE#}', $sendmember == true ? Common::C('cfg_phone') : $orderInfo['linktel'], $content);
        $content = str_replace('{#NUMBER#}', $orderInfo['dingnum'], $content);
        $content = str_replace('{#TOTALPRICE#}', $orderInfo['total'], $content);
        $content = str_replace('{#MEMBERNAME#}', $orderInfo['nickname'], $content);
        $content = str_replace('{#WEBNAME#}', Common::C('cfg_webname'), $content);
        $content = str_replace('{#ORDERSN#}', $orderInfo['ordersn'], $content);
        $content = str_replace('{#ETICKETNO#}', $orderInfo['eticketno'], $content);
        return $content;
    }

    /**
     * 发送邮件
     * @param $maillto 收件人
     * @param $title   主题
     * @param $content 内容
     * @return bool
     */
    static function order_maill($maillto, $title, $content)
    {
        require_once TOOLS_COMMON . 'email/emailservice.php';
        $status = EmailService::send_email($maillto, $title, $content);
        return $status;
    }

    /**
     * 查询供应商信息
     * @param $table
     * @param $productAid
     * @return mixed
     */
    static function get_supplier($table, $productAid)
    {
        $table = $table == "tongyong" ? "model_archive" : $table;
        $sql = "select s.* from sline_{$table} as t,sline_supplier as s where t.aid={$productAid} and t.supplierlist=s.id";
        return DB::query(Database::SELECT, $sql)->execute()->current();
    }

    /**
     * 主站域名
     * @return string
     */
    static function get_main_host()
    {
        $host = '';
        $sql = "select weburl from sline_weblist where webid=0";
        $arr = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($arr))
        {
            $host = $arr['weburl'];
        }
        return $host;
    }

    /**
     * COOKIE 域名
     * @return string
     */
    static function cookie_domain()
    {
        $host = $_SERVER['HTTP_HOST'];
        $sql = "select * from sline_weblist where webid=0";
        $arr = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($arr))
        {
            $host = str_replace($arr['webprefix'] . '.', '', parse_url($arr['weburl'], PHP_URL_HOST));
        }
        return $host;
    }

    /**
     * 检查优惠券是否安装
     */
    public function is_coupon_instal()
    {

        $data = DB::select()->from('app')->where('productcode', '=', 'stourwebcms_app_coupon')->and_where('status', '=', 1)->execute()->current();
        empty($data) ? $status = 0 : $status = 1;
        return $status;

    }

    /**
     * 优惠券抵用金额
     */
    public function get_order_view($ordersn)
    {

        $sql = "select a.cmoney ,b.name from sline_member_order_coupon as a LEFT JOIN sline_coupon as b on a.cid=b.id WHERE a.ordersn = $ordersn";

        return DB::query(1,$sql)->execute()->current();

    }

}
