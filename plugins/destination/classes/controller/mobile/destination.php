<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Hotel
 * @desc 总控制器
 */
class Controller_Mobile_Destination extends Stourweb_Controller
{
    private $_typeid = 12;   //产品类型
    private $_cache_key = '';

    public function before()
    {
        parent::before();
        //检查缓存
        $this->_cache_key = Common::get_current_url();
        $html = Common::cache('get', $this->_cache_key);
        $genpage = intval(Arr::get($_GET, 'genpage'));
        if (!empty($html) && empty($genpage)) {
            echo $html;
            exit;
        }
        $this->host_url = $GLOBALS['cfg_basehost'] . $GLOBALS['cfg_phone_cmspath'];
        $channelname = Model_Nav::get_channel_name_mobile($this->_typeid);
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', $channelname);

    }

    /**
     * 首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo_mobile($this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->display('../mobile/destination/index', 'dest_boot');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set', $this->_cache_key, $content);
    }

    public function action_main()
    {
        //参数处理
        $destpy = Common::remove_xss($this->request->param('pinyin'));
        $destinfo = Model_Destinations::get_dest_bypinyin($destpy);
        if (empty($destinfo) || !in_array('12', explode(',', $destinfo['opentypeids']))) {
            $this->_jump_404();
        }
        $destinfo['piclist'] = Product::pic_list($destinfo['piclist']);
        $destinfo['picnum'] = count($destinfo['piclist']);
        //获取seo信息
        $seo = Model_Destinations::search_seo($destpy, 0);
        $this->assign('seoinfo', $seo);
        $this->assign('destinfo', $destinfo);
        $this->display('../mobile/destination/main', 'dest_index');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set', $this->_cache_key, $content);
    }


    /**
     * 跳转404页面
     */
    private function _jump_404()
    {
        $this->request->redirect('/pub/404');
        exit;
    }

}