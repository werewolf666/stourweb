<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-24 下午1:56
 * Desc:产品详细内容页栏目调用标签
 */
class Taglib_Detailcontent {

    public static function get_content($params)
    {
        $default=array(
            'typeid'=>'',
            'productinfo'=>0,
            'onlyrealfield'=>0,
            'pc'=>0

        );

        $params=array_merge($default,$params);
        extract($params);


        if(empty($typeid))return array();
        $tables = array(
            '1'=>'sline_line_content',
            '2'=>'sline_hotel_content',
            '3'=>'sline_car_content',
            '5'=>'sline_spot_content',
            '8'=>'sline_visa_content',
            '13'=>'sline_tuan_content'
        );
        $extend_tables = array(
            '1'=>'sline_line_extend_field',
            '2'=>'sline_hotel_extend_field',
            '3'=>'sline_car_extend_field',
            '5'=>'sline_spot_extend_field',
            '8'=>'sline_visa_extend_field',
            '13'=>'sline_tuan_extend_field'
        );

        $table = $tables[$typeid];
        $extend_tables = $extend_tables[$typeid];
        $where = '';
        if (empty($table))
        {
          
            $isExists = DB::select()->from('model')->where("id={$typeid}")->execute()->current();
            if (empty($isExists))
            {
                return '';
            }
            $table = 'sline_model_content';
            $extend_tables = "sline_{$isExists['addtable']}";
            $where = 'typeid=' . $typeid . ' and ';
        }
        $sql = "SELECT columnname,chinesename,isrealfield FROM {$table} ";

        $sql.= "WHERE webid=0 and isopen=1 ";
        if($table == 'sline_model_content')
        {
            $sql.= " and typeid={$typeid} ";
        }
        $sql.= "ORDER BY displayorder ASC";
        $arr = DB::query(1,$sql)->execute()->as_array();

        //扩展表数据
        $productid = $productinfo['id'];//产品id
        $sql = "SELECT * FROM $extend_tables WHERE productid='$productid'";
        $ar = DB::query(1,$sql)->execute()->as_array();
        $list = array();
        foreach($arr as $v)
        {
            if ($v['columnname'] == 'tupian')
            {
                continue;
            }
            if($v['isrealfield']==1)
            {
                $content = !empty($productinfo[$v['columnname']]) ? $productinfo[$v['columnname']] : $ar[0][$v['columnname']];
                $content = $content ? $content : '';
            }
            //是否只显示真实字段
            else if($onlyrealfield==1)
            {
                $content = '';
            }
            else
            {
                $content = $productid;
            }
            if($typeid==1 && $v['columnname']=='payment' && empty($content))
            {
                $content = $GLOBALS['cfg_payment'];
            }

            if(empty($content)) continue;

            $a = array();
            $a['columnname'] = $v['columnname'];
            $a['chinesename'] = $v['chinesename'];
            if($v['columnname']=='linedoc'){
                $linedoc=unserialize($content);
                if(!is_array($linedoc)){
                    continue;
                }
                $content='<ol>';
                foreach($linedoc['path'] as $k=>$v){
                    $content.="<li><a href=\"/pub/download/?file={$v}&name={$linedoc['name'][$k]}\" title=\"{$linedoc['name'][$k]} 下载\" class=\"name\">{$linedoc['name'][$k]}</a></li>";
                }
                $content.='</ol>';
                $a['content']=$content;
            }else{
                $a['content'] = $pc==0 ? Product::strip_style($content) : $content; //针对PC/手机版选择是否去样式.
            }

            $list[] = $a;

        }
        return $list;






    }

}