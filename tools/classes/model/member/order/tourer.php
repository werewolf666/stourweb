<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 游客管理
 * Class Tourer
 */
class Model_Member_Order_Tourer extends ORM
{
    /**
     * @function 新增游客信息
     * @param $orderid
     * @param $arr
     * @param $memberid
     */
    public static function add_tourer_pc($orderid, $arr, $memberid)
    {
        for ($i = 0; isset($arr[$i]); $i++)
        {
            $ar = array(
                'orderid' => $orderid,
                'tourername' => $arr[$i]['name'],
                'cardtype' => $arr[$i]['cardtype'],
                'cardnumber' => $arr[$i]['cardno'],
                'mobile' => $arr[$i]['mobile'],
            );
            if (!empty($arr[$i]['sex']))
            {
                $ar['sex'] = $arr[$i]['sex'];
            }

            $m = ORM::factory('member_order_tourer');
            foreach ($ar as $k => $v)
            {
                $m->$k = $v;
            }
            $m->save();
            if ($m->saved())
            {
                Model_Member_Linkman::add_tourer_to_linkman($ar, $memberid);
            }
            $m->clear();
        }
    }

    /**
     * @function 根据订单号获取游客信息
     * @param $orderid
     * @return mixed
     */
    public static function get_tourer_by_orderid($orderid)
    {
        return DB::select()->from('member_order_tourer')->where('orderid','=',$orderid)->execute()->as_array();
    }


    ///********************* PC端开始  ***********************///

    ///********************* PC端结束  **********************///

}

