<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-10-8 上午9:48
 * Desc:导航调用标签
 */
class Taglib_Channel{


    /*
    * 获取导航
    * @param 参数
    * @return array

  */
    //默认图标
    private  static $default_ico = array();

    public static function getchannel($params)
    {
        self::$default_ico = St_Functions::get_menu_default_ico();

        $default = array(
            'flag' => 'top',
            'offset' => '0',
            'row' => '8',
            'type'=> 'mobile'

        );
        $params = array_merge($default, $params);
        extract($params);
        $arr = self::mobile($params);
        return $arr;

    }
    public static function destchannel($params)
    {
        self::$default_ico = St_Functions::get_menu_default_ico();
        $default = array(
            'flag' => 'top',
            'offset' => '0',
            'row' => '8',
            'destpinyin'=>''
        );
        $params = array_merge($default, $params);
        extract($params);
        $sql = "select a.m_typeid,a.m_title,m_ico,b.pinyin,b.correct,b.maintable from sline_m_nav a inner join sline_model b on a.m_typeid=b.id where a.m_typeid>0 and a.m_isopen=1  group by a.m_typeid order by a.m_displayorder ";
        $arr = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $result=array();
        foreach($arr as $key=>$row)
        {
            if(!St_Functions::is_table_exist($row['pinyin'].'_kindlist') || $row['m_typeid']==11)
            {
                continue;
            }
            $row['title'] = $row['m_title'];
            $row['url']=empty($row['correct'])?$GLOBALS['cfg_phone_cmspath'].'/'.$row['pinyin'].'/':$GLOBALS['cfg_phone_cmspath'].'/'.$row['correct'].'/';
            if(!empty($destpinyin))
            {
                $row['url'].= $destpinyin.'/';
            }
            $ico = !empty($row['m_ico']) ? Common::img($row['m_ico']) :(!empty(self::$default_ico[$row['m_typeid']]) ? $GLOBALS['cfg_public_url'].'images/'.self::$default_ico[$row['m_typeid']] : Common::menu_nopic()) ;
            $row['ico'] = !empty($ico) ? $ico : Common::menu_nopic();
            $result[]=$row;
        }
        return $result;
    }

    public static function mobile($params)
    {
        self::$default_ico = St_Functions::get_menu_default_ico();
        $default = array(
            'flag' => 'top',
            'offset' => '0',
            'row' => '8'

        );
        $params = array_merge($default, $params);
        extract($params);
        $arr = DB::select()->from('m_nav')->where('m_isopen','=',1)->order_by('m_displayorder','ASC')->execute()->as_array();
        foreach($arr as &$row)
        {
            $row['title'] = $row['m_title'];
            $row['url']=preg_match('`^http://`',$row['m_url']) || preg_match('`^/phone`',$row['m_url'])?$row['m_url']:$GLOBALS['cfg_phone_cmspath'].$row['m_url'];
            $ico = !empty($row['m_ico']) ? Common::img($row['m_ico']) :(!empty(self::$default_ico[$row['m_typeid']]) ? $GLOBALS['cfg_public_url'].'images/'.self::$default_ico[$row['m_typeid']] : Common::menu_nopic()) ;
            $row['ico'] = !empty($ico) ? $ico : Common::menu_nopic();
        }
        return $arr;
    }

    /*
     * pc站导航
     * */

    public static function pc($params)
    {
        $default = array(
            'flag' => 'top',
            'offset' => '0',
            'row' => '8'

        );
        $params = array_merge($default, $params);
        extract($params);
        $webid = $GLOBALS['sys_child_webid'];
        $webid = empty($webid) ? 0 : $webid;

        $sql = "SELECT * FROM sline_nav ";
        $sql.= "WHERE isopen=1 AND pid=0 AND webid='$webid' ";
        $sql.= "ORDER BY displayorder ASC LIMIT {$offset},{$row}";

        $arr = DB::query(1, $sql)->execute()->as_array();

        foreach($arr as &$r)
        {
            $r['url'] = !empty($r['linktype'])?$GLOBALS['cfg_basehost'].$r['url']:$r['url'];
            $r['title'] = $r['shortname'];

        }

        return $arr;

    }



}