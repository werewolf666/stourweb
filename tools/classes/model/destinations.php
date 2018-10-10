<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 目的地管理
 * Class Destinations
 */
class Model_Destinations extends ORM
{
    /**
     * @function  根据目的地ID获取目的地名称
     * @param $kindid_str
     * @param string $separator
     * @return string
     */
    public static function get_kindname_list($kindid_str, $separator = ',')
    {
        $dest = array();
        $rs = DB::select('kindname')->from('destinations')->where('id', 'in', explode($separator, $kindid_str))->execute()->as_array();
        foreach ($rs as $v)
        {
            $dest[] = $v['kindname'];
        }
        $dest_str = implode($separator, $dest);
        return $dest_str;
    }

    /**
     * @function 获取目的地的所有祖先目的地
     * @param $id
     * @return array|null
     */
    public static function get_parents($id)
    {
        $first_dest = ORM::factory('destinations', $id);
        if (!$first_dest->id)
        {
            return null;
        }
        $cid = $first_dest->pid;
        while (true)
        {
            $cur_dest = ORM::factory('destinations', $cid);
            if ($cur_dest->id == 0)
            {
                return null;
            }
            $new_row['id'] = $cur_dest->id;
            $new_row['kindname'] = $cur_dest->kindname;
            $new_row['pinyin'] = $cur_dest->pinyin;
            $parents[] = $new_row;
            if ($cur_dest->pid == 0)
            {
                break;
            }
            $cid = $cur_dest->pid;
        }
        return $parents;
    }

    /**
     * @function 模块目的地开关
     * @param $kindid
     * @param $typeid
     * @param $isopen
     * @return bool|ORM
     */
    public static function set_typeid_open($kindid, $typeid, $isopen)
    {
        $dest = $first_dest = ORM::factory('destinations', $kindid);
        if (!$dest->loaded())
        {
            return false;
        }
        $openTypeids = $dest->opentypeids;
        $openArr = empty($openTypeids) ? array() : explode(',', $openTypeids);
        if ($isopen)
        {
            $openArr[] = $typeid;
        }
        else
        {
            $openArr = array_diff($openArr, array($typeid));
        }
        $dest->opentypeids = implode(',', $openArr);
        return $dest->save();
    }



    ////******************* PC端开始   ****************************/////
    /**
     * @function 首页目的地展示
     * @return array
     */
    public static function  home_display()
    {
        $sql = 'select * from (select kindid,displayorder from sline_line_kindlist where isnav=1) as a left join (select id,kindname,pinyin,opentypeids from sline_destinations) as b on a.kindid=b.id where find_in_set(b.opentypeids,1) order by a.displayorder asc';
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    /**
     * @function 生成子站列表
     */
    public static function gen_web_list()
    {
        $webfile = CACHE_DIR.'v5/weblist.php';
        if (!file_exists($webfile))
        {
            $out = array();

            $arr = DB::select()->from('destinations')->where('iswebsite','=',1)->execute()->as_array();
            foreach($arr as $row)
            {
                $out[$row['webprefix']]=array(
                    'webprefix'=>$row['webprefix'],
                    'weburl'=>$row['weburl'],
                    'kindname'=>$row['kindname'],
                    'webid'=>$row['id']
                );
            }
            if(!empty($out))
            {

                $weblist = "<?php defined('SYSPATH') or die('No direct script access.');". PHP_EOL . "\$weblist= ".var_export($out,true).";";
                $fp = fopen($webfile,'wb');
                flock($fp,3);
                fwrite($fp,$weblist);
                fclose($fp);
            }

        }

    }



    /**
     * @function 按栏目读取热门目的地
     * @param int $typeid
     * @param int $offset
     * @param int $row
     * @param $destid
     * @return array
     */
    public static function get_hot_dest($typeid=0,$offset=0,$row=30,$destid)
    {
        if($typeid==0)
        {

            $m = DB::select('id','kindname','pinyin','litpic')->from('destinations');
            $m->where('isopen','=',1);
            $m->and_where('ishot','=',1);
            if($destid)
            {
                $m->and_where('pid','=',$destid);
            }
            $m->order_by(DB::expr('ifnull(displayorder,9999)'),'asc');
            $m->offset($offset);
            $m->limit($row);
            $arr = $m->execute()->as_array();

        }
        else
        {

            $pinyin = DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
            if(!empty($pinyin))
            {
                //对应目的地表
                $table = 'sline_' . $pinyin . '_kindlist';
                $destwhere = $destid ? " AND a.pid=$destid " : "";
                $sql = "SELECT a.id,a.kindname,a.pinyin FROM `sline_destinations` a LEFT JOIN ";
                $sql .= "`$table` b ON (a.id=b.kindid AND IFNULL(b.ishot,0)=1) ";
                $sql .= "WHERE FIND_IN_SET($typeid,a.opentypeids) AND IFNULL(b.ishot,0)=1 AND a.isopen=1 $destwhere";
                $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC ";
                $sql .= "LIMIT $offset,$row";
                $arr = DB::query(1,$sql)->execute()->as_array();

            }

        }
        return $arr;

    }

    /**
     * @function 获取顶级目的地
     * @param $offset
     * @param $row
     * @return mixed 顶级目的地列表
     */
    public static function get_top($offset, $row)
    {
        $arr = DB::select()->from('destinations')
            ->where('pid','=',0)
            ->and_where('isopen','=',1)
            ->order_by(DB::expr('ifnull(displayorder,9999)'),'asc')
            ->offset($offset)
            ->limit($row)
            ->execute()
            ->as_array();
        return $arr;

    }

    /**
     * @function 获取下级目的地列表
     * @param $offset 偏移量
     * @param $row  条数
     * @param $pid  父级目的地id
     * @return mixed 下级目的地列表
     */
    public static function get_next($offset, $row, $pid,$typeid=0)
    {

        if($typeid != 0)
        {
            $py =DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');;
            $table = 'sline_'.$py.'_kindlist';
            $typewhere =$typeid ? " AND FIND_IN_SET($typeid,opentypeids) " : "";
            $pid = empty($pid) ? 0 : $pid;
            $sql = "SELECT a.id,a.kindname,a.pinyin,a.iswebsite,a.weburl FROM `sline_destinations` a ";
            $sql.= "LEFT JOIN {$table} b ON a.id=b.kindid ";
            //主目的地开启,且栏目对应的目的地也应开启
            $sql.= "WHERE a.isopen=1 AND a.pid='$pid' {$typewhere}   ";
            $sql.= "ORDER BY IFNULL(b.displayorder,999) ASC ";
            $sql.= "LIMIT $offset,$row";
        }
        else
        {
            $sql = "SELECT * FROM `sline_destinations` ";
            $sql.= "WHERE pid=$pid AND isopen=1 ";
            $sql.= "ORDER BY displayorder ASC ";
            $sql.= "LIMIT {$offset},{$row}";
        }
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }

    /**
     * @function 首页导航目的地
     * @param $offset
     * @param $row
     * @return mixed 首页导航目的地
     */
    public static function get_index_nav($offset, $row)
    {
        $arr = DB::select()->from('destinations')
            ->where('isnav','=',1)
            ->and_where('isopen','=',1)
            ->order_by(DB::expr('ifnull(displayorder,9999)'),'asc')
            ->offset($offset)
            ->limit($row)
            ->execute()
            ->as_array();
        return $arr;

    }

    /**
     * @function  栏目首页导航目的地
     * @param $offset
     * @param $row
     * @param $typeid
     * @return array
     */

    public static function get_channel_nav($offset, $row, $typeid)
    {
        $pinyin =DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
        $arr = array();
        if ($pinyin)
        {
            //对应目的地表
            $table = 'sline_' . $pinyin . '_kindlist';
            $sql = "SELECT a.id,a.kindname,a.pinyin FROM `sline_destinations` a LEFT JOIN ";
            $sql .= "`$table` b ON (a.id=b.kindid) ";
            $sql .= "WHERE FIND_IN_SET($typeid,a.opentypeids) AND b.isnav=1 AND a.isopen=1 ";
            $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC ";
            $sql .= "LIMIT $offset,$row";
            $arr = DB::query(Database::SELECT, $sql)->execute()->as_array();
        }
        return $arr;
    }

    /**
     * @function 获取最后一个目的地
     * @param $kindlist
     * @return array
     */
    public static function  get_last_dest($kindlist)
    {
        $kindlistArr = explode(',', $kindlist);
        $maxdest = max($kindlistArr);
        if(empty($maxdest))
            return array();

        $sql = "SELECT	* FROM	sline_destinations WHERE id ={$maxdest}";
        $rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if(count($rows) > 0)
            return $rows[0];
        else
            return array();
    }

    /**
     * @function 通过拼音获取目的地
     * @param $destpy
     * @return array
     */
    public static function get_dest_bypinyin($destpy)
    {
        if (!empty($destpy) && $destpy != 'all')
        {

            $rows = DB::select()->from('destinations')->where('pinyin','=',$destpy)->and_where('isopen','=',1)->execute()->current();

            if (isset($rows['id']))
                return $rows;
            else
                return array();
        }
        else
        {
            return array();
        }
    }

    /**
     * @function 获取目的地优化标题
     * @param $destpy
     * @param $typeid
     * @return array
     */
    public static function search_seo($destpy, $typeid)
    {
        $file = SLINEDATA . "/autotitle.cache.inc.php"; //载入智能title配置
        if (file_exists($file))
        {
            include($file);
        }
        $result = array(
            'seotitle' => "",
            'keyword' => "",
            'description' => ""
        );

        $auto_title='';
        $auto_desc='';
        if(empty($typeid))
        {
           $auto_title = $cfg_dest_title;
           $auto_desc = $cfg_dest_desc;
        }
        else
        {
            $model_info = Model_Model::get_module_info($typeid);
            $pinyin = $model_info['pinyin'];
            $auto_title_name = 'cfg_'.$pinyin.'_title';
            $auto_desc_name = 'cfg_'.$pinyin.'_desc';
            $auto_title =$$auto_title_name;
            $auto_desc = $$auto_desc_name;
        }
        if (!empty($destpy) && $destpy != 'all')
        {
            $dest = Model_Destinations::get_dest_bypinyin($destpy);
            $destId = $dest["id"];
            if (!empty($destId))
            {
                $seotitle = "";
                //$model = ORM::factory("model", $typeid)->as_array();
                $model_pinyin = DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
                if (!empty($model_pinyin))
                {
                    $kindlist_tablename = "sline_{$model_pinyin}_kindlist";
                    $sql = "select a.kindname,b.seotitle,b.keyword,b.description FROM sline_destinations a LEFT JOIN $kindlist_tablename b ON a.id=b.kindid where b.kindid='$destId'";
                    $info = DB::query(1,$sql)->execute()->current();
                    //$info = ORM::factory($kindlist_tablename)->where("kindid", "=", $destId)->find()->as_array();
                    $seotitle = $info['seotitle'];//? $info['seotitle'] : $info['kindname'];
                    $result["seotitle"] = $seotitle;
                    $result["keyword"] = $info["keyword"];
                    $result["description"] = $info["description"];

                }
                if (empty($seotitle))
                {
                    $info = ORM::factory('destinations', $destId)->as_array();
                    //读取自动优化标题
                    if(!empty($auto_title))
                    {
                        $auto_title = str_replace('XXX', $info['kindname'], $auto_title);
                    }
                    //读取自动描述
                    if(!empty($auto_desc))
                    {
                        $auto_desc = str_replace('XXX', $info['kindname'], $auto_desc);
                    }

                    $result_seotitle = empty($typeid)?$info['seotitle']:'';
                    $result_seotitle = empty($result_seotitle)?$auto_title:$result_seotitle;
                    $result_seotitle = empty($result_seotitle)?$info['kindname']:$result_seotitle;

                    $result_description = empty($typeid)?$info['description']:'';
                    $result_description = empty($result_description)?$auto_desc:$result_description;



                    $result['seotitle'] = $result_seotitle;
                    $result['keyword'] = $info["keyword"];
                    $result['description'] = $result_description;

                }


            }
        }
        else
        {
            $info = Model_Nav::get_channel_info($typeid);
            $result["seotitle"] = $info['seotitle'] ;
            $result["keyword"] = $info["keyword"];
            $result["description"] = $info["description"];
        }

        return $result;
    }

    /**
     * @function 返回上级所有目的地
     * @param $destid
     * @return array
     */

    public static function get_prev_dest($destid)
    {

        $loopid=$destid;
        $result=array();
        $looptime = 1;
        while(1)
        {
            $pid = DB::select('pid')->from('destinations')->where('id','=',$loopid)->execute()->get('pid');
            if($pid!=0)
            {
                $pinfo = DB::select()->from('destinations')->where('id','=',$pid)->execute()->current();
                $result[]=$pinfo;
                $loopid=$pinfo['id'];
            }
            else
            {
                break;
            }
            //增加一个循环跳出机制,避免因数据库问题造成死循环
            $looptime++;
            if($looptime > 5)
            {
                break;
            }


        }
        $count=count($result);
        for($i=$count-1;$i>=0;$i--)
        {
            $newresult[]=$result[$i];
        }
        $destinfo=DB::select()->from('destinations')->where('id','=',$destid)->execute()->current();
        $newresult[]=$destinfo;
        return $newresult;

    }


    /**
     * @function 获取目的地
     * @param $py
     * @param $typeid
     * @param $offset
     * @param $row
     * @return Array
     */
    public static function get_dest_by_pinyin($py,$typeid,$offset,$row)
    {
        $ar = explode(',',$py);
        $whereArr = array();
        foreach($ar as $a)
        {
            $whereArr[]="a.pinyin LIKE '$a%' ";
        }
        $where = implode(" OR ",$whereArr);
        if($typeid==0)
        {
            $where = "isopen=1 AND ishot=1";
            $arr = ORM::factory('destinations')
                ->where($where)
                ->order_by("displayorder","ASC")
                ->offset($offset)
                ->limit($row)
                ->get_all();
        }
        else
        {
            $pinyin = DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
            if(!empty($pinyin))
            {
                //对应目的地表
                $table = 'sline_' . $pinyin . '_kindlist';
                $sql = "SELECT a.id,a.kindname,a.pinyin FROM `sline_destinations` a LEFT JOIN ";
                $sql .= "`$table` b ON (a.id=b.kindid) ";
                $sql .= "WHERE FIND_IN_SET($typeid,a.opentypeids) AND a.isopen=1 AND ($where) ";
                $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC ";
                $sql .= "LIMIT {$offset},{$row}";
                $arr = DB::query(1,$sql)->execute()->as_array();

            }

        }
        return $arr;



    }

    /**
     * @function 匹配目的地拼音
     * @param $keyword
     * @param $typeid
     * @return string
     */
    public static  function match_pinyin($keyword,$typeid)
    {
        if(empty($typeid))
        {
            $sql = "SELECT kindname FROM `sline_destinations` WHERE isopen=1 and (kindname like '%".$keyword."%' or pinyin like '".$keyword."%' )";
        }
        else
        {

            $model_pinyin = DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
            $joinTable = 'sline_'.$model_pinyin.'_kindlist';
            $sql = "SELECT a.kindname FROM `sline_destinations` a ";
            $sql.= "LEFT JOIN $joinTable as b ON(a.id=b.kindid)  ";
            $sql.= "WHERE a.isopen=1 AND FIND_IN_SET($typeid,a.opentypeids)";
            $sql.= "and (kindname like '%".$keyword."%' or pinyin like '".$keyword."%' ) ";

        }


        $res = DB::query(1,$sql)->execute()->as_array();
        $str = '';


        foreach($res AS $row) // 获取全部name
        {

            $row['kindname'] = strlen($keyword)>1 ? str_replace($keyword, '<b>' . $keyword . '</b>', $row['kindname']) : $row['kindname'];
            $str .= $row['kindname']. ",";
        }

        $str = substr($str, 0, strlen($str)-1);
        return $str;
    }
    ////******************* PC端结束  ****************************/////

    ////******************** 后台开始   **********************////
    /**
     * @function 级边删除，弃用
     */
    public function deleteCascade()
    {

    }

    /**
     * @function 删除目的地
     * @throws Kohana_Exception
     */
    public function deleteClear()
    {
        $children = ORM::factory('destinations')->where("pid={$this->id}")->find_all()->as_array();
        foreach ($children as $child)
        {
            $child->deleteClear();
        }
        $this->update_sibling('del');
        /*	Common::deleteRelativeImage($this->litpic);
            $piclist=explode(',',$this->piclist);
             foreach($piclist as $k=>$v)
             {
                  $img_arr=explode('||',$v);
                  Common::deleteRelativeImage($img_arr[0]);
             }*/
        $this->delete();
    }

    /**
     * @function 更新其他产品关联目的地即 XXX_kindlist
     * @param string $action
     * @return bool
     * @throws Kohana_Exception
     */
    public function update_sibling($action = 'add')
    {
        $kindid = $this->id;

        $list = DB::select('pinyin')->from('model')->execute()->as_array();
        $tables = array();
        foreach($list as $v)
        {
            $table_name = $v['pinyin'].'_kindlist';
            if(St_Functions::is_table_exist($table_name))
            {
                $tables[]=$table_name;
            }
        }

        foreach ($tables as $tablename)
        {
            if ($action == 'add')
            {

                $model_num=DB::select(array(DB::expr('COUNT(*)'), 'num'))->from($tablename)->where('kindid','=',$kindid)->execute()->get('num');
                if ($model_num < 1)
                {
                    DB::insert($tablename, array('kindid'))->values(array($kindid))->execute();
                }
            }
            else if ($action == 'del')
            {
                DB::delete($tablename)->where('kindid','=',$kindid)->execute();
            }
        }
        return true;
    }

    /**
     * @function 根据id字符串获取以逗号分隔的目的地名称
     * @param $kindid_str
     * @param string $separator
     * @return string
     */
    public static function getKindnameList($kindid_str, $separator = ',')
    {
        $dest = array();
        $rs = DB::select('kindname')->from('destinations')->where('id', 'in', explode($separator, $kindid_str))->execute()->as_array();
        foreach ($rs as $v)
        {
            $dest[] = $v['kindname'];
        }
        $dest_str = implode($separator, $dest);
        return $dest_str;
    }

    /**
     * @function 获取所有祖先目的地
     * @param $id
     * @return array|null
     */
    public static function getParents($id)
    {

        $first_dest = ORM::factory('destinations', $id);
        if (!$first_dest->id)
            return null;
        $cid = $first_dest->pid;
        while (true)
        {
            $cur_dest = ORM::factory('destinations', $cid);

            if ($cur_dest->id == 0)
                return null;
            $new_row['id'] = $cur_dest->id;
            $new_row['kindname'] = $cur_dest->id;
            $parents[] = $new_row;

            if ($cur_dest->pid == 0)
            {
                break;
            }
            $cid = $cur_dest->pid;


        }
        return $parents;
    }

    /**
     * @function 根据目的地ID字符串（逗号分隔) ，返回目的地数组
     * @param $kindid_str
     * @return array
     */
    public static function getKindlistArr($kindid_str)
    {
        $kindid_arr = explode(',', $kindid_str);
        $kind_arr = array();
        foreach ($kindid_arr as $v)
        {
            $dest = ORM::factory('destinations', $v);
            if ($dest->id)
            {
                $kind_arr[] = $dest->as_array();
            }

        }
        return $kind_arr;

    }


    /**
     * @function 批量保存weburl
     * @param $data
     * @throws Kohana_Exception
     */
    public function save_web($data)
    {
        $weburl = ARR::get($data, 'weburl');

        $id = ARR::get($data, 'id');

        for ($i = 0; isset($weburl[$i]); $i++)
        {
            $obj = $this->where('id', '=', $id[$i])->find();
            $obj->weburl = $weburl[$i];
            $obj->update();
            $obj->clear();
        }

    }

    /**
     * @function 获取以逗号分害的所有目的地的祖先目的地.
     * @param $kinds
     * @return array
     */
    public static function getParentsStr($kinds)
    {
        $kindArr = explode(',', $kinds);
        $parentsIdArr = array();
        foreach ($kindArr as $v)
        {
            $parents = self::getParents($v);
            if (is_array($parents))
            {
                foreach ($parents as $row)
                {
                    $parentsIdArr[] = $row['id'];
                }
            }
        }
        //  $newArr=array_merge($kindArr,$parentsIdArr);
        foreach ($parentsIdArr as $val)
        {
            if (!in_array($val, $kindArr))
                $kindArr[] = $val;
        }
        return $kindArr;
    }

    /**
     * @function 设置模块目的地是否开启
     * @param $kindid
     * @param $typeid
     * @param $isopen
     * @return bool|ORM
     */
    public static function setTypeidOpen($kindid, $typeid, $isopen)
    {
        $dest = $first_dest = ORM::factory('destinations', $kindid);
        if (!$dest->loaded())
            return false;
        $openTypeids = $dest->opentypeids;
        $openArr = empty($openTypeids) ? array() : explode(',', $openTypeids);
        if ($isopen)
            $openArr[] = $typeid;
        else
            $openArr = array_diff($openArr, array($typeid));
        $dest->opentypeids = implode(',', $openArr);
        return $dest->save();
    }


    /**
     * @function 获取默认的最终目的地,参数为目的地id数组
     * @param $kindlist
     * @return string
     */
    public static function getFinaldestId($kindlist)
    {
        if (empty($kindlist))
            return '';
        sort($kindlist);
        return $kindlist[0];
    }

    /**
     * @function 判断拼音是否存在
     * @param $pinyin
     * @return array|bool
     */
    public static function is_pinyin_exist($pinyin)
    {
        $cnt = DB::select(DB::expr(' count(1) num '))->from('destinations')->where('pinyin','=',$pinyin)->execute()->get('num', 0);
        return $cnt>0 ? true : false;
    }


    /**
     * @function 获取拼音,防止与模型和目的地中拼音重复
     * @param $pinyin
     * @param int $loop_count
     * @return string
     */
    public static function get_legal_pinyin($pinyin, $loop_count=1000)
    {
        $org_py = $pinyin;
        $flag_model = Model_Model::exsits_model($pinyin);
        $flag_dest  = self::is_pinyin_exist($pinyin);
        if ( $flag_model OR $flag_dest)
        {
            for ($i = 1; $i <= $loop_count; $i++)
            {
                $pinyin = $org_py . $i;
                $flag_model = Model_Model::exsits_model($pinyin);
                $flag_dest  = self::is_pinyin_exist($pinyin);
                if (!$flag_model && !$flag_dest)
                {
                    break;
                }
            }
        }
        return $pinyin;
    }

    ////******************** 后台结束  ***********************////


    ///********************* 手机开始  ***********************///


    /**
     * @function 下级目的地列表
     * @param $offset 偏移量
     * @param $row  条数
     * @param $pid  父级目的地id
     * @param $typeid  类型id
     * @return mixed
     */
    public static function get_dest($pid = 0, $typeid = 0, $offset, $row = 10)
    {
        $arr = ORM::factory('destinations')
            ->where("isopen=1 AND pid=$pid AND FIND_IN_SET($typeid,opentypeids)")
            ->order_by('displayorder', 'ASC')
            ->offset($offset)
            ->limit($row)
            ->get_all();
        return $arr;
    }

    /**
     * @function 获取已开启目的地
     * @param int $typeid
     * @return mixed
     */
    public static function get_all_dest($typeid=0)
    {
        static $dest = null;
        if (is_null($dest))
        {
            if( in_array($typeid, array(1,2,3,4,5,6,10,11,13))  || ($typeid >= 201))
            {
                $model_pinyin   = DB::select('pinyin')->from('model')->where('id','=',$typeid)->execute()->get('pinyin');
                $join_tab       = 'sline_'.$model_pinyin.'_kindlist';
                $sql = "SELECT a.id, a.pid, a.kindname, a.pinyin FROM `sline_destinations` a ";
                $sql.= "LEFT JOIN {$join_tab} as b ON(a.id=b.kindid)  ";
                $sql.= "WHERE a.isopen=1 AND FIND_IN_SET({$typeid},a.opentypeids) ORDER BY IFNULL(b.displayorder,9999) ASC ";
                $dest = DB::query(Database::SELECT, $sql)->execute()->as_array();
            }
            else
            {
                $query = DB::select('id', 'pid', 'kindname', 'pinyin')->from('destinations')->where('isopen', '=', 1);
                if(!empty($typeid))
                {
                    $query->and_where(DB::expr("FIND_IN_SET({$typeid},opentypeids)"),'>',0);
                }
                $query->order_by(DB::expr(" IFNULL(displayorder,9999) ASC "));
                $dest = $query->execute()->as_array();
            }
        }
        return $dest;
    }

    /**
     * @function 递归目的地父级导航
     * @param $id
     * @param array $data
     * @return array
     */
    public static function get_dest_nav($id, $data = array(),$typeid=0)
    {

        $dest = self::get_all_dest($typeid);
        foreach ($dest as $v)
        {
            if ($v['id'] == $id)
            {
                if ($v['pid'] != 0)
                {
                    array_unshift($data,$v);
                    $data=self::get_dest_nav($v['pid'],$data,$typeid);
                }
                else
                {
                    array_unshift($data,$v);
                }
                break;
            }
        }
        return $data;
    }

    ///********************* 手机结束 ************************///

}