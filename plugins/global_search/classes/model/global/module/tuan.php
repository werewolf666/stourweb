<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 线路搜索模型
 */

class Model_Global_Module_Tuan extends ORM
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
        $time = time();
        //状态
        if ($params['statusid']==1)
        {
            //未开始
            $where.=" AND a.endtime>$time AND a.endtime !='' AND a.starttime>=$time ";
        }
        else
        {
            //进行中
            $where.=" AND a.endtime !='' AND a.starttime<$time AND a.endtime>={$time} ";

        }

        //总数
        $total = DB::select(DB::expr('count(*) as num '))
            ->from(DB::expr('sline_tuan as a'))
            ->where($where)->execute()->get('num');
        $files = 'a.*';
        $sql = "select $files,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_tuan as a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=13) WHERE $where
        ORDER BY order_by,IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC limit $offset,$pagesize";
        $list = DB::query(1,$sql)->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'],13)+intval($v['virtualnum']); //销售数量
            $v['attrlist'] = Model_Tuan_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/tuan/show_{$v['aid']}.html";
            $v['iconlist'] = Product::get_ico_list($v['iconlist']);
            $v['price'] = Currency_Tool::price($v['price']);
            $v['sellprice'] = Currency_Tool::price($v['sellprice']);
            $v['discount'] =  $v['price'] == $v['sellprice'] ? '不打折' : round($v['price'] / $v['sellprice'], 2) * 10;
            $v['expire'] = ($v['starttime'] <= time() && $v['endtime'] >= time()) ? 0 : 1;
            $v['satisfyscore'] = St_Functions::get_satisfy(13,$v['id'],$v['satisfyscore'],array('suffix'=>''));
            $v['series'] = St_Product::product_series($v['id'], 13);
            if(Model_Supplier::display_is_open()&&$v['supplierlist'])
            {
                $v['suppliername'] = Arr::get(Model_Supplier::get_supplier_info($v['supplierlist'],array('suppliername')),'suppliername');
            }
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
        $statusid = $params['statusid'];

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
       if($statusid==1)
       {
            $temp = array();
            $temp['attrname'] = '未开始';
            $temp['type'] = 'status';
            $temp['id'] = 1;
            $items[] = $temp;
       }
       else
       {
           $temp = array();
           $temp['attrname'] = '进行中';
           $temp['type'] = 'status';
           $temp['id'] = 0;
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