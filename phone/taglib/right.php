<?php
/**
 * Created by Phpstorm.
 * User: netman
 * Date: 16-01-06
 * Time: 上午10:43
 * Desc: 右侧模块读取
 */
class Taglib_Right{

    /*
     * 获取友情链接
     * @param 参数
     * @return array

   */
    public static function get($params)
    {
        $default=array(
            'pagename'=>'index',
            'typeid'=>0,
            'data'=>array()
        );
        $params=array_merge($default,$params);
        extract($params);
        $webid = $GLOBALS['sys_webid'];//站点id.


        $sql = "SELECT moduleids FROM `sline_module_config` WHERE webid='$webid' AND shortname='$pagename' and typeid='$typeid'";

        $row = DB::query(1,$sql)->execute()->current();

        $innertext = "";
        if(is_array($row))
        {
            $mids=explode(',',$row['moduleids']);//拆分
            for($i=0;isset($mids[$i]);$i++)
            {
                $sql="SELECT body FROM `sline_module_list` WHERE aid='{$mids[$i]}' AND version=5 LIMIT 1";
                $rs = DB::query(1,$sql)->execute()->current();
                if(!empty($rs['body']))
                {
                    $innertext.=$rs['body'];
                }

            }
        }
        if($innertext=='') return '';//如里为空则退出
        else
        {
            $pname = md5($typeid.$pagename);
            echo Stourweb_View::factory(NULL,$data,$innertext,$pname);
        }

    }



} 