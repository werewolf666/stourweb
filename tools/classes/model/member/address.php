<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 收货地址管理
 * Class Address
 */
Class Model_Member_Address extends ORM
{

    /**
     * @function 获取会员的所有收货地址
     * @param $memberid
     * @return mixed
     */
    public static function get_address($memberid)
   {
      return DB::select()->from('member_address')->where('memberid','=',$memberid)->order_by('is_default','desc')->execute()->as_array();
   }

    /**
     * @function 检查收货地址是否正确
     * @param $memberid
     * @param $address_id
     * @return int
     */
    public static function check_address_id($memberid,$address_id)
   {
       $id = DB::select()->from('member_address')
           ->where('memberid','=',$memberid)
           ->and_where('id','=',$address_id)
           ->execute()
           ->get('id');
       return $id > 0 ? $id : 0;


   }

    /**
     * @param $address_id
     * @return mixed
     */
    public static function get_address_info($address_id)
    {
        return DB::select()->from('member_address')->where('id','=',$address_id)->execute()->current();

    }


}