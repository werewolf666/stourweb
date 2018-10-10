<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Help 帮助中心
 */
class Controller_Help extends Stourweb_Controller
{
    public function before()
    {
        parent::before();
        //检查缓存
        $this->_cache_key = Common::get_current_url();
        $html = Common::cache('get', $this->_cache_key);
        $genpage = intval(Arr::get($_GET, 'genpage'));
        if (!empty($html) && empty($genpage))
        {
            echo $html;
            exit;
        }
    }
    public function action_index()
    {
        $info['kindname'] = '帮助中心';
        $this->assign('info', $info);
        $this->assign('channel',$info['kindname']);
        $this->display('help/index');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);
    }
    public function action_list()
    {
        $aid = $this->request->param('aid');
        if(empty($aid))exit;
        $info = ORM::factory('help_kind')
            ->where('id','=',$aid)
            ->and_where('webid','=',0)
            ->find()
            ->as_array();
        $this->assign('info', $info);
        $this->assign('channel',$info['kindname']);
        $this->display('help/list');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);
    }
    public function action_ajax_help_list()
    {
        $page = intval($_POST['page']);
        $kindid = intval($_POST['kindid']);
        $page = empty($page)?1:$page;
        $page_size = 10;
        $offset = ($page-1)*$page_size;
        $w = " where webid=0 ";
        if(!empty($kindid))
        {
            $w.= " and kindid={$kindid} ";
        }
        $fields = "id,webid,aid,title,addtime,modtime";
        $sql = "select {$fields} from sline_help $w order by displayorder asc,modtime desc limit {$offset},{$page_size}";
        $sql_num = "select count(*) as num from sline_help $w";
        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $total = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
        foreach($list as &$v)
        {
            $v['url'] = Common::get_web_url($v['webid']).'/help/show_'.$v['aid'].'.html';
        }
        $hasmore = $total>($offset+$page_size)?true:false;
        echo json_encode(array('list'=>$list,'hasmore'=>$hasmore));

    }
    public function action_show()
    {
        $aid = $this->request->param('aid');
        $info = ORM::factory('help')
            ->where('aid','=',$aid)
            ->and_where('webid','=',0)
            ->find()
            ->as_array();
        $this->assign('info',$info);
        $this->assign('channel',$info['title']);
        $this->display('help/show','help_show');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);
    }

}