<?php defined('SYSPATH') or die('No direct script access.');

class Model_Jifen extends ORM
{
    public static $field_issystem_names = array('0'=>'自定义策略','1'=>'系统策略');
   // public static $field_section_names = array('0'=>'其他策略','1'=>'产品预订','2'=>'产品评论','3'=>'产品发布');

    public static $field_section_names = array(0=>'其他',1=>'产品预订',2=>'产品评论',3=>'文章发布',4=>'文章评论',5=>'积分抽奖',6=>'会员签到',7=>'新会员引导',8=>'活跃会员',9=>'分销产品');

    /**
     * @function 设置产品的预订积分id
     * @param $typeid
     * @param $jifenid
     * @param $productids
     */
    public static function set_jifenbook_id($typeid,$jifenid,$productids)
    {
        $productids_str = implode(',',$productids);
        if(empty($jifenid)||empty($productids_str))
            return false;
        $table = DB::select('maintable')->from('model')->where('id','=',$typeid)->execute()->get('maintable');
        $jifen_model = ORM::factory('jifen',$jifenid);
        if(empty($table) || !$jifen_model->loaded())
              return false;
        $table = 'sline_'.$table;
        $sql = " update {$table} set jifenbook_id={$jifenid} where id in ({$productids_str})";
        $rows= DB::query(Database::UPDATE,$sql)->execute();
        if($rows)
        {
            $org_typeid = $jifen_model->typeid;
            $jifen_model->typeid = $typeid;
            $jifen_model->save();
            if($org_typeid!=$typeid)
            {
                self::clear_all_jifenbook($org_typeid,$jifenid);
            }
        }
        return $rows;
    }
    /**
     * @function 清除某类产品的积分ID
     * @param $typeid
     * @param $jifenid
     */
    public static function clear_all_jifenbook($typeid,$jifenid)
    {
        if(empty($jifenid)||empty($typeid))
            return false;

        $table = DB::select('maintable')->from('model')->where('id','=',$typeid)->execute()->get('maintable');
        $table = 'sline_'.$table;
        $column_result = DB::query(Database::SELECT,"show columns from {$table} like 'jifenbook_id'")->execute()->as_array();
        if(empty($column_result))
        {
            return;
        }
        $sql = " update {$table} set jifenbook_id=0 where jifenbook_id={$jifenid}";
        $rows= DB::query(Database::UPDATE,$sql)->execute();
        if($rows)
           return true;
        else
           return false;
    }

    /**
     * @function 清除某个产品的积分ID
     * @param $typeid
     * @param $jifenid
     * @param $productid
     * @return bool
     */
    public static function clear_jifenbook($typeid,$jifenid,$productid)
    {
        if(empty($jifenid)||empty($typeid))
            return false;
        $table = DB::select('maintable')->from('model')->where('id','=',$typeid)->execute()->get('maintable');
        $table = 'sline_'.$table;
        $sql = " update {$table} set jifenbook_id=0 where jifenbook_id={$jifenid} and id='{$productid}'";
        $rows= DB::query(Database::UPDATE,$sql)->execute();
        if($rows)
            return true;
        else
            return false;
    }
    /**
     * @function 删除一个策略
     * @throws Kohana_Exception
     */
    public function delete_clear()
    {
        self::clear_all_jifenbook($this->typeid,$this->id);
        $this->delete();
    }

    /**
     * @function 搜索积分策略
     * @param $params
     * @param $page
     * @param int $pagesize
     * @return array
     */
    public static function search_result($params,$page,$pagesize=8)
    {
        $issystem = isset($params['issystem'])?intval($params['issystem']):null;
        $section = isset($params['section'])?intval($params['section']):null;
        $isopen = isset($params['isopen'])?intval($params['isopen']):null;
        $typeid = intval($params['typeid']);
        $keyword = $params['keyword'];
        $label = $params['label'];
        $page = intval($page);
        $page = $page<1?1:$page;
        $offset = $pagesize*($page-1);

        $w=' where id!=0 ';
        if($issystem!==null)
        {
            $w.= " and issystem='{$issystem}'";
        }
        if($section!==null)
        {
            $w.= " and section='{$section}'";
        }
        if($isopen!==null)
        {
            $w.= " and isopen='{$isopen}'";
        }
        if(!empty($typeid))
        {
            $w.=" and find_in_set({$typeid},typeid)";
        }
        if(!empty($keyword))
        {
            $w.=" and title like '%{$keyword}%'";
        }
        if(!empty($label))
        {
            $w.=" and label='{$label}'";
        }

        $sql = "select * from sline_jifen {$w} order by addtime desc limit {$offset},{$pagesize}";
        $sql_num = " select count(*) as num from sline_jifen {$w}";

        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
        $result = array('list'=>$list,'page'=>$page,'pagesize'=>$pagesize,'total'=>$num);
        return $result;
    }

    /**
     * @function 获取产品实际使用的预订积分策略
     * @param $jifenid
     * @param $typeid
     * @return bool|null
     */
    public static function get_used_jifenbook($jifenid,$typeid)
    {
        $info = null;
        if(!empty($jifenid))
        {
            $info = DB::select()->from('jifen')->where('id','=',$jifenid)->and_where('isopen','=',1)->execute()->current();
        }
        else
        {
            $info = DB::select()->from('jifen')->where('issystem','=',1)->and_where('section','=',1)->and_where('typeid','=',$typeid)->and_where('isopen','=',1)->execute()->current();
        }
        if(empty($info['id']))
            return false;
        else
            return $info;
    }

    /**@function 获取产品使用的评论送积分策略
     * @param $typeid
     * @return bool
     */
    public static function get_used_jifencomment($typeid)
    {
        $info = DB::select()->from('jifen')->where('issystem','=',1)->and_where('section','=',2)->and_where('typeid','=',$typeid)->and_where('isopen','=',1)->execute()->current();
        if(empty($info['id']))
            return false;
        else
            return $info;
    }

    /**
     * @function 奖励客户积分的通用函数
     * @param $label
     * @param $mid
     * @param int $totalprice
     * @return int 奖励积分数
     */
    public static function reward_jifen($label,$mid,$totalprice=0)
    {
        $info = DB::select()->from('jifen')->where('label','=',$label)->and_where('isopen','=',1)->execute()->current();
        if(empty($info['id']))
        {
            return 0;
        }
        $curtime = time();
        $frequency = $info['frequency_type']==1||$info['frequency_type']==0?1:$info['frequency'];
        $used_num=0;
        if($info['frequency_type']==1 || $info['frequency_type']==3)
        {
            $used_num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen_record')->where('label','=',$label)->and_where('memberid','=',$mid)->execute()->get('num');
        }
        if($info['frequency_type']==2)
        {
            $starttime = strtotime(date('Y-m-d 00:00:00'));
            $endtime = strtotime(date('Y-m-d 23:59:59'));
            $used_num = DB::select(array(DB::expr('COUNT(*)'), 'num'))->from('jifen_record')
                ->where('label','=',$label)
                ->and_where('memberid','=',$mid)
                ->and_where('addtime','>=',$starttime)
                ->and_where('addtime','<=',$endtime)
                ->execute()->get('num');
        }
        if($used_num>=$frequency)
        {
            return 0;
        }
        $jifen = $info['rewardway']==1? floor($totalprice*$info['value']%100):$info['value'];
        $jifen = intval($jifen);
        if(empty($jifen))
        {
            return 0;
        }

        $result =DB::query(Database::UPDATE,"update sline_member set jifen=jifen+{$jifen} where mid='{$mid}'")->execute();
        if(empty($result))
        {
            return 0;
        }
        else
        {
            DB::insert('jifen_record',array('memberid','jifen','label','addtime'))->values(array($mid,$jifen,$label,$curtime))->execute();
            return $jifen;
        }

    }

}