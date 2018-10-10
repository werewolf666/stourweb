<?php
defined('SYSPATH') or die('No direct access allowed.');

class Model_Member_Third extends ORM
{
    /**
     * 所支持的第三方登陆
     * @return array
     */
    private function _supplyThird()
    {
        $data = array();
        $arr = array(
            'qq' => array(
                'alias' => 'qq',
                'name' => '腾讯QQ',
                'must' => !empty($GLOBALS['cfg_qq_appid']) && !empty($GLOBALS['cfg_qq_appkey'])
            ),
            'weixin' => array(
                'alias' => 'wx',
                'name' => '微信',
                'must' => !empty($GLOBALS['cfg_weixi_appkey']) && !empty($GLOBALS['cfg_weixi_appsecret'])
            ),
            'weibo' => array(
                'alias' => 'wb',
                'name' => '新浪微博',
                'must' => !empty($GLOBALS['cfg_sina_appkey']) && !empty($GLOBALS['cfg_sina_appsecret'])
            )
        );
        foreach ($arr as $k => $v)
        {
            if ($v['must'])
            {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    /**
     * 封装第三登陆
     * @param $third
     * @return array
     */
    public static function thirdData($third)
    {
        $supply = self::_supplyThird();
        $supplyKey = array_keys($supply);
        foreach ($third as $v)
        {
            if (array_search($v['from'], $supplyKey) !== false)
            {
                $supply[$v['from']]['id'] = $v['id'];
                $supply[$v['from']]['nickname'] = $v['nickname'];
            }
        }
        return $supply;
    }

    /**
     * 解除绑定
     * @param $id
     * @return bool
     */
    public static function unbind($id)
    {
        $bool = false;
        $rows = DB::delete('member_third')->where("id={$id}")->execute();
        if ($rows > 0)
        {
            $bool = true;
        }
        return $bool;
    }
}