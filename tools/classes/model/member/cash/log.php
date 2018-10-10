<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 订单管理
 * Class Order
 */
Class Model_Member_Cash_Log extends ORM
{
    /**
     * @function 添加日志
     * @param $mid
     * @param $type
     * @param $amount
     * @param $description
     * @param null $params
     * @return bool
     */
    public static function add_log($mid,$type,$amount,$description,$params=null)
    {
        $model = ORM::factory('member_cash_log');
        $model->memberid = $mid;
        $model->type = $type;
        $model->amount = $amount;
        $model->description = $description;
        $model->addtime = time();
        foreach($params as $key=>$v)
        {
            $model->$key = $v;
        }
        $model->save();
        return $model->saved();
    }

    /**
     * @function 搜索日志
     * @param $mid
     * @param $page
     * @param int $pagesize
     * @param $params
     */
    public static function search_result($mid,$page,$pagesize=10,$params)
    {
        $type = $params['type'];
        $page = $page<1?1:$page;
        $pagesize=empty($pagesize)?10:$pagesize;
        $offset = ($page-1)*$pagesize;
        $w = " WHERE memberid='{$mid}' ";

        if(isset($type) && $type!=='' && $type!==false)
        {
            $w.=" AND type={$type} ";
        }

        $sql = " SELECT * FROM  sline_member_cash_log  {$w} ";
        $sql .=" ORDER BY addtime DESC LIMIT {$offset},{$pagesize}";
        $sql_num = " SELECT COUNT(*) AS num FROM sline_member_cash_log {$w} ";

        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');

        $result = array('list'=>$list,'total'=>$num,'page'=>$page,'pagesize'=>$pagesize);
        return $result;
    }

}