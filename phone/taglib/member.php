<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-28 下午7:35
 * Desc:会员标签
 */
class Taglib_Member {

    /*
     * 获取常用联系人
     * @param 参数
     * @return array

   */
    public static function linkman($params)
    {
        $default=array('memberid'=>'');
        $params=array_merge($default,$params);
        extract($params);
        $sql="SELECT * FROM `sline_member_linkman` WHERE memberid=:memberid";
        $ar = DB::query(1,$sql)->param(':memberid',$memberid)->execute()->as_array();
        return $ar;
    }

    /**
     * 获取订单游客信息
     * @param $params
     * @return mixed
     *
     */

    public static function order_tourer($params)
    {
        $default=array('orderid'=>'');
        $params=array_merge($default,$params);
        extract($params);
        $sql="SELECT * FROM `sline_member_order_tourer` WHERE orderid=:orderid";
        $ar = DB::query(1,$sql)->param(':orderid',$orderid)->execute()->as_array();
        return $ar;
    }

    /*
     *获取订单保险
     * */
    public static function order_insurance($params)
    {
        $default=array('ordersn'=>'');
        $params=array_merge($default,$params);

        extract($params);
        $sql="SELECT * FROM `sline_insurance_booking` WHERE bookordersn=:ordersn";
        $ar = DB::query(1,$sql)->param(':ordersn',$ordersn)->execute()->as_array();

        foreach($ar as &$row)
        {
           $p = ORM::factory('insurance')->where("productcode='".$row['productcasecode']."'")->find();
           $row['productname'] = $p->productname;
           $row['productprice'] = $p->ourprice;
           $startdate=explode("-",$row['begindate']);
           $enddate=explode("-",$row['enddate']);
           $d1=mktime(0,0,0,$startdate[1],$startdate[2],$startdate[0]);
           $d2=mktime(0,0,0,$enddate[1],$enddate[2],$enddate[0]);
           $period=round(($d2-$d1)/3600/24);
           $row['period'] = $period;

        }
        return $ar;
    }

    /**
     * 订单发票信息
     * @return mixed
     */
    public static function order_bill($params)
    {
        $default=array('orderid'=>'');
        $params=array_merge($default,$params);
        extract($params);
        $sql="SELECT * FROM `sline_member_order_bill` WHERE orderid=:orderid";

        $ar = DB::query(1,$sql)->param(':orderid',$orderid)->execute()->current();


        return $ar;
    }

}