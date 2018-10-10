<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Sysconfig extends ORM
{

    protected $_table_name = 'sysconfig';

    /*
     * 根据webid获取所有配置信息
     * */
    public function getConfig($webid)
    {
        $arr = $this->where('webid', '=', $webid)->get_all();
        $out = array();
        foreach ($arr as $row)
        {
            $out[$row['varname']] = $row['value'];
        }
        return $out;
    }

    /*
     * 根据webid保存配置信息
     * */
    public function saveConfig($arr)
    {

        $webid = ARR::get($arr, 'webid');

        foreach ($arr as $k => $v)
        {
            /* if(!get_magic_quotes_gpc())
             {
                 $v = addslashes($v);
             }
             else
             {
                 $v = $v;
             }*/
            if ($k !== 'webid')
            {
                $row = $this->where('webid', '=', $webid)->and_where('varname', '=', $k)->find();

                if (isset($row->id)) //如果存在则修改,如果不存在则创建
                {

                    //$v = $k=='cfg_tongjicode' ? addslashes($v) : $v;
                    $row->value = $v;
                    $row->update();
                }
                else
                {
                    $row->varname = $k;
                    $row->value = $v;
                    $row->webid = $webid;
                    $row->create();
                }

                $row->clear();
            }

        }
        return true;
    }
}