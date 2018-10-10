<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Member_Order_listener extends ORM
{
    public static function detect($ordersn)
    {
        $sql = "select * from sline_member_order where ordersn='{$ordersn}'";
        $orderlist = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($orderlist) <= 0)
        {
            self::write_order_listener_log($ordersn, "query_order", "no order,sql:{$sql}");
            return true;
        }
        $sql = <<<sql
        SELECT
            *
        FROM
            sline_member_order_listener
        WHERE
            (webid IS NULL OR webid = {$orderlist[0]['webid']})
        AND (typeid IS NULL OR typeid = {$orderlist[0]['typeid']})
        AND (
            supplierlist IS NULL
            OR supplierlist = ''
            OR supplierlist = '{$orderlist[0]['supplierlist']}'
        )
        AND (
            distributor IS NULL
            OR distributor = ''
            OR distributor = '{$orderlist[0]['distributor']}'
        )
        AND (
            productautoid IS NULL
            OR productautoid = {$orderlist[0]['productautoid']}
        )
        AND (suitid IS NULL OR suitid = {$orderlist[0]['suitid']})
        AND (
            order_status IS NULL
            OR order_status = {$orderlist[0]['status']}
        )
        AND isenabled = 1
sql;

        $listenerlist = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (count($listenerlist) <= 0)
        {
            self::write_order_listener_log($ordersn, "query_order_listener", "no order listener,sql:{$sql}");
            return true;
        }

        $host = DB::query(Database::SELECT, "select weburl from sline_weblist where webid=0")->execute()->as_array();
        if (count($host) > 0 && !empty($host[0]['weburl']))
            $host = $host[0]['weburl'];
        else
            $host = St_Functions::get_http_prefix()."{$_SERVER['HTTP_HOST']}";

        foreach ($listenerlist as $listener)
        {
            $execurl = $listener['execute_url'];
            if (stristr($execurl, '?') === false)
            {
                $execurl = $execurl . "?ordersn={$ordersn}";
            } else
            {
                $execurl = $execurl . "&ordersn={$ordersn}";
            }

            $execurl = trim($execurl);
            if (stripos($execurl, "http://") !== 0||stripos($execurl, "https://") !== 0)
                $execurl = "{$host}/{$execurl}";

            $execresult_text = self::request($execurl);
            self::write_order_listener_log($execurl, "call_order_listener", $execresult_text);
            $execresult = json_decode($execresult_text);

            $retry = 0;
            while (!$execresult && $retry < 3)
            {
                $execresult_text = self::request($execurl);
                self::write_order_listener_log($execurl, "call_order_listener_{$retry}", $execresult_text);
                $execresult = json_decode($execresult_text);

                $retry++;
            }

            if ($execresult->status != 1)
            {
                return $execresult->msg;
            }
        }
        return true;
    }

    private static function write_order_listener_log($ordersn, $action, $result)
    {
        $payLogDir = BASEPATH . '/data/order_listener_log/';
        if (!file_exists($payLogDir))
        {
            mkdir($payLogDir, 0777, true);
        }
        //日志文件
        $file = $payLogDir . date('ymd') . '.txt';
        $now = date('YmdHis');

        $data = "=========================" . PHP_EOL;
        $data .= "ordersn:{$ordersn} {$now}" . PHP_EOL;
        $data .= "action:{$action}" . PHP_EOL;
        $data .= "result:{$result}" . PHP_EOL;

        file_put_contents($file, $data, FILE_APPEND);
    }

    private static function request($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

        $return = curl_exec($ch);
        curl_close($ch);

        return $return;
    }
}