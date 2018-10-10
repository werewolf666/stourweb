<?php defined('SYSPATH') or die('No direct script access.');

class Model_Payset extends ORM
{
    /**
     * @function 设置开启关闭
     * @param $id
     * @param $isopen
     * @return bool
     */
    public static function set_open_status($id,$isopen)
    {
        $isopen = empty($isopen)?0:1;
        $result = DB::update('payset')->set(array('isopen'=>$isopen))->where('id','=',$id)->execute();
        return $result;
    }

    /**
     * @function 设置顺序
     * @param $id
     * @param int $displayorder
     * @return mixed
     */
    public static function set_displayorder($id,$displayorder=999)
    {
        $result = DB::update('payset')
            ->set(array('displayorder'=>$displayorder))
            ->where('id','=',$id)
            ->execute();
        return $result;
    }

    /**
     * @function 获取支付方式排序
     * @param $payid
     */
    public static function get_displayorder($payid)
    {
        return DB::select('displayorder')
            ->from('payset')
            ->where('id','=',$payid)
            ->execute()
            ->get('displayorder');
    }
	
	/**
     * @function 根据平台获取所有支付方式
     * @param int $platform
     * @return mixed
     */
	public static function get_payset_list($platform = 1)
    {
        $rows = DB::select('name','icon',array('id','payid'))
                  ->from('payset')
                  ->where(DB::expr("(platform = {$platform} OR platform = 0)"))
                  ->and_where('isopen','=',1)
				  ->group_by('icon')
                  ->having('icon','<>','')
                  ->order_by(DB::expr('IFNULL(`displayorder`,9999)'))
                  ->execute()
                  ->as_array();

        return $rows;
    }
}