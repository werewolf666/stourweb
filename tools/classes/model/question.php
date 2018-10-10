<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 问答管理
 * Class Question
 */
Class Model_Question extends ORM{

    ///********************** PC端开始 ******************************///
    /**
     * @function 问答搜索页面
     * @param $currentpage
     * @param $pagesize
     * @param $questype
     * @return array
     */
    public static function search_result($currentpage,$pagesize,$questype,$typeid='',$productid='')
    {
        $page = $currentpage ? $currentpage : 1;
        $offset = (intval($page)-1)*$pagesize;
        $questype = $questype ? $questype : 0;

        $sql = "SELECT a.* FROM `sline_question` a ";
        $sql.= "WHERE a.questype=$questype AND replytime!='' ";
        if(!empty($typeid))
        {
            $sql.=' AND typeid='.$typeid;
        }
        if(!empty($productid))
        {
            $sql.=' AND productid='.$productid;
        }

        $sql.= " ORDER BY replytime desc ";
        //计算总数
        $totalSql = "SELECT count(*) as dd ".strchr($sql," FROM");
        $totalSql = str_replace(strchr($totalSql,"ORDER BY"),'', $totalSql);//去掉order by

        $totalN = DB::query(1,$totalSql)->execute()->get('dd');
        $totalNum = $totalN ? $totalN : 0;
        $sql.= "LIMIT {$offset},{$pagesize}";
        $arr = self::execute($sql);
        foreach($arr as &$row)
        {
            /*$product_info = self::get_product_info($row['typeid'],$row['productid']);
            $row['productname'] = $product_info['title'];
            $row['producturl'] = $product_info['url'];*/
            $row['title'] = !empty($row['title']) ? $row['title'] : Common::cutstr_html($row['content'],20);
        }

        $out = array(
            'total' => $totalNum,
            'list' => $arr
        );
        return $out;
    }



    /**
     * @function 搜索我的问答
     * @param $mid
     * @param $currentpage
     * @param int $pagesize
     * @return array
     */
    public static function question_list($mid,$questype,$currentpage,$pagesize=10)
    {

        $page = $currentpage ? $currentpage : 1;
        $offset = (intval($page)-1)*$pagesize;
        $questype = $questype ? $questype : 0;

        $sql = "SELECT a.* FROM `sline_question` a ";
        $sql.= "WHERE a.memberid=$mid ";

        $sql.= "ORDER BY addtime desc ";

        //计算总数
        $totalSql = "SELECT count(*) as dd ".strchr($sql," FROM");
        $totalSql = str_replace(strchr($totalSql,"ORDER BY"),'', $totalSql);//去掉order by

        $totalN = DB::query(1,$totalSql)->execute()->get('dd');
        $totalNum = $totalN ? $totalN : 0;
        $sql.= "LIMIT {$offset},{$pagesize}";
        $arr = self::execute($sql);
        foreach($arr as &$row)
        {
            $product_info = self::get_product_info($row['typeid'],$row['productid']);
            $row['productname'] = $product_info['title'];
            $row['producturl'] = $product_info['url'];
        }

        $out = array(
            'total' => $totalNum,
            'list' => $arr
        );
        return $out;
    }

    /**
     * @function 获取产品信息
     * @param $typeid
     * @param $productid
     * @return array
     */
    private  static function get_product_info($typeid,$productid)
    {
        $out = array();
        if($typeid)
        {
            $model = ORM::factory('model',$typeid);

            $table = $model->maintable;
            $pinyin = !empty($model->correct) ? $model->correct : $model->pinyin;

            if($table)
            {
                $info = ORM::factory($table,$productid)->as_array();
                $url = Common::get_web_url($info['webid'])."/{$pinyin}/show_{$info['aid']}.html";
                $out['title'] = $info['title'];
                $out['url'] = $url;
            }

        }
        return $out;


    }

    /**
     * @function 执行sql (弃用)
     * @param $sql
     * @return mixed
     */
    private static function execute($sql)
    {
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }
    ///********************** PC端结束 ******************************///

    ///********************** 手机端开始  ****************************///
    /*
        * 获取酒店
        * @param 参数
        * @return array

      */
    private static $basefield ='a.id,
                            a.content,
                            a.replycontent,
                            a.replytime,
                            a.nickname,
                            a.ip,
                            a.status,
                            a.memberid,
                            a.addtime,
                            a.qq,
                            a.webid,
                            a.phone,
                            a.weixin,
                            a.email,
                            a.title,
                            a.questype';

    /**
     * @function 搜索问题
     * @param $status
     * @param $webid
     * @param $keyword
     * @param $offset
     * @param $row
     * @return mixed
     */
    public static function search_question($status, $webid, $keyword, $offset, $row)
    {
        $sql = "SELECT ".self::$basefield." FROM `sline_question` a ";
        $sql.= "WHERE 1=1 ";
        if(!empty($webid))
            $sql.= "AND a.webid={$webid} ";
        if(!empty($status))
            $sql.= "AND a.status={$status} ";
        if(!empty($keyword))
            $sql.= "AND a.content like '%{$keyword}%' ";

        $sql.= "ORDER BY replytime desc ";
        $sql.= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }

    /**
     * @function 获取问题数量
     * @param $status
     * @param $webid
     * @param $keyword
     * @param $offset
     * @param $row
     * @return mixed
     */
    public static function search_question_count($status, $webid, $keyword, $offset, $row)
    {
        $sql = "SELECT count(0) as num FROM `sline_question` a ";
        $sql.= "WHERE 1=1 ";
        if(!empty($webid))
            $sql.= "AND a.webid={$webid} ";
        if(!empty($status))
            $sql.= "AND a.status={$status} ";
        if(!empty($keyword))
            $sql.= "AND a.content like '%{$keyword}%' ";

        $sql.= "ORDER BY replytime desc ";
        $sql.= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }

    ///********************** 手机端结束  ****************************///

    ///********************** 后台开始 *******************************///
    public static $channeltable = array(
        1 => 'line',
        2 => 'hotel',
        3 => 'car',
        4 => 'article',
        5 => 'spot',
        6 => 'photo',
        8 => 'visa',
        11 => 'jieban',
        13 => 'tuan'
    );

    /**
     * @function 获取产品名称 (弃用)
     * @param $id
     * @param $typeid
     * @return string
     */
    public function getProductName($id, $typeid)
    {

        $model_info = ORM::factory('model',$typeid)->as_array();
        if(empty($model_info['id']))
        {
            return;
        }

        $field = 'title';
        $tablename = 'sline_'.$model_info['maintable'];
        $link = empty($model_info['correct'])?$model_info['pinyin']:$model_info['correct'];

        if ($typeid == 11)
        {
            $sql = "select id as aid,{$field} as title from {$tablename} where id='$id'";

        }
        else
        {
            $sql = "select aid,webid,{$field} as title from {$tablename} where id='$id'";
        }

        $row = DB::query(Database::SELECT, $sql)->execute()->current();
        $href = "/{$link}/show_{$row['aid']}.html";
        if ($row['webid'] > 0)
        {
            $destination = ORM::factory('destinations', $row['webid'])->as_array();
            $href = rtrim($destination['weburl'],'/') . $href;
        }
        $out = "<a href='{$href}' class='product-title' target=\"_blank\">{$row['title']}</a>";
        return $out;

    }

    /**
     * @function 获取问题数量
     * @param $typeid
     * @return mixed
     */
    public static function get_question_num($typeid,$articleid='')
    {
        $obj = DB::select(array(DB::expr('COUNT(`id`)'), 'total_num'))->from('question')->where('typeid','=',$typeid);
        if(!empty($articleid))
        {
            $obj->and_where('productid','=',$articleid);
            $obj->and_where('status','=',1);
        }
        $arr = $obj->execute()->current();
        return $arr['total_num'];
    }

    /**
     * @function 获取未回复数量
     * @param $typeid
     * @return mixed
     */
    public static function get_question_unans_num($typeid)
    {
        $arr = DB::select(array(DB::expr('COUNT(`id`)'), 'total_num'))
            ->from('question')
            ->where('typeid','=',$typeid)
            ->and_where('status','=',0)
            ->execute();
        return $arr[0]['total_num'];
    }

    ///********************** 后台结束 *******************************///


}