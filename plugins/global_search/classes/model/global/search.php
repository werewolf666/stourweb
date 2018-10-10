<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * Author: daisc
 * QQ: 2444329889
 * Time: 2017/10/19 16:24
 * Desc: 全局搜索模型
 */

class  Model_Global_Search extends ORM
{


    /**
     * @function 判断当前关键字是否为目的地，并返回下级目的地
     * @param $keyword 关键词
     * @param $typeid 类型id
     */
    public static function check_and_get_destinations($keyword,$typeid)
    {
        //模块是否支持目的地
        if(!self::check_model_has_destinations($typeid))
        {
            return false;
        }
        $destinations = array();

        foreach ($keyword as $key)
        {
             $sql = "select id,kindname from sline_destinations WHERE isopen=1 
and pid = (SELECT id from sline_destinations WHERE kindname like '%$key%' and isopen=1 limit 1)";
             $list  = DB::query(1,$sql)->execute()->as_array();
             if($list)
             {
                 $destinations = $list;
                 break;
             }
        }
        if(!$destinations)
        {
            $destinations = DB::select('id','kindname')
                ->from('destinations')
                ->where('pid','=',0)
                ->and_where('isopen','=',1)
                ->and_where(DB::expr(" and find_in_set($typeid,opentypeids)"))
                ->execute()->as_array();
        }
        return $destinations;
    }


    /**
     * @function 判断当前模块是否支持目的地
     * @param $typeid
     *  return bool
     */
    public static function check_model_has_destinations($typeid)
    {
        $refuse = array('115','8');
        $flag = in_array($typeid,$refuse) ? false : true;
        unset($refuse);
        return  $flag;
    }


    /**
     * @function 获取第一个搜索模块的id
     * @return mixed
     */
    public static function get_first_search_model()
    {
        $sql = "SELECT a.typeid,a.shortname,a.url FROM `sline_nav` a ";
        $sql.= "INNER JOIN `sline_model` b ON a.typeid=b.id ";
        $sql.= "WHERE a.isopen=1 AND a.webid=0 AND a.linktype=1 ";
        $sql.= "AND a.typeid not in(7,9,10,12,14) ";
        $sql.= "ORDER BY a.displayorder ASC limit 1";
        $arr = DB::query(1,$sql)->execute()->current();
        return $arr['typeid'];

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
        $rankid = $params['rankid'];
        $starttime = $params['starttime'];
        $endtime = $params['endtime'];
        $carkind = $params['carkind'];
        $visakindid = $params['visakindid'];
        $visacityid = $params['visacityid'];

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


        //天数
        if($dayid)
        {
            $temp = array();
            $temp['attrname'] = self::get_day_list_title($dayid['dayid']);
            $temp['type'] = 'days';
            $temp['id'] = $dayid;
            $items[] = $temp;
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
            $temp['attrname'] = self::get_price_list_title($lowerprice, $highprice);
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
     * @function 获取搜索结果
     * @param $params
     * @param $keyword
     * @param $p
     * @param $pagesize
     */
    public static function search_result($params,$keyword,$p,$pagesize)
    {


        self::add_search_key($params['keyword']);

        $model = Model_Model::get_module_info($params['typeid']);
        //解析关键词
        $keysearch = self::get_keyword_where($model['maintable'],$keyword,$params);
        if(!$keysearch)
        {
            header('Location:'.$GLOBALS['cfg_basehost']);exit();
        }
        //默认搜索条件
        $where = self::_get_default_where($params,$keysearch,$model['maintable']);
        //通用
        if($model['maintable']=='model_archive')
        {
            $list = Model_Global_Module_General::search_list($params,$p,$pagesize,$where,$model);
        }
        else
        {
            $model = 'Model_Global_Module_'.ucfirst($params['pinyin']);
            $list = $model::search_list($params,$p,$pagesize,$where,$model);
        }
        return $list;
    }




    /*
     * 价格格式化
     * */
    public static function get_price_list_title($lowerprice, $highprice)
    {
        if ($lowerprice != '' && $highprice != '')
        {
            $title = '&yen;' . $lowerprice . '元-' . '&yen;' . $highprice . '元';
        }
        else if ($lowerprice == '')
        {
            $title = '&yen;' . $highprice . '元以下';
        }
        else if ($highprice == '')
        {
            $title = '&yen;' . $lowerprice . '元以上';
        }
        return $title;
    }


    /**
     * @function 获取供应商名称
     * @param $supplierid
     */
    public static function get_supplier_name($supplierid)
    {
        if($supplierid)
        {

            return DB::select('suppliername')
                ->from('supplier')
                ->where('id','=',$supplierid)
                ->execute()
                ->get('suppliername');
        }


    }



    /**
     * 获取默认的搜索条件 关键词，目的地，属性
     * @function
     */
    private static function _get_default_where($params,$keysearch,$table)
    {
        $where = " a.id>0";
        //关键词
        if($keysearch)
        {
            $where .= " and ($keysearch)";
        }
        //目的地
        if($params['destid']&&self::check_column($table,'kindlist'))
        {
            $where .= " and find_in_set({$params['destid']},a.kindlist)";
        }
        //属性
        if(self::check_column($table,'attrid'))
        {
            foreach ($params['attrlist'] as $attr)
            {
                if($attr)
                {
                    $where .=" and find_in_set($attr,a.attrid)";
                }
            }
        }
        return $where;
    }



    /**
     * @function 解析关键词
     * @param $table
     * @param $keyword
     * @return string
     */
    private static function get_keyword_where($table,$keyword,$params)
    {
        $keysearch = '';
        $add_column = self::get_add_column($table);
        //关键字
        $keyword[] = $params['keyword'];
        $keyword = implode('|',$keyword);
        if($keyword)
        {
            $keysearch = " a.title REGEXP '$keyword'";
            if($add_column)
            {
                $keysearch .=" or a.$add_column REGEXP '$keyword' ";
            }
        }
        return $keysearch;
    }

    /**
     * @param $keyword
     * 添加热搜词表
     */
    public static function add_search_key($keyword)
    {
        $value_arr = array(
            ':keyword' => $keyword
        );
        $sql = "SELECT * FROM `sline_search_keyword` WHERE keyword = :keyword LIMIT 1";
        $id = DB::query(1,$sql)->parameters($value_arr)->execute()->get('id');
        if($id > 0)
        {
            $updatesql = "UPDATE `sline_search_keyword` SET keynumber = keynumber+1 WHERE id = $id";
            DB::query(Database::UPDATE,$updatesql)->execute();
        }
        else
        {
            $time = time();
            $insertsql = "INSERT INTO `sline_search_keyword`(keyword,keynumber,addtime) VALUES(:keyword,1,'$time')";
            DB::query(Database::INSERT,$insertsql)->parameters($value_arr)->execute();
        }


    }



    /**
     * @function 获取扩展搜索字段
     * @param $table
     */
    private static function get_add_column($table)
    {
        $default_column = array('sellpoint', 'description');
        foreach ($default_column as $column)
        {
            if(self::check_column($table,$column))
            {
                return $column;
            }

        }

    }



    /**
     * @function 判断字段是否存在
     * @param $tabel
     * @param $column
     */
    private static function check_column($tabel,$column)
    {
        $tabel = 'sline_'.$tabel;
        $sql = "show columns from `{$tabel}` like '{$column}'";
        $result = DB::query(1,$sql)->execute()->current();
        if (false === $result)
        {
            return false;
        }
        else
        {
            return count($result) > 0 ? true : false;
        }

    }


    /**
     * @function 判断应用是否安装
     * @param typeid
     */
    public static  function check_app_install($typeid)
    {

        if (!St_Functions::is_system_app_install($typeid))
        {
           exit('app is not install');
        }

    }


    /**
     * @function
     * @param $seachkeys 分词结果
     * @param $keyword 搜索的词
     * @param $is_mobile 是否是手机
     */
    public static function get_search_model($seachkeys,$keyword,$typeid,$is_mobile=false)
    {
        $out = array();
        $has_search = array();
        if($is_mobile)
        {
            //手机端模型
            $search_model = Model_Model::get_wap_search_model();
        }
        else
        {
            //pc端模型
            $search_model = Model_Model::get_search_model();
        }
        $out['all_search_model'] = $search_model;
        foreach ($search_model as $m)
        {
            //如果不是当前搜索的模块，那么判断是否含有搜索结果
            if($typeid!=$m['typeid'])
            {
                $model = Model_Model::get_module_info($m['typeid']);
                $keysearch = self::get_keyword_where($model['maintable'],$seachkeys,array('keyword'=>$keyword));
                $where = self::_get_default_where(array('keyword'=>$keyword),$keysearch,$model['maintable']);
                if($model['maintable']=='model_archive')
                {
                    $where .=" and a.typeid=$typeid";
                }
                if($m['typeid']==11)
                {
                    $where .= " and a.status=1";
                }
                $table = 'sline_'.$model['maintable'];
                $sql = "select a.id from $table as a WHERE  $where limit 1";
                $list = DB::query(1,$sql)->execute()->as_array();
                if($list)
                {
                    $has_search[] = $m;
                }
            }
            else
            {
                $has_search[] = $m;
            }
        }
        $out['has_search'] = $has_search;
        return $out;

    }



}