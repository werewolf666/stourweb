<?php defined('SYSPATH') or die('No direct script access.');
//引入公用函数库
require TOOLS_COMMON . 'functions.php';

class Common extends Functions
{
    public static function xml_to_array($xml)
    {
        $array = (array)(simplexml_load_string($xml));
        foreach ($array as $key => $item)
        {
            $array[$key] = self::struct_to_array((array)$item);
        }
        return $array;
    }

    public static function struct_to_array($item)
    {
        if (!is_string($item))
        {
            $item = (array)$item;
            foreach ($item as $key => $val)
            {
                $item[$key] = self::struct_to_array($val);
            }
        }
        return $item;
    }

    public static function format_sql_text_value($value)
    {
        return str_ireplace("'", "''", $value);
    }

    //验证是否登陆
    public static function checkLogin($secretkey)
    {
        $info = explode('||', self::authcode($secretkey));
        if (isset($info[0]) && $info[1])
        {
            $model = ORM::factory('admin')->where("username='{$info[0]}' and password='{$info[1]}'")->find();
            if (isset($model->id))
                return $model->as_array();
            else
                return 0;
        }
    }


    //操作日志记录
    public static function addLog($controller, $action, $second_action)
    {
        $session = Session::instance();
        $session_username = $session->get('username');
        $uid = $session->get('userid');
        if (empty($uid))
            return;
        $time = date('Y-m-d H:i:s');
        $info = explode('||', self::authcode($session_username));
        $second_action = !empty($second_action) ? '->' . $second_action : '';
        $msg = "用户{$info[0]}在{$time}执行$controller->{$action}{$second_action}操作";
        $logData = array(
            'logtime' => time(),
            'uid' => $uid,
            'username' => $info[0],
            'loginfo' => $msg,
            'logip' => $_SERVER['REMOTE_ADDR']
        );
        foreach ($logData as $key => $value)
        {
            $keys .= $key . ',';
            $values .= "'" . $value . "',";
        }
        $keys = trim($keys, ',');
        $values = trim($values, ',');
        $sql = "insert into sline_user_log($keys) values($values)";
        DB::query(1, $sql)->execute();
    }

    /*
    * curl http访问
    * */
    public static function http($url, $method = 'get', $postfields = '')
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        if ($method == 'POST')
        {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if ($postfields != '')
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }
}
