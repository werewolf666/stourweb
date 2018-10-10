<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 1649513971
 * Time: 2017/10/20 16:25
 * Desc: 资讯搜索模型
 */

class Model_Global_Module_News extends ORM
{


    public static function search_list($params,$p,$pagesize,$where,$model)
    {



        $offset = --$p*$pagesize;
        $where .=" and a.ishidden=0";
        $attrlist = implode(',',$params['attrlist']);
        if($attrlist)
        {
            $where .=" and a.category_two in ($attrlist)";
        }





        //总数
        $total = DB::select(DB::expr('count(*) as num '))
            ->from(DB::expr('sline_news as a'))
            ->where($where)->execute()->get('num');

        $sql = "select a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_news as a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid={$params['typeid']}) WHERE $where
        ORDER BY order_by,IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC limit $offset,$pagesize";
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach ($list as &$v)
        {
            $attrlist = array();
//            if($v['category_one'])
//            {
//                $attrlist[]= DB::select('attrname')->from('news_attr')->where('id','=',$v['category_one'])->execute()->current();
//            }
            if($v['category_two'])
            {
                $attrlist[]= DB::select('attrname')->from('news_attr')->where('id','=',$v['category_two'])->execute()->current();
            }
            $v['url'] = Model_News::show_url($v['aid'],$v['webid']);

            $v['attrlist'] = $attrlist;
        }
        $out = array(
            'total' => $total,
            'list' => $list
        );

        return $out;

    }

    /**
     * @function 返回搜索条件
     * @param $destid 目的地
     * @param $attrlist 属性
     * @param $typeid 类型
     * @return array
     */
    public static function get_search_items($params)
    {

        $attrlist = $params['attrlist'];
        $typeid = $params['typeid'];
        $items = array();

        //属性

        if($attrlist)
        {


            $attr_arr = DB::select('attrname','id')
                ->from('news_attr')
                ->where('id','in',$attrlist)
                ->order_by(DB::expr('ifnull(displayorder,9999)'),'asc')
                ->execute()
                ->as_array();

            foreach ($attr_arr as &$a)
            {
                $a['type'] = 'attr';
            }
        }
        return  array_merge($items,$attr_arr);
    }






}