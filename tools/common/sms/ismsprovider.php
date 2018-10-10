<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16-6-17
 * Time: 下午4:40
 */

interface ISMSProvider {

    /*
	 * 发送短消息
	 *@param string $phone,接收手机号
	 *@param string $prefix,短信签名,如"xx旅行网",短信中显示【xx旅行网】
	 *@param string $content,短信内容.
     *@return json {"Success":false,"Message":"短信帐户余额不足，可用短信0条，需要1条","Data":null}
     *@json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
	 * */

    public function send_msg($phone,$prefix,$content);

    /*
    * @查询短信帐户余额(条数)
    * @return json {"Success":true,"Message":null,"Data":0}
    * @json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
    * */
    public function query_balance();

    /*
     * 查询发送记录接口
     * @param string begindate //发送记录日期 如2014-05-06,表示2014-5-6以后的发送记录
     * @return json {"Success":true,"Message":"","Data":[]}
     * @json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
     * */
    public function query_send_log($begindate);

    /*
     * 查询帐户冲值记录
     * @param string begindate //充值记录日期 如2014-05-06,表示2014-5-6以后的充值记录
     * @return json {"Success":true,"Message":"","Data":[]}
     * @json {"Success":执行是否成功,"Message":执行相关提示、错误等说明信息,"Data":执行返回结果数据}
     * */
    public function query_buy_log($begindate);
} 