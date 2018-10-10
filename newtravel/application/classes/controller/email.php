<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Email extends Stourweb_Controller
{
    /*
     * 短信平台
     * */
    public function before()
    {
        require_once TOOLS_COMMON . 'email/emailservice.php';
        parent::before();

        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
        Common::getUserRight('email', 'smodify');
    }

    //短信平台首页
    public function action_index()
    {
        $configinfo = EmailService::get_email_config();
        $this->assign('config', $configinfo);
        $this->display('stourtravel/email/index');
    }

    public function action_dialog_test()
    {
        $this->display('stourtravel/email/dialog_test');
    }

    public function action_ajax_saveconfig()
    {
        $config_data = array();
        $config_data['cfg_mail_smtp'] = Common::remove_xss(Arr::get($_POST, 'cfg_mail_smtp'));
        $config_data['cfg_mail_port'] = Common::remove_xss(Arr::get($_POST, 'cfg_mail_port'));
        $config_data['cfg_mail_securetype'] = Common::remove_xss(Arr::get($_POST, 'cfg_mail_securetype'));
        $config_data['cfg_mail_user'] = Common::remove_xss(Arr::get($_POST, 'cfg_mail_user'));
        $config_data['cfg_mail_pass'] = Common::remove_xss(Arr::get($_POST, 'cfg_mail_pass'));

        EmailService::set_email_config($config_data);
        echo json_encode(array('status' => true));
    }

    public function action_ajax_sendmail()
    {
        $maillto = Arr::get($_POST, 'email');
        $title = Arr::get($_POST, 'title');
        $content = Arr::get($_POST, 'content');

        $status = EmailService::send_email($maillto, $title, $content);
        echo json_encode(array('status' => $status));
    }

}