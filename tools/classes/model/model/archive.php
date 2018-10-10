<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Model_Archive extends ORM {


    /**
     * @function  获取通用产品最低价
     * @param $tongyongid
     * @param array $params
     * @return array
     */
    public static function get_minprice($tongyongid, $params = array())
    {
        $rs = array('price' => 0);
        $time = strtotime(date('Y-m-d'));
        $update_minprice = false;
        if (!is_array($params))
        {
            $params = array('suitid' => $params);
        }
        if (!isset($params['suitid']))
        {
            //市场价最低
            $sql = "SELECT MIN(CAST(sellprice as SIGNED)) AS sellprice FROM `sline_model_suit` ";
            $sql .= "WHERE productid='{$tongyongid}' AND sellprice >0";
            $row = DB::query(1, $sql)->execute()->current();
            $rs['sellprice'] = Currency_Tool::price($row['sellprice']);
            //报价最低
            if (!isset($params['info']))
            {
                $params['info'] = DB::select('price', 'price_date')->from('model_archive')->where('id', '=', $tongyongid)->execute()->current();
            }
            if ($time == $params['info']['price_date'])
            {
                $rs['price'] = Currency_Tool::price($params['info']['price']);
                return $rs;
            }
            //更新最低价
            $update_minprice = true;
        }
        $where = isset($params['suitid']) ? " and `suitid` ={$params['suitid']} " : '';
        $sql = 'SELECT MIN(price) as price FROM sline_model_suit_price WHERE `productid`="' . $tongyongid . '" and `day`>=' . $time . ' and (`number`>0 or `number`=-1) ' . $where;
        $result = DB::query(1, $sql)->execute()->current();
        if ($result)
        {
            $rs['price'] = $result['price'];
        }
        //更新产品最低价
        if ($update_minprice)
        {
            DB::update('model_archive')->set(array('price' => $rs['price'], 'price_date' => $time))->where('id', '=', $tongyongid)->execute();
        }
        $rs['price'] = Currency_Tool::price($rs['price']);
        return $rs;
    }
    /**
     * @function 库存操作函数,当$dingnum为正数是为加库存,当$dingnum为负数时为减库存
     * @param $suitid
     * @param $dingnum
     * @param $day
     * @return bool|object
     */
    public static function storage($suitid, $dingnum, $order_arr)
    {
        $day = strtotime($order_arr['usedate']);
        $org_number = DB::select('number')->from('model_suit_price')
            ->where('day', '=', $day)
            ->and_where('suitid', '=', $suitid)
            ->execute()
            ->get('number');
        if ($org_number == -1)
        {
            return true;
        }
        if (intval($dingnum) < 0)
        {
            //如果库存小于需求库存量,则直接返回减库存失败
            if ($org_number < abs($dingnum))
            {
                return false;
            }
        }
        $update_arr = array(
            'number' => DB::expr("number + $dingnum")
        );
        $query = DB::update('model_suit_price')
            ->set($update_arr)
            ->where('suitid', '=', $suitid)
            ->and_where('number', '<>', -1)
            ->and_where('day', '=', $day);

        return $query->execute();

    }

}