<?php defined('SYSPATH') or die('No direct access allowed.');
require TOOLS_COMMON . '/sms/ismsprovider.php';

class StourSMSProvider implements ISMSProvider
{
    var $_apiUrl = 'http://sms.souxw.com/service/api.ashx?'; //短信接口地址
    var $_account_data = array();

    function __construct()
    {
        $sql = "SELECT * from sline_sysconfig where varname='cfg_sms_username' or varname='cfg_sms_password'";
        $rows = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($rows as $row)
        {
            if ($row['varname'] == 'cfg_sms_username')
                $this->_account_data['account'] = $row['value'];

            if ($row['varname'] == 'cfg_sms_password')
                $this->_account_data['password'] = md5($row['value']);
        }
    }


    /*
	 * 发送短消息
	 *@param string $phone,接收手机号
	 *@param string $prefix,短信签名,如"xx旅行网",短信中显示【xx旅行网】
	 *@param string $content,短信内容.
     *@return json {"Success":false,"Message":"短信帐户余额不足，可用短信0条，需要1条","Data":null}
     *@json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
	 * */

    public function send_msg($phone, $prefix, $content)
    {
        $init = array(
            'action' => 'sendsms',
            'telno' => $phone,
            'contentprefix' => $prefix,
            'content' => $content
        );
        $data = array_merge($this->_account_data, $init); //合并数组
        $params = http_build_query($data); //生成参数数组
        $url = $this->_apiUrl;

        return $this->http($url,$params,"POST");
    }

    /*
    * @查询短信帐户余额(条数)
    * @return json {"Success":true,"Message":null,"Data":0}
    * @json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
    * */
    public function query_balance()
    {
        $init = array(
            'action' => 'querysmsbalance'
        );
        $data = array_merge($this->_account_data, $init); //合并数组
        $params = http_build_query($data); //生成参数数组
        $url = $this->_apiUrl . $params;
        return $this->http($url);
    }

    /*
     * 查询发送记录接口
     * @param string begindate //发送记录日期 如2014-05-06,表示2014-5-6以后的发送记录
     * @return json {"Success":true,"Message":"","Data":[]}
     * @json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
     * */
    public function query_send_log($begindate)
    {
        $init = array(
            'action' => 'querysmssendlog',
            'sendtime' => $begindate
        );
        $data = array_merge($this->_account_data, $init); //合并数组
        $params = http_build_query($data); //生成参数数组
        $url = $this->_apiUrl . $params;
        return $this->http($url);
    }


    /*
     * 查询帐户冲值记录
     * @param string begindate //充值记录日期 如2014-05-06,表示2014-5-6以后的充值记录
     * @return json {"Success":true,"Message":"","Data":[]}
     * @json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
     * */
    public function query_buy_log($begindate)
    {

        $init = array(
            'action' => 'querysmsbuylog',
            'buytime' => $begindate
        );
        $data = array_merge($this->_account_data, $init); //合并数组
        $params = http_build_query($data); //生成参数数组
        $url = $this->_apiUrl . $params;

        return $this->http($url);
    }

    public function query_send_fail_Log($begindate)
    {
        $init = array(
            'action'=>'querysmssendlog',
            'sendtime'=>$begindate,
            'sendstatus'=>0
        );
        $data = array_merge($this->_account_data,$init);//合并数组
        $params = http_build_query($data);//生成参数数组
        $url = $this->_apiUrl.$params;
        return $this->http($url);
    }

    /*
     * 查询系统参数(可购买条数等信息)
     * @return json {"Success":true,"Message":null,"Data":{"IsSMSInterfaceEnable":true,"IsBalanceNotEnough":true,"TotalSMSBalance":37961.8,"TotalSaleSMS":865916.0}}
     * */
    public function query_service_info()
    {
        $init = array(
            'action' => 'queryservicestatus'
        );
        $data = array_merge($this->_account_data, $init); //合并数组
        $params = http_build_query($data); //生成参数数组
        $url = $this->_apiUrl . $params;
        return $this->http($url);
    }


    /*
     * 接口请求函数
     * @param string url
     * @param string postfields,post请求附加字段.
     * @return $response
     * */
    private function http($url, $postfields = '', $method = 'GET')
    {
        $ci = curl_init();

        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);

        if ($method == 'POST')
        {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if ($postfields != '') curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }
}