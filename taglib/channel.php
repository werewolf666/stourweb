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
    private  static $default_ico = array(
        '1'=>'menu_ico02.png',
        '2'=>'menu_ico01.png',
        '3'=>'menu_ico05.png',
        '4'=>'menu_ico09.png',
        '5'=>'menu_ico03.png',
        '6'=>'menu_ico10.png',
        '8'=>'menu_ico04.png',
        '10'=>'menu_ico12.png',
        '11'=>'menu_ico07.png',
        '12'=>'menu_ico11.png',
        '13'=>'menu_ico06.png',
        '14'=>'menu_ico08.png'

    );
    public static function getchannel($params)
    {
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

    public static function mobile($params)
    {
       /* $default = array(
            'flag' => 'top',
            'offset' => '0',
            'row' => '8'

        );
        $params = array_merge($default, $params);
        extract($params);
        $sql='select a.*,b.typeid from sline_m_nav as a left join sline_nav as b on a.navid=b.id ';
        $sql.='WHERE a.M_isopen=1 ';
        $sql .= "ORDER BY ifnull(a.m_displayorder,9999) ASC ";
        $sql .= "LIMIT {$offset},{$row}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        foreach($arr as &$row)
        {

            $row['title'] = !empty($row['m_title']) ? $row['m_title'] : $row['shortname'];
            $row['url']=preg_match('`^http://`',$row['m_url']) || preg_match('`^/phone`',$row['m_url'])?$row['m_url']:$GLOBALS['cfg_phone_cmspath'].$row['m_url'];
            $ico = !empty($row['m_ico']) ? Common::img($row['m_ico']) :(!empty(self::$default_ico[$row['typeid']]) ? $GLOBALS['cfg_public_url'].'images/'.self::$default_ico[$row['typeid']] : Common::menu_nopic()) ;
            $row['ico'] = !empty($ico) ? $ico : Common::menu_nopic();
        }
        return $arr;*/
    }

    /*
     * pc站导航
     * */

    public static function pc($params)
    {
        global $sys_webid;
        $default = array(
            'flag' => 'top',
            'offset' => '0',
            'row' => '8'
        );
        $params = array_merge($default, $params);
        extract($params);
        $sql = "SELECT * FROM sline_nav ";
        $sql.= "WHERE isopen=1 AND pid=0 AND webid='{$sys_webid}' ";
        $sql.= "ORDER BY displayorder ASC LIMIT {$offset},{$row}";

        $arr = DB::query(1, $sql)->execute()->as_array();

        foreach($arr as &$r)
        {
            $r['url'] = !empty($r['linktype'])?$GLOBALS['cfg_basehost'].$r['url']:$r['url'];
            $r['title'] = $r['shortname'];
            $r['submenu'] =DB::select()->from('nav')
                ->where('isopen','=',1)
                ->and_where('pid','=',$r['id'])
                ->and_where('webid','=',$sys_webid)
                ->order_by('displayorder','asc')
                ->execute()
                ->as_array();
            foreach($r['submenu'] as &$sub)
            {
                $sub['url'] = !empty($sub['linktype'])?$GLOBALS['cfg_basehost'].$sub['url']:$sub['url'];
                $sub['title'] = $sub['shortname'];
            }

        }


        return $arr;

    }



}