<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Model_Content extends ORM {

    /**
     * @function 更新扩展字段描述
     */
    public static function update_extend_field_name($typeid)
    {

        $arr = DB::select_array(array('chinesename','columnname'))
            ->from('model_content')
            ->where('columnname','like','e_%')
            ->and_where('typeid','=',$typeid)
            ->execute()->as_array();
        foreach($arr as $row)
        {
            $data = array('description'=>$row['chinesename']);
            DB::update('extend_field')->set($data)->where('fieldname','=',$row['columnname'])->and_where('typeid','=',$typeid)->execute();
        }
    }

}