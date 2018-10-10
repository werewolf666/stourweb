<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 时间处理类
 * Class Time
 */
class St_Time{


    /**
     * @function 生成日期格式
     * @param string $format
     * @param int $timest
     * @return string
     */
    static function timestamp_to_date($format='Y-m-d H:i:s', $timest=0)
    {
        if(empty($format))
        {
            $format = 'Y-m-d H:i:s';
        }
        return gmdate ($format, $timest);
    }

    //功能：计算两个时间戳之间相差的日时分秒
    //$begin_time  开始时间戳
    //$end_time 结束时间戳
    static function time_span($begin_time,$end_time)
    {
        if($begin_time < $end_time){
            $starttime = $begin_time;
            $endtime = $end_time;
        }else{
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        //计算天数
        $timediff = $endtime-$starttime;
        $days = intval($timediff/86400);
        //计算小时数
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
        //计算分钟数
        $remain = $remain%3600;
        $mins = intval($remain/60);
        //计算秒数
        $secs = $remain%60;
        $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
        return $res;
    }

   

    

}