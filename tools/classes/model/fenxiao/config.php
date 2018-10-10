<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Fenxiao_Config extends ORM {
    public static function get_val($varname)
    {
        $model=ORM::factory('fenxiao_config')->where('varname','=',$varname)->find();
        if(!$model->loaded())
            return null;
        else
            return $model->value;
    }
}