<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 酒店搜索模型
 */

class Model_Global_Module_Hotel extends ORM
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
        $where .= " and ishidden=0";
        $date = array(
            'starttime'=>strtotime($params['starttime']),
            'endtime'=>strtotime($params['endtime']),
        );
        $extwhere = self::get_hotel_id($date);
        //星级
        if ($params['rankid'])
        {
            $where .= " AND a.hotelrankid='{$params['rankid']}'";
        }
        //价格区间
        if ($params['priceid'])
        {
            $priceArr = DB::select()->from('hotel_pricelist')->where('id','=',$params['priceid'])->execute()->current();
            if($priceArr['max'])
            {
                $where .= " AND a.price BETWEEN {$priceArr['min']} AND {$priceArr['max']} ";
            }
            else
            {
                $where .= " AND a.price > {$priceArr['min']}  ";
            }
        }
        $total_sql = "select count(*) as num from sline_hotel as a $extwhere WHERE $where ";
        //总数
        $total = DB::query(1,$total_sql)->execute()->get('num');
        $sql = "select a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_hotel as a  LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=2)  
 $extwhere WHERE $where ORDER BY order_by, IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC limit $offset,$pagesize";
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'], 2); //评论次数
            $v['satisfyscore']  =St_Functions::get_satisfy(2,$v['id'],$v['satisfyscore'],array('suffix'=>''));
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], 2) + intval($v['bookcount']); //销售数量
            $v['sellprice'] = Model_Hotel::get_sellprice($v['id']);//挂牌价
            $v['price'] = Model_Hotel::get_minprice($v['id'], array('info' => $v));//最低价
            $v['attrlist'] = Model_Hotel_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/hotels/show_{$v['aid']}.html";
            $v['litpic'] = Common::img($v['litpic']);
            $v['series'] = St_Product::product_series($v['id'], 2);
            if(Model_Supplier::display_is_open()&&$v['supplierlist'])
            {
                $v['suppliername'] = Arr::get(Model_Supplier::get_supplier_info($v['supplierlist'],array('suppliername')),'suppliername');
            }
            $v['hotelrankid'] = ORM::factory('hotel_rank', $v['hotelrankid'])->get('hotelrank');
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
        $priceid = $params['priceid'];
        $rankid = $params['rankid'];
        $starttime = $params['starttime'];
        $endtime = $params['endtime'];

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

        //开始时间
        if(strtotime($starttime))
        {
            if($params['pinyin']=='hotel')
            {
                $temp = array();
                $temp['type'] = 'time';
                $temp['id'] = 'starttime';
                $temp['attrname'] = '入住时间：'.$starttime;
                $items[] = $temp;
            }
        }
        //离开时间
        if(strtotime($endtime))
        {
            if($params['pinyin']=='hotel')
            {
                $temp = array();
                $temp['type'] = 'time';
                $temp['id'] = 'endtime';
                $temp['attrname'] = '离店时间：'.$endtime;
                $items[] = $temp;
            }
        }

        //星级
        if ($rankid)
        {
            $temp = array();
            $temp['attrname'] = DB::select('hotelrank')->from('hotel_rank')->where('id','=',$rankid)->execute()->get('hotelrank');
            $temp['type'] = 'rank';
            $temp['id'] = $rankid;
            $items[] = $temp;
        }
        //价格
        if($priceid)
        {
            $temp = array();
            $ar = DB::select()->from('hotel_pricelist')->where('id','=',$priceid)->execute()->current();
            $lowerprice = $ar['min'];
            $highprice = $ar['max'];
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




    /**
     * 通过入住时间和离店时间获取酒店id
     * @param $param
     * @return string
     */
    private static function get_hotel_id($param)
    {

        $where = '';
        if (empty($param['starttime']) && empty($param['endtime']))
        {
            return '';
        }

        if (!empty($param['starttime']) && !empty($param['endtime']) && $param['starttime']!=$param['endtime'])
        {

            $where = " inner join (select distinct hotelid from sline_hotel_room_price where day='{$param['starttime']}' or day='{$param['endtime']}' group by suitid having count(*)>1) c on a.id=c.hotelid";

        }
        else
        {
            //只有入住日期或离开日期
            $time = empty($param['starttime']) ? $param['endtime'] : $param['starttime'];
            $where = " inner join   (select distinct hotelid from sline_hotel_room_price where day='{$time}') c on a.id=c.hotelid";
        }
        return $where;
    }


}