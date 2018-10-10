<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 酒店搜索模型
 */

class Model_Global_Module_Car extends ORM
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

        //星级
        if ($params['carkind'])
        {
            $where .= " AND a.carkindid='{$params['carkind']}'";
        }

        $total_sql = "select count(*) as num from sline_car as a  WHERE $where ";
        //总数
        $total = DB::query(1,$total_sql)->execute()->get('num');
        $sql = "select a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_car as a  LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=3)  
  WHERE $where ORDER BY order_by, IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC limit $offset,$pagesize";
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], 3) + intval($v['bookcount']); //销售数量
            $v['price'] = Model_Car::get_minprice($v['id']);//最低价
            $v['attrlist'] = Model_Car_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/cars/show_{$v['aid']}.html";
            $v['kindname'] = Model_Car_Kind::get_carkindname($v['carkindid']);
            $v['iconlist'] = Product::get_ico_list($v['iconlist']);
            $v['litpic'] = Common::img($v['litpic'],200,136);
            $v['satisfyscore'] = St_Functions::get_satisfy(3,$v['id'],$v['satisfyscore'],array('suffix'=>''));
            if(Model_Supplier::display_is_open()&&$v['supplierlist'])
            {
                $v['suppliername'] = Arr::get(Model_Supplier::get_supplier_info($v['supplierlist'],array('suppliername')),'suppliername');
            }
            $v['series'] = St_Product::product_series($v['id'], 3);
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
        $destid = $params['destid'];
        $attrlist = $params['attrlist'];
        $typeid = $params['typeid'];
        $carkind = $params['carkind'];


        $items = array();
        //目的地
        if($destid)
        {
            $dest = DB::select('id','kindname')
                ->from('destinations')
                ->where('id','=',$destid)
                ->execute()->current();
            $dest['type'] = 'dest';
            $dest['attrname'] = $dest['kindname'];
            $items[] = $dest;
        }
        //车辆等级
        if($carkind)
        {
            $temp = array();
            $temp['attrname'] = Model_Car_Kind::get_carkindname($carkind);
            $temp['type'] = 'carkind';
            $temp['id'] = $carkind;
            $items[] = $temp;
        }

        //属性
        $attr_arr = array();
        $attrlist = implode(',',$attrlist);
        if($attrlist)
        {
            $attrtable = DB::select('attrtable')->from('model')->where('id','=',$typeid)->execute()->get('attrtable');
            //排除通用模块和签证模块(读取属性)
            if(!empty($attrtable) && $typeid!=8)
            {
                $m = DB::select('attrname','id')->from($attrtable);
                $m->where('isopen','=',1);
                if($attrtable == 'model_attr')
                {
                    $m->and_where('typeid','=',$typeid);
                }
                $m->and_where(DB::expr(" and id in ($attrlist)"));
                $m->order_by(DB::expr('ifnull(displayorder,9999)'),'asc');
                $attr_arr = $m->execute()->as_array();
            }
            foreach ($attr_arr as &$a)
            {
                $a['type'] = 'attr';
            }
        }
        return  array_merge($items,$attr_arr);
    }





}