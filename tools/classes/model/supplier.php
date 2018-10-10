<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Supplier extends ORM
{
    /**
     * @function 根据ID获取供应商信息
     * @param $ids
     * @param string $fields
     * @return mixed
     */
    public static function get_supplier_info($ids,$fields=null)
    {
        $obj = DB::select_array($fields)->from('supplier');
        if (is_array($ids))
        {
            $rs = $obj->where('id', 'in', $ids)->execute()->as_array();
        }
        else
        {
            $rs = $obj->where('id', '=', $ids)->execute()->current();
        }

        return $rs;
    }


    /**
     * @function 判断后台显示开关是否开启
     */
    public static function display_is_open()
    {
        return  DB::select('value')
            ->from('sysconfig')
            ->where('varname','=','cfg_supplier_display_status')
            ->execute()->get('value');

    }

}