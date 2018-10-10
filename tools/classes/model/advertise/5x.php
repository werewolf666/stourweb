<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Advertise_5x extends ORM
{
    /**
     * 插入子站广告位
     * @param $webid
     * @throws Kohana_Exception
     */
    public static function substation($webid)
    {
        $ads = array(
            array('flag' => '2', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '1','position'=>'主导航下部通栏大图','size'=>'1920*420', 'remark' => ''),
            array('flag' => '1', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '2','position'=>'线路左侧竖型','size'=>'279*610', 'remark' => ''),
            array('flag' => '1', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '3','position'=>'线路下侧通栏','size'=>'1200*110', 'remark' => ''),
            array('flag' => '1', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '4','position'=>'酒店左侧竖型','size'=>'279*610', 'remark' => ''),
            array('flag' => '1', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '5','position'=>'酒店下侧通栏','size'=>'1200*110', 'remark' => ''),
            array('flag' => '1', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '6','position'=>'景点下侧通栏','size'=>'1200*110', 'remark' => ''),
            array('flag' => '1', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '7','position'=>'租车下侧通栏','size'=>'1200*110', 'remark' => ''),
            array('flag' => '1', 'custom_label' => '', 'kindlist' => '', 'adsrc' => 'N;', 'adlink' => 'N;', 'adname' => 'N;', 'adorder' => 'N;', 'is_system' => '1', 'is_show' => '1', 'is_pc' => '1', 'prefix' => 'index', 'number' => '8','position'=>'攻略下侧通栏','size'=>'1200*110', 'remark' => '')
        );
        foreach ($ads as $web)
        {
            $sql = "select * from `sline_advertise_5x` where webid={$webid} and `is_system`='{$web['is_system']}' and `is_pc`='{$web['is_pc']}' and `prefix`='{$web['prefix']}' and `number`={$web['number']}";
            $result = DB::query(1, $sql)->execute()->current();
            if (empty($result))
            {
                $web['webid'] = $webid;
                DB::insert('advertise_5x', array_keys($web))->values(array_values($web))->execute();
            }
        }
    }

    /**
     * 根据ID获取站点名称
     * @param int $webid
     * @return mixed
     */
    public static function site($webid = 0)
    {
        static $siteArr = null;
        if (is_null($siteArr))
        {
            $siteArr = array(0 => '主站');
            $dest = DB::select('id', 'kindname')->from('destinations')->where('iswebsite', '=', 1)->and_where('isopen', '=', 1)->execute()->as_array();
            foreach ($dest as $v)
            {
                $siteArr[$v['id']] = $v['kindname'];
            }
        }
        return $siteArr[$webid];
    }
}