<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Admin extends ORM {

    public static function check_right($menuid)
    {
        if(empty($menuid))
            return 0;
        if(strpos($menuid,'t')!==false)
            return 1;
        $roleid = Session::instance()->get('roleid');
        if($roleid==1)
            return 1;
        $result = DB::select('right')->from('role_right')->where('roleid','=',$roleid)->and_where('menuid','=',$menuid)->execute()->current();
        return $result['right'];
    }

    public function deleteClear()
    {
        $this->delete();
    }
}