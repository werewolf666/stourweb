<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Nav extends ORM
{

    /**
     * @function 删除导航
     */
    public function deleteClear()
    {
        $children=ORM::factory('nav')->where("pid={$this->id}")->find_all()->as_array();
        foreach($children as $child)
        {
            $child->deleteClear();
        }
        $this->delete();
    }

    ///**************************** PC端开始 ****************************///

    /**
     * @function 获取导航信息
     * @param $typeid
     * @return mixed
     */
    public static function get_channel_info($typeid)
    {
        global $sys_webid;

        $sql = "SELECT a.* FROM sline_nav as a WHERE a.typeid=$typeid and a.webid={$sys_webid}";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * @function 返回栏目名称
     * @param $typeid
     * @return mixed
     */
    public static function get_channel_name($typeid)
    {
        $ar = self::get_channel_info($typeid);
        $channelname = $ar['shortname'];
        return $channelname;
    }

    /**
     * @function 获取模块的优化信息
     * @param $typeid
     * @return mixed
     */
    public static function get_channel_seo($typeid)
    {
        global $sys_webid;
        $sql = "SELECT seotitle,keyword,description,shortname,url,jieshao FROM sline_nav WHERE typeid='$typeid' and webid={$sys_webid} limit 1";
        $ar = DB::query(1,$sql)->execute()->as_array();
        if(empty($ar[0]['seotitle']))
        {
           switch ($typeid) 
           {
               case '1':
                   $flag = 'line';
                   break;
               case '2':
                   $flag = 'hotel';
                   break;
               case '3':
                   $flag = 'car';
                   break;
               case '4':
                   $flag = 'article';
                   break;
               case '5':
                   $flag = 'spot';
                   break;
               case '6':
                   $flag = 'photo';
                   break;
               case '12':
                   $flag = 'dest';
                   break;
               
               default:
                   break;
           }
           $name = 'cfg_'.$flag.'_title';
           $sql1 = "SELECT value FROM sline_plugin_autotitle WHERE name = '{$name}'";
           $ar1 = DB::query(1,$sql1)->execute()->as_array();
           if(empty($ar1[0]['value']))
           {

                $ar[0]['seotitle'] = !empty($ar[0]['seotitle']) ? $ar[0]['seotitle'] : $ar[0]['shortname'];
           }
           else
           {
                $ar[0]['seotitle'] = $ar1[0]['value'];
           }
           
        }
        else
        {
            $ar[0]['seotitle'] = !empty($ar[0]['seotitle']) ? $ar[0]['seotitle'] : $ar[0]['shortname'];
        }
        return $ar[0];
    }

    /**
     * @function 获取所有的导航信息
     * @return array
     */
    public static function get_all_channel_info()
    {
        global $sys_webid;
        $out = array();
        $sql = "SELECT a.typeid,a.shortname,a.isopen,b.pinyin FROM `sline_nav` a ";
        $sql.= "LEFT JOIN `sline_model` b on(a.typeid=b.id) WHERE a.webid='$sys_webid'";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as $row)
        {
            $info = array();
            $info['isopen'] = $row['isopen'];
            $info['channelname'] = $row['shortname'];
            $out[$row['pinyin']] = $info;
        }
        return $out;

    }
    ///**************************** PC端结束 ****************************///


    ///************************* 手机端开始  ***************************///

    /**
     * @function 根据$typeid获取栏目信息
     * @param $typeid
     * @return mixed
     */
    public static function get_channel_info_mobile($typeid)
    {
        $sql = "SELECT a.*,b.m_title,b.m_isopen FROM sline_nav as a LEFT JOIN sline_m_nav b on(a.url=b.m_url and a.typeid=b.m_typeid) WHERE a.typeid=$typeid";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * @function 返回栏目名称
     * @param $typeid 栏目IDs
     * @return mixed
     */
    public static function get_channel_name_mobile($typeid)
    {
        $ar = self::get_channel_info_mobile($typeid);
        $channelname = $ar['m_title'] ? $ar['m_title'] : $ar['shortname'];
        return $channelname;
    }

    /**
     * @function 返回栏目优化标题等信息
     * @param $typeid
     * @return mixed
     */
    public static function get_channel_seo_mobile($typeid)
    {
        $sql = "SELECT seotitle,keyword,description,shortname FROM sline_nav WHERE typeid='$typeid' limit 1";
        $ar = DB::query(1,$sql)->execute()->as_array();
        $ar[0]['seotitle'] = !empty($ar[0]['seotitle']) ? $ar[0]['seotitle'] : $ar[0]['shortname'];
        return $ar[0];
    }

    /**
     * @function 获取栏目的开启状态
     * @param $typeid
     * @return int
     */
    public static function get_channel_isopen_mobile($typeid)
    {
        $sql = "SELECT m_isopen FROM sline_m_nav  WHERE m_typeid='$typeid'";
        $arr = DB::query(1,$sql)->execute()->as_array();
        $row = $arr[0];
        return $row['m_isopen'] == 1 ? 1 : 0;
    }

    /**
     * @function  获取手机版开启的系统栏目
     * @return int
     */
    public static function get_all_m_channel()
    {

        $sql = "SELECT m_typeid as typeid FROM `sline_m_nav` WHERE m_isopen=1 ";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }

    /**
     * @function 获取手机版系统栏目的开启状态与名称
     * @return array
     */
    public static function get_all_channel_info_mobile()
    {

        $out = array();
        $sql = "SELECT id,pinyin FROM `sline_model` ";
        $sql.= "WHERE issystem = 1";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as $row)
        {
            $info = array();
            $info['isopen'] = self::get_channel_isopen_mobile($row['id']);
            $info['channelname'] = self::get_channel_name_mobile($row['id']);
            $out[$row['pinyin']] = $info;
        }
        return $out;
    }
    ///************************* 手机端结束  ****************************////

	   /*
       * 保存手机导航
       * @param int webid
       * @return array
       * */
    public function save_mobile_nav($data)
    {
        $shortname = Arr::get($data,'shortname');
        $displayorder = Arr::get($data,'displayorder');
        $id = Arr::get($data,'id');
        $isopen = Arr::get($data,'isopen');
        $url = Arr::get($data,'url');

        for($i=0;isset($shortname[$i]);$i++)
        {

            $obj = ORM::factory('m_nav')->where("id='$id[$i]'")->find();
            if(!$obj->loaded())
            {
                $obj = ORM::factory('m_nav');
                $obj->navid = $id[$i];
            }
            $obj->m_title = $shortname[$i];
            $obj->m_displayorder = $displayorder[$i] ? $displayorder[$i] : 9999;
            $obj->m_isopen = $isopen[$i];
            $obj->m_url = $url[$i];
            $obj->save();
            $obj->clear();
        }

    }

}