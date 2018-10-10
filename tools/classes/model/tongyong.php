<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 通用模型工厂
 * @author:netman
 * */

class Model_Tongyong extends ORM
{

    ///**************************后台开始 ********************************///
    /**
     * @function 构造函数
     * @param mixed|null $tablename
     */
    public function __construct($tablename)
    {
        $this->_table_name = $tablename;
        $this->_object_name = $tablename;
        $this->_actual_model_name = 'model_' . strtolower($tablename);
        parent::_initialize();
    }

    /**
     * @function 更新最低报价
     * @param $productid
     * @throws Kohana_Exception
     */
    public static function updateMinPrice($productid)
    {
        $day = strtotime(date('Y-m-d'));
        $sql = "SELECT MIN(price) as price FROM sline_model_suit_price WHERE `productid`={$productid} and price>0 and `day`>={$day} and `number`!=0";
        $ar = DB::query(1, $sql)->execute()->current();
        $price = $ar['price'] ? $ar['price'] : 0;
        //更新最低价
        DB::update('model_archive')->set(array('price' => $price, 'price_date' => strtotime(date('Y-m-d'))))->where('id', '=', $productid)->execute();
    }

    ///**************************后台结束 **************************////

    ///**************************PC端开始 **************************///


    /**
     * @function 根据id获取产品详情
     * @param $aid
     * @param $typeid
     * @return mixed
     */
    public static function detail_id($id)
    {
        $id = intval($id);
        $sql = "SELECT * FROM `sline_model_archive` WHERE id=$id";
        $arr = DB::query(1, $sql)->execute()->as_array();
        $arr[0]['price'] = Currency_Tool::price($arr[0]['price']);
        return $arr[0];
    }

    /**
     * @function 获取扩展字段
     * @param $productid
     * @param $typeid
     * @return mixed
     */
    public static function extend($productid, $typeid)
    {
        $pinyin = DB::select('pinyin')->from('model')->where('id', '=', $typeid)->execute()->get('pinyin');
        $sql = "SELECT * FROM sline_" . $pinyin . "_extend_field ";
        $sql .= "WHERE productid={$productid}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;

    }

    /**
     * @function 获取套餐信息
     * @param $suitid
     * @return array
     */
    public static function suit_info($suitid)
    {
        $suitInfo = DB::select()->from('model_suit')->where('id', '=', $suitid)->execute()->current();
        $suitInfo['sellprice'] = Currency_Tool::price($suitInfo['sellprice']);
        $suitInfo['ourprice'] = Currency_Tool::price($suitInfo['ourprice']);
        $suitInfo['dingjin'] = Currency_Tool::price($suitInfo['dingjin']);
        return $suitInfo;
    }

    /**
     * @function 获取产品最低价
     * @param $productid
     * @param array $params
     * @return number
     */
    public static function get_minprice($productid, $params = array())
    {
        $time = strtotime(date('Y-m-d'));
        $update_minprice = false;
        if (!is_array($params))
        {
            $params = array('suitid' => $params);
        }
        if (!isset($params['suitid']))
        {
            if (!isset($params['info']))
            {
                $params['info'] = DB::select()->from('model_archive')->where('id', '=', $productid)->execute()->current();
            }
            if ($params['info']['price_date'] == $time)
            {
                return Currency_Tool::price($params['info']['price']);
            }
            //更新最低价
            $update_minprice = true;
        }
        $where = isset($params['suitid']) ? " AND suitid=" . $params['suitid'] : '';
        $sql = 'SELECT MIN(price) AS price FROM `sline_model_suit_price` WHERE productid=' . $productid . ' and price>0 and  day>=' . $time . ' and (number>0 or number=-1)' . $where;
        $row = DB::query(1, $sql)->execute()->current();
        $price = !empty($row) ? $row['price'] : 0;
        if ($update_minprice)
        {
            DB::update('model_archive')->set(array('price' => $price, 'price_date' => $time))->where('id', '=', $productid)->execute();
        }
        return Currency_Tool::price($price);
    }

    /**
     * @function 获取最低市场价(弃用)
     * @param $productid
     * @param string $suitid
     * @return int|number
     */
    public static function get_min_sellprice($productid, $suitid = '')
    {
        $where = !empty($suitid) ? " AND id=$suitid" : '';
        $sql = "SELECT MIN(sellprice) AS price FROM `sline_model_suit` WHERE productid='$productid' {$where}";
        $row = DB::query(1, $sql)->execute()->current();
        $row['price'] = Currency_Tool::price($row['price']);
        return $row['price'] ? $row['price'] : 0;
    }

    /**
     * @function 获取产品属性名称数组
     * @param $attrid
     * @return array
     */
    public static function product_attr($attrid, $typeid)
    {
        if (empty($attrid))
        {
            return;
        }
        $attrid = trim($attrid, ',');
        $attrid_arr = explode(',', $attrid);
        $arr = DB::select('attrname')->from('model_attr')->where('id', 'in', $attrid_arr)->and_where('pid', '!=', 0)->and_where('typeid', '=', $typeid)->execute()->as_array();
        return $arr;
    }


    /**
     * @function 获取目的地优化信息
     * @param $destpy
     * @param $typeid
     * @return array
     */
    public static function search_seo($destpy, $typeid)
    {
        if (!empty($destpy) && $destpy != 'all')
        {
            $destId = DB::select('id')->from('destinations')->where('pinyin', '=', $destpy)->and_where('isopen', '=', 1)->execute()->get('id');
            $info = DB::select()->from('destinations')->where('id', '=', $destId)->execute()->current();
            $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['kindname'];
        }
        else
        {
            $info = Model_Nav::get_channel_info($typeid);
            $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['shortname'];
        }
        return array('seotitle' => $seotitle);
    }

    /**
     * @function 产品搜索
     * @param $params
     * @param $keyword
     * @param $currentpage
     * @param string $pagesize
     * @return array
     */
    public static function search_result($params, $keyword, $currentpage, $pagesize = '10')
    {
        $destPy = $params['destpy'];
        $sortType = intval($params['sorttype']);
        $attrId = $params['attrid'];
        $page = $currentpage;
        $page = $page ? $page : 1;
        $typeid = intval($params['typeid']);

        $where = ' WHERE a.ishidden=0 and a.typeid=' . $typeid . ' ';
        $value_arr = array();
        //按目的地搜索
        if ($destPy && $destPy != 'all')
        {
            $destId = DB::select('id')->from('destinations')->where('pinyin', '=', $destPy)->execute()->get('id');
            $where .= " AND FIND_IN_SET('$destId',a.kindlist) ";
        }

        //排序
        $orderBy = "";
        if (!empty($sortType))
        {
            if ($sortType == 1)//价格升序
            {
                $orderBy = "  a.price DESC,";
            }
            else if ($sortType == 2) //价格降序
            {
                $orderBy = "  a.price ASC,";
            }
            else if ($sortType == 3) //销量降序
            {
                $orderBy = " a.shownum DESC,";
            }
            else if ($sortType == 4)//推荐
            {
                $orderBy = " a.shownum DESC,";
            }
        }

        //关键词
        if (!empty($keyword))
        {
            $where .= " AND a.title like :keyword ";
            $value_arr[':keyword'] = '%' . $keyword . '%';
        }
        //按属性
        if (!empty($attrId))
        {
            $where .= Product::get_attr_where($attrId);
        }

        $offset = (intval($page) - 1) * $pagesize;

        //如果选择了目的地
        if (!empty($destId))
        {
            $sql = "SELECT a.* FROM `sline_model_archive` a ";
            $sql .= "LEFT JOIN `sline_kindorderlist` b ";
            $sql .= "ON (a.id=b.aid AND b.typeid=$typeid AND a.webid=b.webid AND b.classid=$destId)";
            $sql .= $where;
            $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,{$orderBy}a.modtime DESC,a.addtime DESC ";
            //$sql.= "LIMIT {$offset},{$pagesize}";

        }
        else
        {
            $sql = "SELECT a.* FROM `sline_model_archive` a ";
            $sql .= "LEFT JOIN `sline_allorderlist` b ";
            $sql .= "ON (a.id=b.aid AND b.typeid=$typeid AND a.webid=b.webid)";
            $sql .= $where;
            $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,{$orderBy}a.modtime DESC,a.addtime DESC ";
            //$sql.= "LIMIT {$offset},{$pagesize}";


        }

        //计算总数
        $totalSql = "SELECT count(*) as dd " . strchr($sql, " FROM");
        $totalSql = str_replace(strchr($totalSql, "ORDER BY"), '', $totalSql);//去掉order by


        $totalN = DB::query(1, $totalSql)->parameters($value_arr)->execute()->as_array();
        $totalNum = $totalN[0]['dd'] ? $totalN[0]['dd'] : 0;

        $sql .= "LIMIT {$offset},{$pagesize}";

        $arr = DB::query(1, $sql)->parameters($value_arr)->execute()->as_array();

        $model_info = DB::select()->from('model')->where('id', '=', $typeid)->execute()->current();
        foreach ($arr as &$v)
        {
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'], $typeid); //评论次数
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], $typeid); //销售数量
            $v['score'] = $v['satisfyscore'] . '%';
            $v['price'] = Model_Tongyong::get_minprice($v['id'], $v);//最低价
            $v['attrlist'] = Model_Model_Attr::get_attr_list($v['attrid'], $typeid);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/{$model_info['pinyin']}/show_{$v['aid']}.html";
            $v['iconlist'] = Product::get_ico_list($v['iconlist']);
        }
        $out = array(
            'total' => $totalNum,
            'list' => $arr
        );
        return $out;


    }

    /**
     * @function 生成搜索页URL地址
     * @param $v
     * @param $paramname
     * @param $p
     * @param int $currentpage
     * @return string
     */
    public static function get_search_url($v, $paramname, $p, $currentpage = 1)
    {
        $model_info = DB::select()->from('model')->where('id', '=', $p['typeid'])->execute()->current();
        $url = $GLOBALS['cfg_basehost'] . '/' . $model_info['pinyin'] . '/';
        switch ($paramname)
        {
            case "destpy":
                $url .= $v . '-' . $p['sorttype'] . '-' . $p['attrid'] . '-' . $currentpage;
                break;

            case "sorttype":
                $url .= $p['destpy'] . '-' . $v . '-' . $p['attrid'] . '-' . $currentpage;
                break;
            case "attrid":

                $orignalArr = Product::get_attr_parent($p['attrid'], $p['typeid']);
                $nowArr = Product::get_attr_parent($v, $p['typeid']);
                if (!empty($nowArr))
                {
                    $attrArr = $nowArr + $orignalArr;
                    sort($attrArr);
                    $attr_list = join('_', $attrArr);
                }
                else
                {
                    $attr_list = 0;
                }
                $url .= $p['destpy'] . '-' . $p['sorttype'] . '-' . $attr_list . '-' . $currentpage;
                break;

        }
        return $url;


    }

    /**
     * @function 搜索页已选择项的url地址
     * @param $p
     * @return array
     */
    public static function get_selected_item($p)
    {

        $out = array();
        $model_info = DB::select()->from('model')->where('id', '=', $p['typeid'])->execute()->current();
        //目的地
        if ($p['destpy'] != 'all')
        {
            $temp = array();
            $url = self::get_search_url('all', 'destpy', $p);
            $temp['url'] = $url;
            $temp['itemname'] = DB::select('kindname')->from('destinations')->where('pinyin', '=', $p['destpy'])->execute()->get('kindname');
            $out[] = $temp;
        }


        //属性
        if ($p['attrid'] != 0)
        {
            $attArr = $orgArr = explode('_', $p['attrid']);

            foreach ($attArr as $ar)
            {

                $orgArr = $attArr;

                $temp = array();
                $temp['itemname'] = DB::select('attrname')->from('model_attr')->where('id', '=', $ar)->execute()->get('attrname');
                unset($orgArr[array_search($ar, $orgArr)]);
                if (!empty($orgArr))
                {
                    $attrid = implode('_', $orgArr);
                }
                else
                {
                    $attrid = 0;
                }
                $url = $GLOBALS['cfg_basehost'] . '/' . $model_info['pinyin'] . '/';
                $url .= $p['destpy'] . '-' . $p['sorttype'] . '-' . $attrid . '-1';
                $temp['url'] = $url;
                $out[] = $temp;
            }

        }
        return $out;

    }


    /**
     * @function 生成搜索页优化标题
     * @param $param
     * @return string
     */
    public static function gen_seotitle($param)
    {
        $main_items=array();
        $normal_items = array();
        if (!empty($param['p']))
        {
            $p = intval($param['p']);
            if ($p > 1)
            {
                $main_items[] = '第' . $p . '页';
            }
        }

        if (!empty($param['destpy']) && $param['destpy']!='all')
        {
            $destInfo = Model_Destinations::search_seo($param['destpy'], $param['typeid']);
            $normal_items[] = $destInfo['seotitle'];
        }
        if (!empty($param['keyword']))
        {
            $normal_items[] = '关于' . $param['keyword'] . '的搜索结果';
        }
        if (!empty($param['attrid']))
        {
            $normal_items[] = Model_Model_Attr::get_attrname_list($param['attrid'], '|', $param['typeid']);
        }
        $normal_items_str = implode('|',$normal_items);
        if(!empty($normal_items_str))
        {
            $main_items[]=$normal_items_str;
        }

       /* if(!empty($param['channel_name']))
        {
            $main_items[]=$param['channel_name'];
        }*/
        $main_items[] = $GLOBALS['cfg_webname'];

        $main_items_str = implode('-',$main_items);
        return $main_items_str;


    }

    /**
     * @function 获取目的地优化信息
     * @param $destid
     * @param $typeid
     * @return array
     */
    public static function get_dest_info($destid, $typeid)
    {
        $pinyin = DB::select('pinyin')->from('model')->where('id', '=', $typeid)->execute()->get('pinyin');
        $arr = array();
        if ($destid)
        {

            $sql = "SELECT a.kindname,b.seotitle,b.jieshao,b.keyword,b.tagword,b.description,a.pinyin FROM `sline_destinations` as a left join `sline_" . $pinyin . "_kindlist` AS b ON a.id=b.kindid WHERE a.id = $destid ";
        }
        $row = DB::query(1, $sql)->execute()->current();

        $arr['typename'] = $row['kindname'];
        $arr['dest_jieshao'] = $row['jieshao'];
        $arr['dest_name'] = $row['kindname'];
        $arr['kindid'] = $destid;
        $arr['dest_id'] = $destid;
        $arr['dest_pinyin'] = $row['pinyin'];
        $arr['tagword'] = $row['tagword'];
        $arr['keyword'] = !empty($row['keyword']) ? "<meta name=\"keywords\" content=\"" . $row['keyword'] . "\"/>" : "";
        $arr['description'] = !empty($arr['description']) ? "<meta name=\"description\" content=\"" . $arr['description'] . "\"/>" : "";
        $arr['pinyin'] = $row['pinyin'];
        return $arr;
    }

    /**
     * @function 获取当日报价
     * @param $suitid
     * @param $timeStamp
     * @return array
     */
    public static function current_price($suitid, $productId, $timeStamp)
    {
        $arr = DB::select()->from('model_suit_price')->where('suitid', '=', $suitid)->and_where('productid', '=', $productId)->and_where('day', '=', $timeStamp)->and_where('number', '!=', 0)->execute()->current();
        $data = !empty($arr) ? $arr : array();
        if (isset($data['price']))
        {
            $data['price'] = Currency_Tool::price($data['price']);
        }
        return $data;
    }

    /**
     * @function 获取某月套餐报价
     * @param $year
     * @param $month
     * @param $suitid
     * @param string $startdate
     * @return array
     */
    public static function get_suit_price($year, $month, $suitid, $startdate = '')
    {
        $start = !empty($startdate) ? strtotime($startdate) : strtotime("$year-$month-1");
        $end = strtotime("$year-$month-31");
        $arr = DB::select()->from('model_suit_price')
            ->where("suitid=$suitid")
            ->and_where('day', '>=', $start)
            ->and_where('day', '<=', $end)
            ->and_where('number', '!=', 0)
            ->execute()
            ->as_array();

        $price = array();
        foreach ($arr as $row)
        {
            if ($row)
            {

                $day = $row['day'];
                $price[$day]['date'] = Common::mydate('Y-m-d', $row['day']);
                $price[$day]['price'] = Currency_Tool::price($row['price']);
                $price[$day]['suitid'] = $suitid;
                $price[$day]['number'] = $row['number'];//库存
                $price[$day]['description'] = $row['description'];//描述

            }
        }
        return $price;
    }

    /**
     * @function 判断库存
     * @param $productid 产品ID
     * @param $dingnum 预订数量
     * @param string $suitid 套餐ID
     * @param string $usedate 使用日期，格式为2016-01-01
     * @param string $enddate 结束日期，格式为2016-01-01
     * @param array $extraparam 附加参数
     * @return bool
     */
    public static function check_storage($productid,$dingnum,$suitid='',$usedate='',$enddate='',$extraparam='')
    {
        if(empty($suitid) || empty($usedate))
            return false;
        $day = strtotime($usedate);
        $query = DB::select('number','suitid')->from('model_suit_price')
            ->and_where('suitid','=',$suitid)
            ->and_where('day','=',$day);
        $row = $query->execute()->current();
        $status = true;
        if(empty($row) || empty($row['suitid']))
        {
            $status = false;
        }
        else if($row['number']!='-1' && intval($row['number'])<intval($dingnum))
        {
            $status = false;
        }
        return $status;
    }
    ///*************************PC端结束 **************************///

    ///************************手机端开始 *************************///

    /**
     * @function 根据aid获取产品详情
     * @param $aid
     * @param $typeid
     * @return mixed
     */
    public static function detail($aid, $typeid, $webid = 0)
    {
        $sql = "SELECT * FROM `sline_model_archive` WHERE aid='{$aid}' AND typeid='$typeid' AND webid='$webid'";
        $arr = DB::query(1, $sql)->execute()->as_array();
        $arr[0]['price'] = Currency_Tool::price($arr[0]['price']);
        return $arr[0];
    }


    /**
     * @function 当前产品的套餐价格
     * @param $productid
     * @param $suitid
     * @param $usedate
     * @return mixed
     */
    public static function current_suit_price($productid, $suitid, $usedate)
    {
        $arr = DB::select()->from('model_suit_price')->where('productid', '=', $productid)->and_where('suitid', '=', $suitid)
            ->and_where('day', '=', strtotime($usedate))->and_where('number', '!=', 0)->execute()->current();
        $data = !empty($arr) ? $arr : array();
        if (isset($data['price']))
        {
            $data['price'] = Currency_Tool::price($data['price']);
        }
        return $data;
    }
    ///************************手机端结束 *************************///



}