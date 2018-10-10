<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 线路搜索模型
 */

class Model_Global_Module_Notes extends ORM
{

    public static function search_list($params,$p,$pagesize,$where,$model)
    {
        $offset = --$p*$pagesize;
        $where .=" and a.status=1";


        //总数
        $total = DB::select(DB::expr('count(*) as num '))
            ->from(DB::expr('sline_notes as a'))
            ->where($where)->execute()->get('num');
        $files = 'a.*';
        $sql = "select $files,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_notes as a  WHERE {$where} ORDER BY order_by,a.modtime DESC,a.addtime DESC limit {$offset},{$pagesize}";
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['pubdate'] = Common::mydate('Y-m-d H:i', $v['modtime']);
            $v['url'] =  Common::get_web_url($v['webid']) . '/notes/show_' . $v['id'] . '.html';
            $v['litpic'] = Common::img($v['litpic'],220,136);
        }
        $out = array(
            'total' => $total,
            'list' => $list
        );
        return $out;

    }


    public static function get_search_items($params)
    {
        $destid = $params['destid'];
        $attrlist = $params['attrlist'];
        $typeid = $params['typeid'];
        $dayid = $params['dayid'];
        $priceid = $params['priceid'];
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
        //天数
        if($dayid)
        {
            $temp = array();
            $days = DB::select('word')->from('line_day')->where('id', '=', $dayid)->limit(1)->execute()->get('word');

            $temp['attrname'] = self::get_day_list_title($days);
            $temp['type'] = 'days';
            $temp['id'] = $dayid;
            $items[] = $temp;
        }

        //价格
        if($priceid)
        {
            $temp = array();
            switch ($params['pinyin'])
            {
                case 'line':
                    $ar = DB::select()->from('line_pricelist')->where('id', '=', $priceid)->execute()->current();
                    $lowerprice = $ar['lowerprice'];
                    $highprice = $ar['highprice'];
                    break;
                case 'hotel':
                    $ar = DB::select()->from('hotel_pricelist')->where('id','=',$priceid)->execute()->current();
                    $lowerprice = $ar['min'];
                    $highprice = $ar['max'];
                    break;
            }
            $temp['attrname'] = Model_Global_Search::get_price_list_title($lowerprice, $highprice);
            $temp['type'] = 'price';
            $temp['id'] = $priceid;
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

    /*
    * 出游天数格式化
    * */
    public static function get_day_list_title($day)
    {
        $title = Product::to_upper($day);

        $suit = ORM::factory('line_day')->get_all();
        if ($day < count($suit))
        {
            $title .= '日游';
        }
        else
        {
            $title .= '日游以上';
        }

        return $title;
    }




}