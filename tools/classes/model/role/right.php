<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Role_Right extends ORM {

    /**
     * @function 设置模块权限
     * @param $roleid
     * @param $moduleid
     * @param $field
     * @param $value
     * @return bool
     * @throws Kohana_Exception
     */
    public static function set_right($roleid,$menuid,$field,$value)
    {
        $module=new self;
        $onemodule=$module->where("roleid=$roleid and menuid='$menuid'")->find();
        if($onemodule->roleid)
        {
            $query = DB::update('role_right')->set(array('right'=>$value))->where("roleid=$roleid and menuid='$menuid'");
            $result=$query->execute();
            if($result>=1)
            {
                if($value == 1)
                {
                    self::update_parent_right($menuid,$roleid);
                }
                return true;
            }

        }
        else
        {
            $onemodule=new self;
            $onemodule->roleid=$roleid;
            $onemodule->menuid=$menuid;
            $onemodule->right=$value;
            $onemodule->save();
            if($onemodule->saved())
            {
                if($value == 1)
                {
                    self::update_parent_right($menuid,$roleid);
                }
                return true;
            }

        }
        return false;

    }

    /**
     * @function 更新父级权限
     * @param $menuid
     * @param $roleid
     */
    private static function update_parent_right($menuid,$roleid)
    {
        $parent_menu_id = DB::select('pid')->from('menu_new')->where('id','=',$menuid)->execute()->get('pid');
        if($parent_menu_id != 0)
        {
            $m = ORM::factory('role_right')->where('menuid','=',$parent_menu_id)->find();
            if(!$m->loaded())
            {
                $m = new self;
            }

            $m->roleid = $roleid;
            $m->menuid = $parent_menu_id;
            $m->right= 1;
            $m->save();
            if($m->saved())
            {
                self::update_parent_right($parent_menu_id,$roleid);
            }
        }
    }
}