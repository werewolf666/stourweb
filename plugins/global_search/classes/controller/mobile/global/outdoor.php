<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 18:21
 * Desc: 线路
 */

class   Controller_Mobile_Global_Outdoor extends Stourweb_Controller
{
    private $_webid = 0;
    function before()
    {
        Model_Global_Search::check_app_install(114);
       // global $sys_webid;
      //  $this->_webid = $sys_webid;
        parent::before();

    }


    public function action_index()
    {
        $keyword = St_String::filter_mark(Common::remove_xss($_GET['keyword']));//关键词
        $pinyin = Common::remove_xss($this->request->controller());//拼音
        $typeid = DB::select('id')->from('model')->where('pinyin','=',$pinyin)->execute()->get('id');
        if(!$typeid)
        {
            $this->request->redirect('error/404');
        }
        if(!$keyword)
        {
            $this->request->redirect($GLOBALS['cfg_basehost'],301);
        }
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'pinyin'=>$pinyin,
            'keyword' => $keyword,
        );
        $searchcode =  St_String::split_keyword($keyword);//分词
        $searchmodel = Model_Global_Search::get_search_model($searchcode,$keyword,$typeid);
        $this->assign('searchmodel',$searchmodel);
        $this->assign('typeid',$typeid);
        $this->assign('params',$route_array);
        $tempalate = '../mobile/search/outdoor';
        $this->display($tempalate);
    }



    public function action_get_more()
    {
        $pagesize = 12 ;
        $p = intval($_GET['page']) ? intval($_GET['page']) : 1;
        $keyword = Common::remove_xss($_GET['keyword']);//关键词
        $searchcode =  St_String::split_keyword($keyword);//分词
        $pinyin = Common::remove_xss($this->request->controller());//拼音
        $typeid = DB::select('id')->from('model')->where('pinyin','=',$pinyin)->execute()->get('id');
        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'pinyin'=>$pinyin,
            'keyword' => $keyword,
            'typeid'=>$typeid
        );
        $out = Model_Global_Search::search_result($route_array, $searchcode, $p, $pagesize);
        foreach ($out['list'] as &$l)
        {
            $l['litpic'] = Common::img($l['litpic'],110,75);
            $l['addtime'] = date('Y-m-d',$l['addtime']);
        }
        if(count($out['list'])==$pagesize)
        {
            $p++ ;
        }
        else
        {
            $p = -1;
        }
        $out['page'] = $p;
        echo  json_encode($out);
    }


}