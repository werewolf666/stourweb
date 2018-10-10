<?php

/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 广告获取标签
 */
class Taglib_Ad
{

    /*
     * 获取广告
     * @param 参数
     * @return array

   */
    public static function getad($params)
    {
        global $sys_webid;//当前站点id
        $default = array(
            'name' => '',
            'destid' => '0',
            'pc' => 0

        );
        $params = array_merge($default, $params);
        extract($params);
        $custom = $name;
        $pc_where = $pc == 0 ? "AND is_pc='0'" : "AND is_pc='1'";
        $bool = preg_match('`^s_`', $name);
        $name = substr($name, 2);
        $pc_where .= $bool ? " AND is_system='1'" : " AND is_system='0'";
        $sql = "SELECT * FROM sline_advertise_5x WHERE ((CONCAT(prefix,'_',number) = '{$name}'  {$pc_where}) or custom_label='{$custom}') AND is_show='1' AND webid='$sys_webid' ";

        if (!empty($destid))
        {
            $sql .= " AND FIND_IN_SET({$destid},kindlist)";
        }
        $ar = DB::query(1, $sql)->execute()->as_array();
        if (count($ar) <= 0)
        {
            return $ar;
        }
        $ad = $ar[0];
        $ad['aditems'] = array();
        list($adsrc, $adlink, $adname, $adorder) = array(unserialize($ad['adsrc']), unserialize($ad['adlink']), unserialize($ad['adname']), unserialize($ad['adorder']));
        if (is_array($adsrc))
        {
            $adsrc = array_values($adsrc);
            $adname = array_values($adname);
            $adlink = array_values($adlink);
            $adorder = array_values($adorder);
        }
        for ($i = 0; $i < count($adsrc); $i++)
        {
            $adorder[$i] = empty($adorder[$i]) ? '9999' : $adorder[$i];
            $ad['aditems'][$i] = array('adsrc' => $adsrc[$i], 'adlink' => $adlink[$i], 'adname' => $adname[$i], 'adorder' => $adorder[$i]);
        }
        usort($ad['aditems'], array('Taglib_Ad', 'adorder'));
        //如果是单图,则直接返回单图数组即可
        $out = array();
        if ($ad['flag'] == 1 || $row == 1)
        {
            $out = $ad['aditems'][0];
        }
        else
        {
            $out = $ad;
        }
        return $out;
    }

    /**
     * 广告位排序
     * @param $a
     * @param $b
     * @return int
     */
    public function adorder($a, $b)
    {
        return $a['adorder'] > $b['adorder'] ? 1 : 0;
    }


}