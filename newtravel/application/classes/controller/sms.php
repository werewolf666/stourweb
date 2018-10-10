<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Sms extends Stourweb_Controller{
    /*
     * 短信平台
     * */
    public function before()
    {
        require_once TOOLS_COMMON.'sms/smsservice.php';
        parent::before();

        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);
        Common::getUserRight('msg','smodify');
    }

    //短信平台首页
    public function action_index()
    {
        $providerlist = SMSService::get_all_provider();

        $this->assign('providerlist',$providerlist);

        $this->display('stourtravel/sms/sms');
    }

}