<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 酒店搜索模型
 */

class Model_Global_Module_Visa extends ORM
{


    /**
     * @function 酒店搜索
     * @param $params
     * @param $p
     * @param $pagesize
     * @param $where
     * @param $model
     */
    public static function search_list($params,$p,$pagesize,$where,$model)
    {

        $offset = --$p*$pagesize;
        $where .= " and a.ishidden=0";
        //分类
        if ($params['visakindid'])
        {
            $where .= " AND a.visatype='{$params['visakindid']}'";
        }
        //区域
        if ($params['visacityid'])
        {
            $where .= " AND a.cityid='{$params['visacityid']}'";
        }
        $total_sql = "select count(*) as num from sline_visa as a  WHERE $where ";
        //总数
        $total = DB::query(1,$total_sql)->execute()->get('num');
        $sql = "select a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_visa as a  LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=8)  
  WHERE $where ORDER BY order_by,IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC limit $offset,$pagesize";
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], 8) + intval($v['bookcount']); //销售数量
            $v['url'] = Common::get_web_url($v['webid']) . "/visa/show_{$v['aid']}.html";
            $v['iconlist'] = Product::get_ico_list($v['iconlist']);
            $v['visatype'] = DB::select('kindname')->from('visa_kind')->where('id', '=', $v['visatype'])->execute()->get('kindname');
            $v['price'] = Currency_Tool::price($v['price']);
            $v['marketprice'] = Currency_Tool::price($v['marketprice']);
            $v['suppliername'] = Model_Global_Search::get_supplier_name($v['supplierlist']);
            $v['series'] = St_Product::product_series($v['id'], 8);
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

        $visakindid = $params['visakindid'];
        $visacityid = $params['visacityid'];

        $items = array();

        //签证类型
        if($visacityid)
        {
            $visacity = DB::select()->from('visa_city')->where('id','=',$visacityid)->execute()->current();
            $visacity['attrname'] = $visacity['kindname'];
            $visacity['type'] = 'visacity';
            $visacity['id'] = $visacityid;
            $items[] = $visacity;

        }
        //签证城市
        if($visakindid)
        {
            $visakind = DB::select()->from('visa_kind')->where('id','=',$visacityid)->execute()->current();
            $visakind['attrname'] = $visakind['kindname'];
            $visakind['type'] = 'visakind';
            $visakind['id'] = $visakindid;
            $items[] = $visakind;
        }
        return $items;


    }



}