<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Sysconfig extends ORM
{
    /*
     * 获取配置文件
     * @param int $webid 站点id
     * return array
     */
    public static function config($webid = 0)
    {
        $sql = "select varname,value from sline_sysconfig where webid={$webid}";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }
    /*
   * 根据webid获取所有配置信息
   * */
    public function get_config($webid)
    {
        $arr = $this->where('webid','=',$webid)->get_all();
        $out = array();
        foreach($arr as $row)
        {
            $out[$row['varname']] = $row['value'];
        }
        return $out;
    }
}