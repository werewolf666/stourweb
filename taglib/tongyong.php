<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Taglib_Tongyong
 * 通用标签
 *
 */
class Taglib_Tongyong
{

    /**
     * 获取通用套餐列表
     * @param $params
     * @return Array
     */

    public static function suit($params)
    {
        $default = array('row' => '10', 'productid' => 0);
        $params = array_merge($default, $params);
        extract($params);
        $suit = ORM::factory('model_suit')
            ->where("productid=:productid")
            ->param(':productid', $productid)
            ->get_all();
        foreach ($suit as &$r)
        {
            $time = strtotime(date('Y-m-d'));
            $suitid = $r['id'];
            $sql = "select MIN(price) as minprice from sline_model_suit_price WHERE suitid={$suitid} and day>={$time} and number!=0";
            $result = DB::query(1, $sql)->execute()->current();
            $r['price'] = Currency_Tool::price($r['price']);
            $r['minprice'] = empty($result) ? 0 : Currency_Tool::price($result['minprice']);
            $r['title'] = $r['suitname'];
        }
        return $suit;

    }

    /**
     * 获取通用产品列表
     * @param $params
     * @return mixed
     */
    public static function query($params)
    {
        $default = array(
            'row' => '10',
            'pinyin' => '',
            'typeid' => '',
            'flag' => '',
            'offset' => 0,
            'destid' => 0
        );
        $params = array_merge($default, $params);
        extract($params);
        $tongyongModel = null;
        if (!empty($typeid))
        {
            $tongyongModel = ORM::factory('model', $typeid);
        }
        else if (empty($typeid) && !empty($pinyin))
        {
            $tongyongModel = ORM::factory('model')->where('pinyin', '=', $pinyin)->find();
        }

        if (empty($tongyongModel) || !$tongyongModel->loaded())
        {
            return null;
        }

        $modelInfo = $tongyongModel->as_array();
        $typeid = $tongyongModel->id;
        $pinyin = $tongyongModel->pinyin;

        $list = null;
        switch ($flag)
        {
            case 'new':
                $list = self::get_list_new($modelInfo, $offset, $row);
                break;
        }
        //对获取的数据进行初始化处理
        foreach ($list as &$v)
        {
            $v['price'] = Model_Tongyong::get_minprice($v['id'], array('info' => $v));
            //$v['price'] = Currency_Tool::price($v['price']);
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'], $typeid); //评论次数
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], $typeid) + intval($v['bookcount']); //销售数量
            $v['sellprice'] = Model_Tongyong::get_min_sellprice($v['id']);
            // $v['sellprice'] = Currency_Tool::price($v['sellprice']);
            $v['attrlist'] = Model_Hotel_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/" . $pinyin . "/show_{$v['aid']}.html";
        }
        return $list;
    }

    /**
     * 获取最新的列表
     * @param $typeid
     * @param $offset
     * @param $row
     */
    public static function get_list_new($modelInfo, $offset, $row)
    {
        $typeid = $modelInfo['id'];
        $addtable = $modelInfo['addtable'];
        $addtable = 'sline_' . $addtable;

        $w = " where a.ishidden=0 and a.typeid={$typeid} ";
        $sql = "SELECT a.* FROM sline_model_archive as a " .
            " LEFT JOIN {$addtable} b on (a.id=b.productid) " .
            " LEFT JOIN sline_allorderlist c on (a.aid=c.aid and c.typeid={$typeid})" .
            $w .
            " ORDER BY ifnull(c.displayorder,9999) asc,a.addtime desc " .
            " limit {$offset},{$row}";
        $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
        return $list;
    }


    /**
     * 执行sql
     * @param $sql
     * @return mixed
     */
    private static function execute($sql)
    {
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }


}