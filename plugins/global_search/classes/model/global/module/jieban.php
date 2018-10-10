<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/20 16:25
 * Desc: 线路搜索模型
 */

class Model_Global_Module_Jieban extends ORM
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
        $where .=" and a.status=1 and a.ishidden=0";

        //天数
        if ($params['dayid'])
        {
            $day_arr = explode('_',$params['dayid']);
            if($day_arr[1]!=0)
            {
                $where.=" AND (a.day>=$day_arr[0] AND a.day<=$day_arr[1] ) ";
            }
            else
            {
                $where.=" AND (a.day>=$day_arr[0]) ";
            }
        }

        //按出发时间搜索
        if($params['startdate'])
        {
            $startdateid = $params['startdate'];
            //当天
            if($startdateid == 2)
            {
                $time = strtotime(date('Y-m-d'));
                $where.=" AND UNIX_TIMESTAMP(a.startdate)=$time ";
            }
            //30天
            if($startdateid == 3)
            {
                $time = strtotime("+30 day");
                $where.= " AND UNIX_TIMESTAMP(a.startdate)<=$time ";

            }
            //90天
            if($startdateid == 4)
            {
                $time = strtotime("+90 day");
                $where.= " AND UNIX_TIMESTAMP(a.startdate)<=$time ";
            }
            //90天以上
            if($startdateid == 5)
            {
                $time = strtotime("+90 day");
                $where.= " AND UNIX_TIMESTAMP(a.startdate)>$time ";
            }

        }

        //总数
        $total = DB::select(DB::expr('count(*) as num '))
            ->from(DB::expr('sline_jieban as a'))
            ->where($where)->execute()->get('num');
        $sql = "select a.*,(LENGTH(REPLACE(a.title,'{$params['keyword']}',''))) as order_by from sline_jieban as a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid={$params['typeid']}) WHERE $where
        ORDER BY order_by,IFNULL(b.displayorder,9999) ASC,a.addtime DESC limit $offset,$pagesize";
        $list = DB::query(1,$sql)->execute()->as_array();

        foreach ($list as &$l)
        {
            //提取结伴图片
            $l['litpic'] = Model_Jieban::get_pic($l['memo']);
            //描述
            $l['description'] = common::cutstr_html($l['memo'],120);
            //URL地址
            $l['url'] = Common::get_web_url(0) . "/jieban/show_{$l['id']}.html";
            //活动类型
            $l['attrlist'] = Model_Jieban_Attr::get_attr_list($l['attrid']);
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
        $timeid = $params['timeid'];
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
            $temp['attrname'] = self::get_day_list_title($dayid);
            $temp['type'] = 'days';
            $temp['id'] = $dayid;
            $items[] = $temp;
        }
        //天数
        if($timeid)
        {
            $timeArr = Model_Jieban::get_time_arr();
            $temp['attrname'] = $timeArr[$timeid-1]['tagname'];
            $temp['type'] = 'startdate';
            $temp['id'] = $timeid;
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


        $day_arr = explode('_',$day);

        if($day_arr[1]==0)
        {
            return $day_arr[0].'天以上';
        }
        else
        {

            return str_replace('_','-',$day).'天';
        }

    }




}