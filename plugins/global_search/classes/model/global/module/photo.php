<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 线路搜索模型
 */

class Model_Global_Module_Photo extends ORM
{


    public static function search_list($params,$p,$pagesize,$where,$model)
    {
        $offset = --$p*$pagesize;
        $where .=" and a.ishidden=0";
        if(!empty($destId))
        {
            $sql = "SELECT a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by FROM `sline_photo` a ";
            $sql.= "LEFT JOIN `sline_kindorderlist` b ";
            $sql.= "ON (a.id=b.aid AND b.typeid=6 AND a.webid=b.webid AND b.classid=$destId) where";
            $sql.= $where;
            $sql.= " ORDER BY order_by,IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC ";
        }
        else
        {
            $sql = "SELECT a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by FROM `sline_photo` a ";
            $sql.= "LEFT JOIN `sline_allorderlist` b ";
            $sql.= "ON (a.id=b.aid AND b.typeid=6 AND a.webid=b.webid) where ";
            $sql.= $where;
            $sql.= " ORDER BY order_by,IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC ";
        }
        //计算总数
        $total = DB::select(DB::expr('count(*) as num '))->from(DB::expr('sline_photo as a'))->where($where)->execute()->get('num');
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach($list as &$v)
        {
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'],6); //评论次数

            $v['attrlist'] = Model_Photo_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/photos/show_{$v['aid']}.html";
            $v['iconlist'] = Product::get_ico_list($v['iconlist']);
            $v['photonum'] = DB::select(array(DB::expr('count(*)'), 'total_num'))->from('photo_picture')->where('pid','=',$v['id'])->execute()->get('total_num');

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