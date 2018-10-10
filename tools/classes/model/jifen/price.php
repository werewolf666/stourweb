<?php defined('SYSPATH') or die('No direct script access.');

class Model_Jifen_Price extends ORM
{
    public static $field_issystem_names = array('0'=>'自定义策略','1'=>'系统策略');

    /**
     * @function 清除某类产品的积分抵现策略
     * @param $typeid
     * @param $tpriceid
     * @return bool
     */
    public static function clear_all_jifentprice($typeid,$tpriceid)
    {
        if(empty($tpriceid)||empty($typeid))
            return false;
        $table = DB::select('maintable')->from('model')->where('id','=',$typeid)->execute()->get('maintable');
        $table = 'sline_'.$table;
        $sql = " update {$table} set jifentprice_id=0 where jifentprice_id={$tpriceid}";
        $rows= DB::query(Database::UPDATE,$sql)->execute();
        if($rows)
            return true;
        else
            return false;
    }

    /**
     * @function 设置多个产品的积分抵现策略id
     * @param $typeid
     * @param $jifenid
     * @param $productids
     * @return bool|object
     */
    public static function set_tprice_id($typeid,$jifenid,$productids)
    {
        $productids_str = implode(',',$productids);
        if(empty($jifenid)||empty($productids_str))
            return false;
        $table = DB::select('maintable')->from('model')->where('id','=',$typeid)->execute()->get('maintable');
        $jifen_model = ORM::factory('jifen_price',$jifenid);
        if(empty($table) || !$jifen_model->loaded())
            return false;
        $table = 'sline_'.$table;
        $sql = " update {$table} set jifentprice_id={$jifenid} where id in ({$productids_str})";
        $rows= DB::query(Database::UPDATE,$sql)->execute();
        if($rows)
        {
            $org_typeid = $jifen_model->typeid;
            $jifen_model->typeid = $typeid;
            $jifen_model->save();
            if($org_typeid!=$typeid)
            {
                self::clear_all_jifentprice($org_typeid,$jifenid);
            }
        }
        return $rows;
    }

    /**
     * @function 删除某个产品的积分抵现id
     * @param $typeid
     * @param $jifenid
     * @param $productid
     * @return bool
     */
    public static function clear_jifentprice($typeid,$jifenid,$productid)
    {
        if(empty($jifenid)||empty($typeid))
            return false;
        $table = DB::select('maintable')->from('model')->where('id','=',$typeid)->execute()->get('maintable');
        $table = 'sline_'.$table;
        $sql = " update {$table} set jifentprice_id=0 where jifentprice_id={$jifenid} and id='{$productid}'";
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
        self::clear_all_jifentprice($this->typeid,$this->id);
        $this->delete();
    }

    /**
     * @function 搜索积分抵现策略
     * @param $params
     * @param $page
     * @param int $pagesize
     * @return array
     */
    public static function search_result($params,$page,$pagesize=8)
    {
        $issystem = isset($params['issystem'])?intval($params['issystem']):null;
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

        $sql = "select * from sline_jifen_price {$w} order by addtime desc limit {$offset},{$pagesize}";
        $sql_num = " select count(*) as num from sline_jifen_price {$w}";

        $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
        $num = DB::query(Database::SELECT,$sql_num)->execute()->get('num');
        $result = array('list'=>$list,'page'=>$page,'pagesize'=>$pagesize,'total'=>$num);
        return $result;
    }
    /**
     * @function 获取产品实际使用的积分抵现策略
     * @param $jifenid
     * @param $typeid
     * @return bool|null
     */
    public static function get_used_jifentprice($jifenid,$typeid)
    {
        $info = null;
        $curtime = time();
        if(!empty($jifenid))
        {
            $info = DB::select()->from('jifen_price')->where('id','=',$jifenid)->and_where('isopen','=',1)->execute()->current();

        }
        else
        {
            $info = DB::select()->from('jifen_price')->where('issystem','=',1)->and_where('typeid','=',$typeid)->and_where('isopen','=',1)->execute()->current();
        }
        if(empty($info['id']))
        {
            return false;
        }

        $endtime = empty($info['endtime'])?0:strtotime(date('Y-m-d 23:59:59',$info['endtime']));
        $starttime = empty($info['starttime'])?0:strtotime(date('Y-m-d 00:00:00',$info['starttime']));

        if($info['expiration_type']==0 || ($info['expiration_type']==1 && $curtime>=$starttime && $curtime<=$endtime) || ($info['expiration_type']==2 && $curtime<=$endtime))
        {
            $cfg_exchange_jifen = isset($GLOBALS['cfg_exchange_jifen'])?$GLOBALS['cfg_exchange_jifen']:Model_Sysconfig::get_configs(0,'cfg_exchange_jifen',true);
            $cfg_exchange_jifen = intval($cfg_exchange_jifen);
            if($cfg_exchange_jifen<=0 || $info['toplimit']<$cfg_exchange_jifen)
                return false;

            $info['jifentprice'] = floor($info['toplimit']/$cfg_exchange_jifen);
            $info['cfg_exchange_jifen'] = $cfg_exchange_jifen;
            return $info;
        }
        return false;
    }

    /**
     * @function 计算预订送积分
     * @param $jifenid
     * @param $typeid
     * @param $needjifen
     * @param $userinfo
     * @return bool
     * @throws Exception
     */
    public static function calculate_jifentprice($jifenid,$typeid,$needjifen,$userinfo)
    {
            if(empty($userinfo) ||empty($needjifen))
                return 0;
            $jifentprice_info = self::get_used_jifentprice($jifenid,$typeid);
            $jifentprice = floor($needjifen / $jifentprice_info['cfg_exchange_jifen']); //所需积分
            if ($jifentprice <= 0 || $userinfo['jifen'] < $needjifen)
                return 0;
            return $jifentprice;
    }

}