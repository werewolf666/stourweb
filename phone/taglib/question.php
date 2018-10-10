<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-10-28 上午9:50
 * Desc:问答读取标签
 */

class Taglib_Question
{



    public static function query($params)
    {
        $default=array(
            'row'=>'10',
            'flag'=>'',
            'offset'=>0,
            'typeid'=>0,
            'productid'=>0,
            'limit'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        $where = $typeid==0 ? '' : " AND typeid={$typeid}";
        $where.= !empty($productid) ? " AND productid='$productid'" : $where;

        $sql ="SELECT * FROM `sline_question` WHERE replycontent IS NOT NULL {$where} ";
        $sql.="ORDER BY replytime DESC LIMIT {$offset},{$row} ";//查询单个
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as &$r)
        {
            if($r['questype']==0)
            {
                $productinfo = self::get_product_info($r['typeid'],$r['productid']);
                $r['productname']=$productinfo['title'];
                $r['producturl']=$productinfo['url'];
            }
            else
            {
                $r['productname'] = $r['title'];
            }

            $r['nickname'] = empty($r['nickname']) ? '匿名' : $r['nickname'];
            $r['content'] = strip_tags($r['content']);
            $r['replycontent'] = strip_tags($r['replycontent']) ;


        }
        return $arr;


    }



    /**
     * 执行sql
     * @param $sql
     * @return mixed
     */
    private static function execute($sql)
    {
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * @param $typeid
     * @param $productid
     * @return mixed
     * @desc 根据typeid,产品id获取产品的具体信息
     */

    private static function get_product_info($typeid,$productid)
    {

            if($typeid)
            {
                $table = ORM::factory('model',$typeid)->get('maintable');
                $sql = "SELECT * FROM sline_$table WHERE id='$productid'";
                $ar = DB::query(1,$sql)->execute()->as_array();
                $row = $ar[0];
                if(!empty($row))
                {
                    $weburl=Common::get_web_url($row['webid']);
                    switch($typeid)
                    {
                        case 1:
                            $row['title']=$row['title'];
                            $row['url']=$weburl."/lines/show_{$row['aid']}.html";
                            break;
                        case 2:
                            $row['title']=$row['title'];
                            $row['url']=$weburl."/hotels/show_{$row['aid']}.html";
                            break;
                        case 3:
                            $row['title']=$row['title'];
                            $row['url']=$weburl."/cars/show_{$row['aid']}.html";
                            break;
                        case 4:
                            $row['title']=$row['title'];
                            $row['url']=$weburl."/raiders/show_{$row['aid']}.html";
                            break;
                        case 5:
                            $row['title']=$row['title'];
                            $row['url']=$weburl."/spots/show_{$row['aid']}.html";
                            break;
                        case 13:
                            $row['title']=$row['title'];
                            $row['url']=$weburl."/tuan/show_{$row['aid']}.html";
                            break;

                    }
                    return $row;
                }

            }






    }
}