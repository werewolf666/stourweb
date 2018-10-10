<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Tool_Link extends ORM {

    private $keywordlist = array();//关键词
    private $urllist = array();//对应url
    private $aurllist = array();//生成了<a>链接的URL.

    /**
     * @function 检测连接是否存在
     * @param $field
     * @param $value
     * @param string $id
     * @return string
     * @throws Kohana_Exception
     */
    public static function checkExist($field,$value,$id='')
    {
        $flag = 'true';
        $model = ORM::factory('tool_link');
        if(!empty($id))
        {
            $model->where('id','!=',$id);
        }
        //如果是关键词名称
        if($field=='title')
        {

           $arr = $model->get_all();
           $tarr = array();
           foreach($arr as $row)
           {
               $title = explode(',',$row['title']);
               foreach($title as $v)
               {
                   array_push($tarr,$v);
               }
           }
           if(in_array($value,$tarr))
           {
               $flag = 'false';
           }
        }
        else
        {
            $model->and_where($field,'=',$value)->find();
            if($model->loaded() && !empty($model->id))
            {
                $flag = 'false';
            }
        }
        return $flag  ;


    }

    /**
     * @function 关键词替换
     * @param $body
     * @param $typeid
     * @return mixed
     */
    public function keywordReplaceBody($body,$typeid)
    {
        $arr = $this->getKeyWordList(0);//获取关键词列表
        $fieldarr = array(
            '1'=>'linelink',
            '2'=>'hotellink',
            '3'=>'carlink',
            '4'=>'articlelink',
            '5'=>'spotlink',
            '6'=>'photolink',
            '8'=>'visalink',
            '10'=>'questionlink',
            '13'=>'tuanlink'
        );
        $updatefield = $fieldarr[$typeid];
        //格式化关键词数组便于调用.
        foreach($arr as $row)
        {
            $this->keywordlist[] = $row['title'];
            $this->urllist[] = $row['linkurl'];
        }
        $body = $this->bodyHandle($body,$updatefield);
        return $body;
    }

    /**
     * @function 处理关键词替换
     * @param $type
     * @param $channel
     * @param $offset
     * @return bool
     */
    public function keywordReplace($type,$channel,$offset)
    {
        $arr = $this->getKeyWordList($type);//获取关键词列表

        //格式化关键词数组便于调用.
        foreach($arr as $row)
        {
            $title_arr = explode(',',$row['title']);//这里拆分关键词存储到对应数组.
            foreach($title_arr as $r=>$v)
            {
                $this->keywordlist[] = $v;
                $this->urllist[] = $row['linkurl'];
            }

            //$this->aurllist[] = "<a href=\"{$row['linkurl']}\" target=\"_blank\">{$row['title']}</a>";
        }


        if(1 == $channel) //线路
        {
            $this->genLineHref(array('jieshao'),'sline_line','linelink',$offset);
        }
        if(2 == $channel) //酒店
        {
            $this->genPublic(array('content'),'sline_hotel','hotellink',$offset);
        }
        if(3 == $channel) //车辆
        {
            $this->genPublic(array('content'),'sline_car','carlink',$offset);
        }
        if(4 == $channel) //文章
        {
            $this->genPublic(array('content'),'sline_article','articlelink',$offset);
        }
        if(5 == $channel) //景点
        {
            $this->genPublic(array('content'),'sline_spot','spotlink',$offset);
        }
        if(6 == $channel) //景点
        {
            $this->genPublic(array('content'),'sline_photo','photolink',$offset);
        }
        if(8 == $channel) //签证
        {
            $this->genPublic(array('content'),'sline_visa','visalink',$offset);
        }
        if(13 == $channel) //团购
        {
            $this->genPublic(array('content'),'sline_tuan','tuanlink',$offset);
        }
        return true;

    }

    /**
     * @function 去除已有的关键词链接
     * @param $content
     * @return mixed
     */
    public function removeHref($content)
    {
        foreach($this->keywordlist as $keyword)
        {
            $content = preg_replace('/(<a([^<]*)>)('.$keyword.')(<\/a>)/sui', $keyword, $content);
        }
        return $content;
    }



    /**

     * @function 更新链接公共函数
     * @access    public
     * @para      fieldarr,要查询的字段,tablename, 表名,updatefield,统计次数的更新字段
     * @return    string
     */
    public function genPublic($fieldarr,$tablename,$updatefield,$offset)
    {

        $fields = implode(',',$fieldarr);//转化为'jieshao,content形式'

        $tablename = str_replace('sline_','sline_',$tablename);

        $sql = "SELECT id,{$fields} FROM {$tablename} where $fields !=''  order by modtime desc limit {$offset},10";

        $arr = $this->get_sql($sql,1);


        //$hasdo = 0;
        foreach($arr as $row)
        {
            $handle = array();

            //去除已有关键词链接

            foreach($fieldarr as $index => $field)
            {
                $handle[$index] = !empty($row[$field]) ? $this->removeHref($row[$field]) : '';
            }

            $tmpKwds = array(); //存放暂时被替换的子关键字
            $hasReplace = array();//已经替换过的链接
            for($k=0;isset($this->keywordlist[$k]);$k++)
            {
                $keywordUrl = trim($this->urllist[$k]);
                if(empty($keywordUrl))
                    continue;

                $keyword = $this->keywordlist[$k];
                $_num = 0;


                //匹配规则
                //$rule = "/(?!<[^>]+)".$keyword."(?![^<]*>)/";
                $rule = '~((?:<[^>]*?>)?'.$keyword.'(?:</[^>]*?>)?)~is';
                for($j=$k+1; isset($this->keywordlist[$j]); $j++)
                {
                    $subKwd = $this->keywordlist[$j];
                    //如果包含其他关键字，暂时替换成其他字符串，如 九寨沟 变成 {fcc734148321f5ad627b27585aa23958}
                    if(strpos($keyword, $subKwd) !== false)
                    {
                        $tmpKwd = '{'.md5($subKwd).'}';
                        $keyword = str_replace($subKwd, $tmpKwd, $keyword);
                        $tmpKwds[$tmpKwd] = $subKwd;
                    }
                }





                foreach($handle AS $key => $value)
                {

                    //图片不做处理
                    $img_rule="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
                    if(preg_match_all($img_rule,$value,$match))
                    {
                        foreach ($match['0'] as $img)
                        {
                            if(strpos($img,$keyword) !== false)
                            {
                                $tmpKwd = '{'.md5($img).'}';
                                $value = str_replace($img, $tmpKwd, $value);
                                $tmpKwds[$tmpKwd] = $img;
                            }
                        }
                    }


                    // $handle[$key] = str_replace($this->keywordlist[$k],'<a href="'.$this->urllist[$k].'" target="_blank">'.$keyword.'</a>',$value);//进行替换
                    if(preg_match_all($rule,$value,$match))
                    {

                        $_num = count($match[0]);
                        if($_num > 0)
                        {
                            if(!in_array($this->urllist[$k],$hasReplace))
                            {
                                $this->updateLinkNum($this->keywordlist[$k],$_num,$updatefield);//更新链接次数

                                $handle[$key] = preg_replace($rule,'<a href="'.$this->urllist[$k].'" target="_blank">'.$match[1][0].'</a>',$value,1);//进行替换
                                array_push($this->urllist[$k],$this->keywordlist[$k]);
                            }
                        }
                    }

                }
            }
            foreach($tmpKwds as $tmp=>$kwd)
            {
                foreach($handle AS $key => $value)
                {
                    $handle[$key] = preg_replace('/'.$tmp.'/', $kwd, $value);
                }

            }
            $updatesql = "UPDATE {$tablename} SET";
            foreach($fieldarr AS $index => $field)
            {
                $updatesql.= " {$field}='".addslashes($handle[$index])."',";
            }
            $updatesql = substr($updatesql,0,strlen($updatesql)-1);
            $updatesql.=" WHERE id = '{$row['id']}'";
            //Common::debug($updatesql);
            $flag = $this->get_sql($updatesql,3);

           /* if(!$this->get_sql($updatesql,3))
            {
                //echo $this->db->GetError();
                //debug($this->db->GetError());
            }*/
            //$hasdo++;
            //setcookie('hasdo',$hasdo,'/');

        }



    }

    /**
     * @function 更新线路关键词
     * @param $fieldarr
     * @param $tablename
     * @param $updatefiled
     * @param $offset
     */
    private  function genLineHref($fieldarr,$tablename,$updatefiled,$offset)
    {

        $fields = implode(',',$fieldarr);//转化为'jieshao,content形式'

        $tablename = str_replace('sline_','sline_',$tablename);

        $sql = "SELECT id,{$fields} FROM {$tablename}  order by modtime desc limit {$offset},10";

        $arr = $this->get_sql($sql,1);


        //$hasdo = 0;
        foreach($arr as $row)
        {

            $sql2= "select id,jieshao from sline_line_jieshao where lineid='{$row['id']}' and jieshao!=''";

            $ar2 = $this->get_sql($sql2,1);

            foreach($ar2  as $ar_row) //线路按天行程
            {

                   $jieshao = $this->removeHref($ar_row['jieshao']);

                    $jieshao = self::_update_jieshao_content($jieshao,$updatefiled);
                   $updatesql = "UPDATE sline_line_jieshao SET jieshao ='".addslashes($jieshao)."' WHERE id = '{$ar_row['id']}'";
                   $this->get_sql($updatesql,3);
            }
            $jieshao =  $this->removeHref($row['jieshao']);
            $jieshao = self::_update_jieshao_content($jieshao,$updatefiled);
            $updatesql = "UPDATE sline_line SET jieshao ='".addslashes($jieshao)."' WHERE id = '{$row['id']}'";
            $this->get_sql($updatesql,3);
        }

    }


    /**
     * @param $jieshao  原内容
     * @param $updatefiled 要更新的字段
     * @return mixed  新内容
     */
    private   function _update_jieshao_content($jieshao,$updatefiled)
    {


        $tmpKwds = array(); //存放暂时被替换的子关键字
        $hasReplace = array();//已经替换过的链接
        for($k=0;isset($this->keywordlist[$k]);$k++)
        {
            $keywordUrl = trim($this->urllist[$k]);
            if(empty($keywordUrl))
                continue;

            $keyword = $this->keywordlist[$k];
            $_num = 0;
            //匹配规则
            $rule = '~((?:<[^>]*?>)?'.$keyword.'(?:</[^>]*?>)?)~is';
            for($j=$k+1; isset($this->keywordlist[$j]); $j++)
            {
                $subKwd = $this->keywordlist[$j];
                //如果包含其他关键字，暂时替换成其他字符串，如 九寨沟 变成 {fcc734148321f5ad627b27585aa23958}
                if(strpos($keyword, $subKwd) !== false)
                {
                    $tmpKwd = '{'.md5($subKwd).'}';
                    $keyword = str_replace($subKwd, $tmpKwd, $keyword);
                    $tmpKwds[$tmpKwd] = $subKwd;
                }
            }
            //图片不做处理
            $img_rule="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
            if(preg_match_all($img_rule,$jieshao,$match))
            {
                foreach ($match['0'] as $img)
                {
                    if(strpos($img,$keyword) !== false)
                    {
                        $tmpKwd = '{'.md5($img).'}';
                        $jieshao = str_replace($img, $tmpKwd, $jieshao);
                        $tmpKwds[$tmpKwd] = $img;
                    }
                }
            }
            // $handle[$key] = str_replace($this->keywordlist[$k],'<a href="'.$this->urllist[$k].'" target="_blank">'.$keyword.'</a>',$value);//进行替换
            if(preg_match_all($rule,$jieshao,$match))
            {
               // var_dump($jieshao);exit;
                $_num = count($match[0]);

                if($_num > 0)
                {
                    if(!in_array($this->urllist[$k],$hasReplace))
                    {
                        $this->updateLinkNum($this->keywordlist[$k],$_num,$updatefiled);//更新链接次数
                        //$search =  "/(?!<[^>]+)".$this->keywordlist[$k]."(?![^<]*>)/";
                        $search = '~((?:<[^>]*?>)?'.$this->keywordlist[$k].'(?:</[^>]*?>)?)~is';
                        $jieshao = preg_replace($search,'<a href="'.$this->urllist[$k].'" target="_blank">'.$match[1][0].'</a>',$jieshao,1);//进行替换

                        array_push($this->urllist[$k],$this->keywordlist[$k]);
                    }
                }
            }
        }
        foreach($tmpKwds as $tmp=>$kwd)
        {
            $jieshao = preg_replace('/'.$tmp.'/', $kwd, $jieshao);

        }

        return $jieshao;


    }







    /**
     * @function 处理内容中的关键词
     * @param $body
     * @param $updatefield
     * @return mixed
     */
    public function bodyHandle($body,$updatefield)
    {
        $tmpKwds = array(); //存放暂时被替换的子关键字
        $body = $this->removeHref($body);

        $hasReplace = array();//已经替换过的链接
        for($k=0;isset($this->keywordlist[$k]);$k++)
        {

            $keyword = $this->keywordlist[$k];
            $_num = 0;


            //匹配规则
            //$rule="/".$keyword."/";
           // $rule = "/(?!<[^>]+)".$keyword."(?![^<]*>)/";
            $rule = '~((?:<[^>]*?>)?'.$keyword.'(?:</[^>]*?>)?)~is';
            for($j=$k+1; isset($this->keywordlist[$j]); $j++)
            {
                $subKwd = $this->keywordlist[$j];
                //如果包含其他关键字，暂时替换成其他字符串，如 九寨沟 变成 {fcc734148321f5ad627b27585aa23958}
                if(strpos($keyword, $subKwd) !== false)
                {
                    $tmpKwd = '{'.md5($subKwd).'}';
                    $keyword = str_replace($subKwd, $tmpKwd, $keyword);
                    $tmpKwds[$tmpKwd] = $subKwd;
                }
            }


            //图片不做处理
            $img_rule="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
            if(preg_match_all($img_rule,$body,$match))
            {
                foreach ($match['0'] as $img)
                {
                    if(strpos($img,$keyword) !== false)
                    {
                        $tmpKwd = '{'.md5($img).'}';
                        $body = str_replace($img, $tmpKwd, $body);
                        $tmpKwds[$tmpKwd] = $img;
                    }
                }
            }


            if(preg_match_all($rule,$body,$match))
            {


                $_num = count($match[0]);

                if($_num > 0)
                {
                    if(!in_array($this->urllist[$k],$hasReplace))
                    {
                        $this->updateLinkNum($this->keywordlist[$k],1,$updatefield);//更新链接次数
                        $body = preg_replace($rule,'<a href="'.$this->urllist[$k].'" target="_blank">'.$match[1][0].'</a>',$body,1);//进行替换
                        array_push($hasReplace,$this->urllist[$k]);

                    }

                }
            }


        }
        foreach($tmpKwds as $tmp=>$kwd)
        {

            $body = preg_replace('/'.$tmp.'/', $kwd, $body,1);

        }
        return $body;

    }

    /**
     * @function 获取产品总数量
     * @param $channel
     * @return int
     */
    public function getTotalNum($channel)
    {
        $table = array(
            "1"=>'sline_line',
            '2'=>'sline_hotel',
            '3'=>'sline_car',
            '4'=>'sline_article',
            '5'=>'sline_spot',
            '6'=>'sline_photo',
            '8'=>'sline_visa',
            '10'=>'sline_leave',
            '13'=>'sline_tuan'
        );
        $tablename =str_replace('sline_','sline_',$table[$channel]) ;


        $sql = "SELECT count(*) AS num FROM {$tablename} limit 1";

        $row = $this->get_sql($sql,1);

        return !empty($row[0]['num']) ? $row[0]['num'] : 0;

    }


    /**
     * @function 更新连接数量
     * @param $keyword
     * @param $num
     * @param $field
     */
    public function updateLinkNum($keyword,$num,$field)
    {

        $sql = "UPDATE sline_tool_link SET {$field} = {$field}+{$num} WHERE title = '$keyword'";

        $this->get_sql($sql,3);

    }

    /**
     * @function 获取关键词数组
     * @param string $type
     * @return array|object
     */
    public function getKeyWordList($type='')
    {
        $w = !empty($type) ? "WHERE type = '$type'" : '';
        $sql = "SELECT * FROM sline_tool_link {$w} order by length(title) desc";
        $arr = $this->get_sql($sql,1);
        foreach($arr as &$row)
        {
            $keyword = preg_replace('/[^\x7f-\xff]/','',$row['title']);
            $row['title'] = $keyword;
        }
        return $arr;
    }

    /**
     * @function 清空关键词连接次数
     * @param $channel
     */
    public function clearKeywordLink($channel)
    {
        $fieldarr = array(
            '1'=>'linelink',
            '2'=>'hotellink',
            '3'=>'carlink',
            '4'=>'articlelink',
            '5'=>'spotlink',
            '6'=>'photolink',
            '8'=>'visalink',
            '10'=>'questionlink',
            '13'=>'tuanlink'
        );
        $field = $fieldarr[$channel];
        $sql = "UPDATE sline_tool_link SET $field = 0";
        $this->get_sql($sql,3);

    }

    /**
     * @function 根据url返回链接数量
     * @param $url
     * @return int
     */
    public function getUrlCount($url)
    {
        $sql = "SELECT count(*) as num from sline_tool_link where linkurl='$url'";
        $row = $this->get_sql($sql);
        return $row[0]['num'] ? $row[0]['num'] : 0;
    }

    /**
     * @function 根据栏目获取生成的数量
     * @param $channel
     * @param $type
     * @return int
     */
    public function getKeywordLinkNum($channel,$type)
    {

        $fieldarr = array(
            '1'=>'linelink',
            '2'=>'hotellink',
            '3'=>'carlink',
            '4'=>'articlelink',
            '5'=>'spotlink',
            '6'=>'photolink',
            '8'=>'visalink',
            '10'=>'questionlink',
            '13'=>'tuanlink'
        );
        $field = $fieldarr[$channel];
        $where = !empty($type) ? "where type='$type'" : '';
        $sql = "select sum({$field}) as num from sline_tool_link {$where}";
        $row = $this->get_sql($sql,1);
        return $row[0]['num'] ? $row[0]['num'] : 0;
    }


}