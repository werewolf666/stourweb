<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Stourweb_Controller
{

    private $_cache_key = '';

    public function before()
    {
        parent::before();
        //检查缓存
        $this->_cache_key = Common::get_current_url();
        $html = Common::cache('get', $this->_cache_key);
        $genpage = intval(Arr::get($_GET, 'genpage'));
        if (!empty($html) && empty($genpage) && !St_Functions::is_normal_app_install('weixinquicklogin') && !Common::is_weixin_browser()) {
            echo $html;
            exit;
        }
    }

    //首页
    public function action_index()
    {
        //seo信息
        $seoinfo = array(
            'seotitle' => $GLOBALS['cfg_indextitle'],
            'keyword' => $GLOBALS['cfg_keywords'],
            'description' => $GLOBALS['cfg_description']
        );

        //获取栏目名称与开启状态
        $channel = Model_Nav::get_all_channel_info_mobile();
        $this->assign('channel', $channel);
        $this->assign('seoinfo', $seoinfo);
        $this->display('index', 'index');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set', $this->_cache_key, $content);
    }

    //移动端子站内容
    public function action_sub_station()
    {
        $param = $this->request->param();
        $host_url = $GLOBALS['cfg_basehost'] . $GLOBALS['cfg_phone_cmspath'];
        //目的地检测
        $dest = Model_Destinations::get_dest_bypinyin($param['pinyin']);
        if (empty($dest) || Model_Model::exsits_model($param['model'])) {
            $this->request->redirect('/pub/404');
        }
        //获取内容
        $html = file_get_contents($host_url . "/{$param['model']}/show_{$param['aid']}.html?webid={$dest['id']}");
        if (empty($html)) {
            $this->request->redirect('/pub/404');
        }
        $this->response->body($html);
    }
}