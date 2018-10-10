<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-10-28 上午9:50
 * Desc:订单读取标签
 */

class Taglib_Order
{

    public static function query($params)
    {
        $default=array(
            'row'=>'10',
            'flag'=>'',
            'offset'=>0,
            'typeid'=>0,
            'limit'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        //获取全部订单
        if($flag=='all')
        {
            $where = ' WHERE typeid IN ( SELECT id FROM sline_model where  id!=14) ';
        }
        else if($flag == 'line') //线路订单
        {
            $where = 'where typeid = 1';
        }
        else if($flag == 'hotel')
        {
            $where = 'where typeid = 2';
        }
        else if($flag == 'car')
        {
            $where = 'where typeid = 3';
        }
        else if($flag == 'spot')
        {
            $where = 'where typeid = 5';
        }
        else if($flag == 'visa')
        {
            $where = 'where typeid = 8';
        }
        else if($flag == 'tuan')
        {
            $where = 'where typeid = 13';
        }
		else
		{
			return array();
		}


        $sql="SELECT * FROM `sline_member_order` {$where} ORDER BY addtime DESC LIMIT $limit,$row";
        $arr = self::execute($sql);
        foreach($arr as &$row)
        {

            $memberinfo =self::get_member_info($row['memberid']);
            $row['nickname'] = $memberinfo['nickname']; //昵称
            $row['dingtime'] = Product::format_addtime($row['addtime']); //预订时间
            $productinfo = self::get_product_info($row['productautoid'],$row['typeid']);
            $row['productname'] = $productinfo['title'];
            $row['producturl'] = $productinfo['url'];

        }

        return $arr;



    }

    private static function get_product_info($productid,$typeid)
    {
        $out = array();
        if($typeid)
        {
            $sql = "SELECT * FROM sline_model WHERE id=$typeid";
            $model = DB::query(1,$sql)->execute()->current();
            $table = 'sline_'.$model['maintable'];
            $pinyin = $model['pinyin'];
            if($table)
            {

                if(Model_Model::is_standard_product($typeid))
                {
                    $fields = 'aid,webid,title';
                }
                else
                {
                    $fields = 'id as aid,title';
                }
                $s = "SELECT {$fields} FROM $table WHERE id=$productid";
                $info = DB::query(1,$s)->execute()->current();
                //$py = ($typeid>17 || $typeid==8 || $typeid==13) ? $pinyin : $pinyin.'s';
                $py = $model['correct'] ? $model['correct'] : $model['pinyin'];
                $webid = Model_Model::is_standard_product($typeid) ? $info['webid'] : 0;
                $url = Common::get_web_url($webid)."/{$py}/show_{$info['aid']}.html";
                $info['url'] = $url;
                $out = $info;
            }

        }
        return $out;
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



    private static function get_member_info($mid)
    {

        if ($mid)
        {
            $sql = "SELECT * FROM `sline_member` WHERE mid='$mid'";
            $user = self::execute($sql);
            $memberinfo = $user[0];
            $memberinfo['last_logintime'] = date('Y-m-d',$memberinfo['logintime']);
            $memberinfo['litpic'] = !empty($memberinfo['litpic']) ? $memberinfo['litpic'] : Common::member_nopic();
            return $memberinfo;
        }
    }
}