<?php defined('SYSPATH') or die('No direct script access.');

class Controller_SMS extends Stourweb_Controller
{

    private $_provider = null;
    private $_provider_instance = null;

    public function before()
    {
        require_once TOOLS_COMMON . 'sms/smsservice.php';

        $provider_id = Common::remove_xss(Arr::get($_GET, 'provider_id'));
        $this->_provider = SMSService::get_provider($provider_id);
        $this->_provider_instance = SMSService::create_provider_instance($this->_provider);

        parent::before();
    }

    public function action_index()
    {
        $this->validate_login();

        $cfg_sms_username = Common::get_sys_para('cfg_sms_username');
        $cfg_sms_password = Common::get_sys_para('cfg_sms_password');
        $this->assign('cfg_sms_username', $cfg_sms_username);
        $this->assign('cfg_sms_password', $cfg_sms_password);

        $this->assign('provider', $this->_provider);

        $out = $this->_provider_instance->query_balance();
        $out = json_decode($out);
        $balance = $out->Success ? $out->Data : "帐号不正确";
        $this->assign('balance', $balance);

        $this->display('sms/config');
    }

    public function action_ajax_saveconfig()
    {
        $this->validate_login();

        $isopen = Common::remove_xss(Arr::get($_POST, 'isopen'));
        $cfg_sms_username = Common::remove_xss(Arr::get($_POST, 'cfg_sms_username'));
        $cfg_sms_password = Common::remove_xss(Arr::get($_POST, 'cfg_sms_password'));

        $sysconfig_model = new Model_Sysconfig();
        $sysconfig_model->saveConfig(array(
            'webid' => 0,
            'cfg_sms_username' => $cfg_sms_username,
            'cfg_sms_password' => $cfg_sms_password
        ));

        if ($isopen == "1")
            SMSService::set_open_provider($this->_provider);
        else
            SMSService::set_close_provider($this->_provider);

        echo json_encode(array('status' => true));
    }

//购买短信
    public function action_buysms()
    {
        $this->validate_login();

        $cfg_sms_username = Common::get_sys_para('cfg_sms_username');
        $cfg_sms_password = Common::get_sys_para('cfg_sms_password');
        $cfg_sms_password = md5($cfg_sms_password);
        $suittype = Arr::get($_POST, 'suittype');

        $buyurl = "http://www.stourweb.com/Sms/buysms/";
        $postfields = array(
            'account' => $cfg_sms_username,
            'password' => $cfg_sms_password,
            'suittype' => $suittype
        );

        $out = Common::http($buyurl, 'POST', $postfields);
        print_r($out);
    }

    /*
     * 查询
     * */
    public function action_ajax_query()
    {
        $this->validate_login();

        $querytype = $this->params['querytype'];
        $querydate = $this->params['querydate'];
        if ($querytype == 'uselog')
        {
            $out = $this->_provider_instance->query_send_log($querydate);
        }
        if ($querytype == 'faillog')
        {
            $out = $this->_provider_instance->query_send_fail_Log($querydate);
        }
        if ($querytype == 'buylog')
        {
            $out = $this->_provider_instance->query_buy_log($querydate);
        }

        echo $out;
    }
}