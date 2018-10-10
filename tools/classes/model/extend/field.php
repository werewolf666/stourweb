<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Extend_Field extends ORM {
    /**
     * @function 添加公共扩展
     * @param $typeid
     * @param $field
     * @param $description
     * @param int $issave
     * @return bool|ORM
     * @throws Kohana_Exception
     */
    public static function add_extend_field($typeid, $field, $description, $issave = 0)
    {
        $model = ORM::factory('extend_field')->where('typeid', '=', $typeid)->and_where('fieldname', '=', $field)->find();
        if ($issave == 1 && !$model->loaded())
        {
            return false;
        }
        $model->typeid = $typeid;
        $model->fieldname = $field;
        $model->fieldtype = 'editor';
        $model->description = $description;
        $model->isopen = 1;
        $model->isunique = 0;
        return $model->save();
    }
}