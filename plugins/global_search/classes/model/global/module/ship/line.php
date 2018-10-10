<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 线路搜索模型
 */

class Model_Global_Module_Ship_Line extends ORM
{

    /**
     * @function 线路搜索
     * @param $params
     * @param $p
     * @param $pagesize
     * @param $keysearch
     * @param $model
     */
    public static function search_list($params,$p,$pagesize,$where,$model)
    {
        $offset = --$p*$pagesize;
        $where .=" and a.ishidden=0";
        //天数
        if ($params['dayid'])
        {
            $days = DB::select('title')->from('ship_line_day')->where('title', '=', $params['dayid'])->limit(1)->execute()->get('title');
            if (empty($days))
            {
                Common::head404();
            }
            if (Model_Line::is_last_day($days))
            {
                $where .= " AND a.lineday>='$days'";
            }
            else
            {
                $where .= " AND a.lineday='$days'";
            }
        }
        //价格区间
        if ($params['priceid'])
        {
            $priceArr = DB::select()->from('ship_line_pricelist')->where('id', '=', $params['priceid'])->execute()->current();
            if (empty($priceArr))
            {
                Common::head404();
            }
            $where .= " AND a.price BETWEEN {$priceArr['lowerprice']} AND {$priceArr['highprice']} ";
        }
        //总数
        $total = DB::select(DB::expr('count(*) as num '))
            ->from(DB::expr('sline_ship_line as a'))
            ->where($where)->execute()->get('num');
       // $files = 'a.id,a.webid,a.aid,a.title,a.storeprice,a.litpic,a.startcity,a.kindlist,a.attrid,a.bookcount,a.storeprice,a.sellpoint,a.iconlist,a.satisfyscore,a.supplierlist';
        $sql = "select a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_ship_line as a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=104) WHERE $where
        ORDER BY order_by,IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC limit $offset,$pagesize";
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['price'] = Model_Ship_Line::get_minprice($v['id'], $v);//价格
            $v['attrlist'] = Model_Ship_Line::line_attr($v['attrid']);//属性
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'], 104); //评论次数
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], 104) + intval($v['bookcount']); //销售数量
            $v['url'] = Common::get_web_url($v['webid']) . "/ship/show_{$v['aid']}.html";
            $v['litpic'] = Common::img($v['litpic'],220,136);//封面
            $v['satisfyscore']  =St_Functions::get_satisfy(104,$v['id'],$v['satisfyscore'],array('suffix'=>''));
            $v['series'] = St_Product::product_series($v['id'], 104);
            $v['suppliername'] = Model_Global_Search::get_supplier_name($v['supplierlist']);
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
        $dayid = $params['dayid'];
        $priceid = $params['priceid'];
        $startcityid = $params['startcityid'];
        $shipid = $params['shipid'];
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
            $days = DB::select('title')->from('ship_line_day')->where('id', '=', $dayid)->limit(1)->execute()->get('title');

            $temp['attrname'] = self::get_day_list_title($days);
            $temp['type'] = 'days';
            $temp['id'] = $dayid;
            $items[] = $temp;
        }

        //价格
        if($priceid)
        {
            $temp = array();

            $ar = DB::select()->from('ship_line_pricelist')->where('id', '=', $priceid)->execute()->current();
            $lowerprice = $ar['lowerprice'];
            $highprice = $ar['highprice'];
            $temp['attrname'] = Model_Global_Search::get_price_list_title($lowerprice, $highprice);
            $temp['type'] = 'price';
            $temp['id'] = $priceid;
            $items[] = $temp;
        }
        //轮船
        if($shipid)
        {
            $temp = array();
            $title = DB::select('title')->from('ship')->where('id', '=', $shipid)->limit(1)->execute()->get('title');
            $temp['attrname'] =$title;
            $temp['type'] = 'ship';
            $temp['id'] = $shipid;
            $items[] = $temp;
        }

        //出发城市
        if($startcityid)
        {
            $temp = array();
            $title = DB::select('cityname')->from('startplace')->where('id', '=', $startcityid)->limit(1)->execute()->get('cityname');
            $temp['attrname'] =$title;
            $temp['type'] = 'startcityid';
            $temp['id'] = $startcityid;
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

        $suit = ORM::factory('ship_line_day')->get_all();
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