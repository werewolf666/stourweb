<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 13:59
 */
class Plugin_Fenxiao extends Plugin_Core_Base{
    public function on_orderstatus_changed($params){
        try {
            if ($params['status'] == 5)
                $this->pay_commission($params);
          }catch(Exception $excep){
            echo $excep->getMessage();
        }

    }
    //反佣
    private function pay_commission($order)
    {

        $level_names=array(1=>'一级',2=>'二级',3=>'三级');
        $cur_fenxiao_model=ORM::factory('fenxiao')->where('memberid','=',$order['memberid'])->find();

        $level_num = Model_Fenxiao_Config::get_val('cfg_fenxiao_level_num');

        if(!$cur_fenxiao_model->loaded())
        {
            return;
        }


        $consumeAmount= floatval($order['actual_price']);//Model_Member_Order::get_payed_amount($order);

        /*
        $commissiontype=Model_Fenxiao_Config::get_val('cfg_fenxiao_commission_type_'.$order['typeid']);
       $rateArr=array();
        $cashArr=array();
        if($commissiontype==1) {
            $rateArr = $this->get_commission_rate($order['typeid'], $order['productautoid']);
        }
        else {
            $cashArr = $this->get_commission_cash($order['typeid'], $order['productautoid']);
        }*/

        $fenxiaoArr=array();
        $fenxiaoArr[]=Model_Fenxiao::get_ancestor_fenxiao_info($order['memberid'],1);
        $fenxiaoArr[]=Model_Fenxiao::get_ancestor_fenxiao_info($order['memberid'],2);
        if($level_num==0 || $level_num==3)
        {
            $fenxiaoArr[] = Model_Fenxiao::get_ancestor_fenxiao_info($order['memberid'], 3);
        }
        $curtime=time();
        //一级分销反佣
        foreach($fenxiaoArr as $k=>$v) {
            if ($v) {
                $hasNum=ORM::factory('fenxiao_record')->where('memberid','=',$v['memberid'])->and_where('fxmemberid','=',$order['memberid'])->and_where('type','=',0)->and_where('orderid','=',$order['id'])->count_all();
                if($hasNum>0)
                {
                    continue;
                }
                $fenxiaoModel = ORM::factory('fenxiao', $v['id']);
                if ($fenxiaoModel->loaded())
                {
                    if($fenxiaoModel->isfrozen==1)
                    {
                        continue;
                    }

                    $payAmount=0;
                    $commission_info = self::get_commission_info_line($order['typeid'], $order['productautoid'],$fenxiaoModel->fxgroupid);
                    if($commission_info['commission_type']==1)
                    {
                        $payAmount = floatval($consumeAmount * $commission_info['rate_arr'][$k]);
                    }else
                    {
                        $cash_info = $commission_info['cash_arr'][$k];
                        if($order['typeid']==1)
                        {
                            $payAmount = floatval($cash_info['adult'])*intval($order['dingnum'])+floatval($cash_info['old'])*intval($order['oldnum'])+floatval($cash_info['child'])*intval($order['childnum']);
                        }
                        else
                        {
                            $payAmount = floatval($cash_info['adult'])*intval($order['dingnum']);
                        }
                    }
                    //$fenxiaoModel->fxamount += $payAmount;
                    $fxrankid = Model_Fenxiao::get_rankid($v['memberid']);

                    $old_fxrankid= $fenxiaoModel->fxrankid;
                    $fenxiaoModel->fxrankid = $fxrankid;
                    $fenxiaoModel->save();

                    $member_out = DB::select()->from('member')->where('mid','=',$order['memberid'])->execute()->current();
                    $member_out_nickname = $member_out['nickname'].'['.$order['memberid'].']';
                    $cur_level = Model_Fenxiao::get_rank($v['memberid'],$order['memberid']);
                    $description = $level_names[$cur_level]."分销商".$member_out_nickname."订单<".$order['ordersn'].">交易完成，反佣".$payAmount.'元';
                    $deal_result=Model_Member::cash_deal($v['memberid'],$payAmount,0,$description,false,$order['id']);

                    if ($deal_result['status']) {
                        $fenxiaoRecord = ORM::factory('fenxiao_record');
                        $fenxiaoRecord->memberid = $v['memberid'];
                        $fenxiaoRecord->type = 0;
                        $fenxiaoRecord->amount = $payAmount;
                        $fenxiaoRecord->orderid=$order['id'];
                        $fenxiaoRecord->fxmembertype=$k+1;
                        $fenxiaoRecord->fxmemberid=$order['memberid'];
                        $fenxiaoRecord->addtime=$curtime;
                        $fenxiaoRecord->save();


                        if($fxrankid)
                        {
                            $rank_info = DB::select()->from('fenxiao_rank')->where('id', '=', $fxrankid)->execute()->current();
                            $jifen_label = DB::select('label')->from('jifen')->where('id', '=', $rank_info['jifen_id'])->execute()->get('label');
                            if (!empty($jifen_label)) {
                                $jifen = Model_Jifen::reward_jifen($jifen_label, $v['memberid']);
                                if (!empty($jifen)) {
                                    $jifen_desc = $rank_info['title'].'分销商的分销订单完成获得'.$jifen.'积分';
                                    St_Product::add_jifen_log($v['memberid'],$jifen_desc, $jifen, 2);
                                }
                            }
                        }
                   }
                }
            }
        }


        //自身返佣
        if($cur_fenxiao_model->status==1 && $cur_fenxiao_model->isfrozen!=1)
        {

            $result=DB::select()->from('fenxiao_record')->where('type','=',0)->and_where('memberid','=',$order['memberid'])->and_where('orderid','=',$order['id'])->and_where('fxmemberid','=',$order['memberid'])->execute()->current();
            if(!empty($result))
            {
                return;
            }
            $payAmount=0;
            $commission_info = self::get_commission_info_line($order['typeid'], $order['productautoid'],$cur_fenxiao_model->fxgroupid);
            if($commission_info['commission_type']==1)
            {
                $rate = array_pop($commission_info['rate_arr']);
                $payAmount = floatval($consumeAmount * $rate);
            }else
            {
                $cash_info = array_pop($commission_info['cash_arr']);
                if($order['typeid']==1)
                {
                    $payAmount = floatval($cash_info['adult'])*intval($order['dingnum'])+floatval($cash_info['old'])*intval($order['oldnum'])+floatval($cash_info['child'])*intval($order['childnum']);
                }
                else
                {
                    $payAmount = floatval($cash_info['adult'])*intval($order['dingnum']);
                }
            }

            $description ="分销商自购订单<".$order['ordersn'].">交易完成，反佣".$payAmount.'元';
            $deal_result=Model_Member::cash_deal($order['memberid'],$payAmount,0,$description,false,$order['id']);

            if ($deal_result['status']) {
                $fenxiaoRecord = ORM::factory('fenxiao_record');
                $fenxiaoRecord->memberid = $order['memberid'];
                $fenxiaoRecord->type = 0;
                $fenxiaoRecord->amount = $payAmount;
                $fenxiaoRecord->orderid=$order['id'];
                $fenxiaoRecord->fxmembertype=0;
                $fenxiaoRecord->fxmemberid=$order['memberid'];
                $fenxiaoRecord->addtime=$curtime;
                $fenxiaoRecord->save();
            }
        }


    }
    public static function get_commission_info($typeid,$productid,$groupid)
    {
        $commissiontype=Model_Fenxiao_Config::get_val('cfg_fenxiao_commission_type_'.$typeid,$groupid);

        $rateArr=array();
        $cashArr=array();
        if($commissiontype==1) {
            $rateArr = self::get_commission_rate($typeid, $productid,$groupid);
        }
        else {
            $cashArr = self::get_commission_cash($typeid, $productid,$groupid);
        }

        return array('commission_type'=>$commissiontype,'rate_arr'=>$rateArr,'cash_arr'=>$cashArr);

    }
    //获取某一产品的返佣比例
    public static  function get_commission_rate($typeid,$productid,$groupid=0)
    {
        $groupid = empty($groupid)?0:$groupid;
        $configList=ORM::factory('fenxiao_config')->and_where('groupid','=',$groupid)->get_all();
        $first=0;
        $second=0;
        $third=0;
        $last = 0;//表示分销商自身

        $ratioModel=ORM::factory('fenxiao_ratio')->where('typeid','=',$typeid)->and_where('productid','=',$productid)->and_where('groupid','=',$groupid)->find();
        if($ratioModel->loaded())
        {
            $last = floatval($ratioModel->fxratio);
            $last = empty($last)?0:$last/100;

            $first=floatval($ratioModel->fxratio1);
            $first=empty($first)?0:$first/100;

            $second=floatval($ratioModel->fxratio2);
            $second=empty($second)?0:$second/100;

            $third=floatval($ratioModel->fxratio3);
            $third=empty($third)?0:$third/100;
        }

        foreach($configList as $k=>$v)
        {
            if($v['varname']=='cfg_fenxiao_first_ratio_'.$typeid && empty($first))
            {
                $val=floatval($v['value']);
                $first=empty($val)?0:$val/100;
            }
            if($v['varname']=='cfg_fenxiao_second_ratio_'.$typeid  && empty($second))
            {
                $val=floatval($v['value']);
                $second=empty($val)?0:$val/100;
            }
            if($v['varname']=='cfg_fenxiao_third_ratio_'.$typeid && empty($third))
            {
                $val=floatval($v['value']);
                $third=empty($val)?0:$val/100;
            }
            if($v['varname']=='cfg_fenxiao_self_ratio_'.$typeid && empty($last))
            {
                $val=floatval($v['value']);
                $last=empty($val)?0:$val/100;
            }

        }
        return array($first,$second,$third,$last);
    }
    //获取某一产品的返佣金额
    public static function get_commission_cash($typeid,$productid,$groupid=0)
    {
        $groupid = empty($groupid)?0:$groupid;
        $configList=ORM::factory('fenxiao_config')->and_where('groupid','=',$groupid)->get_all();
        $first=0;
        $second=0;
        $third=0;
        $last = 0;

        $ratioModel=ORM::factory('fenxiao_ratio')->where('typeid','=',$typeid)->and_where('productid','=',$productid)->and_where('groupid','=',$groupid)->find();
        if($ratioModel->loaded())
        {
            $first=floatval($ratioModel->cash1);
            $first=empty($first)?0:$first;

            $second=floatval($ratioModel->cash2);
            $second=empty($second)?0:$second;

            $third=floatval($ratioModel->cash3);
            $third=empty($third)?0:$third;

            $last = floatval($ratioModel->cash);
            $last = empty($last)?0:$last;
        }

        foreach($configList as $k=>$v)
        {
            if($v['varname']=='cfg_fenxiao_first_cash_'.$typeid && empty($first))
            {
                $val=floatval($v['value']);
                $first=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_second_cash_'.$typeid  && empty($second))
            {
                $val=floatval($v['value']);
                $second=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_third_cash_'.$typeid && empty($third))
            {
                $val=floatval($v['value']);
                $third=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_self_cash_'.$typeid && empty($last))
            {
                $val=floatval($v['value']);
                $last=empty($val)?0:$val;
            }
        }
        return array($first,$second,$third,$last);
    }

    public static function get_commission_info_line($typeid,$productid,$groupid)
    {
        $commissiontype=Model_Fenxiao_Config::get_val('cfg_fenxiao_commission_type_'.$typeid,$groupid);

        $rateArr=array();
        $cashArr=array();
        if($commissiontype==1) {
            $rateArr = self::get_commission_rate($typeid, $productid,$groupid);
        }
        else {
            $cashArr = self::get_commission_cash_line($typeid, $productid,$groupid);
        }

        return array('commission_type'=>$commissiontype,'rate_arr'=>$rateArr,'cash_arr'=>$cashArr);

    }
    //获取某一产品的返佣金额
    public static function get_commission_cash_line($typeid,$productid,$groupid=0)
    {
        $groupid = empty($groupid)?0:$groupid;
        $configList=ORM::factory('fenxiao_config')->and_where('groupid','=',$groupid)->get_all();
        $first=0;
        $first_old = 0;
        $first_child = 0;
        $second=0;
        $second_old = 0;
        $second_child = 0;
        $third=0;
        $third_old = 0;
        $last = 0;
        $last_old=0;
        $last_child=0;

        $ratioModel=ORM::factory('fenxiao_ratio')->where('typeid','=',$typeid)->and_where('productid','=',$productid)->and_where('groupid','=',$groupid)->find();
        if($ratioModel->loaded())
        {
            $first=floatval($ratioModel->cash1);
            $first=empty($first)?0:$first;
            $first_child=floatval($ratioModel->cash1_child);
            $first_child=empty($first_child)?0:$first_child;
            $first_old=floatval($ratioModel->cash1_old);
            $first_old=empty($first_old)?0:$first_old;
            
            

            $second=floatval($ratioModel->cash2);
            $second=empty($second)?0:$second;
            $second_child=floatval($ratioModel->cash2_child);
            $second_child=empty($second_child)?0:$second_child;
            $second_old=floatval($ratioModel->cash2_old);
            $second_old=empty($second_old)?0:$second_old;
            
            

            $third=floatval($ratioModel->cash3);
            $third=empty($third)?0:$third;
            $third_child=floatval($ratioModel->cash3_child);
            $third_child=empty($third_child)?0:$third_child;
            $third_old=floatval($ratioModel->cash3_old);
            $third_old=empty($third_old)?0:$third_old;
            

            $last = floatval($ratioModel->cash);
            $last = empty($last)?0:$last;
            $last_child=floatval($ratioModel->cash_child);
            $last_child=empty($last_child)?0:$last_child;
            $last_old=floatval($ratioModel->cash_old);
            $last_old=empty($last_old)?0:$last_old;
            
        }

        foreach($configList as $k=>$v)
        {
            if($v['varname']=='cfg_fenxiao_first_cash_'.$typeid && empty($first))
            {
                $val=floatval($v['value']);
                $first=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_first_cash_'.$typeid.'_old' && empty($first_old))
            {
                $val=floatval($v['value']);
                $first_old=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_first_cash_'.$typeid.'_child' && empty($first_child))
            {
                $val=floatval($v['value']);
                $first_child=empty($val)?0:$val;
            }





            if($v['varname']=='cfg_fenxiao_second_cash_'.$typeid  && empty($second))
            {
                $val=floatval($v['value']);
                $second=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_second_cash_'.$typeid.'_old' && empty($second_old))
            {
                $val=floatval($v['value']);
                $second_old=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_second_cash_'.$typeid.'_child' && empty($second_child))
            {
                $val=floatval($v['value']);
                $second_child=empty($val)?0:$val;
            }





            if($v['varname']=='cfg_fenxiao_third_cash_'.$typeid && empty($third))
            {
                $val=floatval($v['value']);
                $third=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_third_cash_'.$typeid.'_old' && empty($third_old))
            {
                $val=floatval($v['value']);
                $third_old=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_third_cash_'.$typeid.'_child' && empty($third_child))
            {
                $val=floatval($v['value']);
                $third_child=empty($val)?0:$val;
            }
            
            
            
            
            if($v['varname']=='cfg_fenxiao_self_cash_'.$typeid && empty($last))
            {
                $val=floatval($v['value']);
                $last=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_self_cash_'.$typeid.'_old' && empty($last_old))
            {
                $val=floatval($v['value']);
                $last_old=empty($val)?0:$val;
            }
            if($v['varname']=='cfg_fenxiao_self_cash_'.$typeid.'_child' && empty($last_child))
            {
                $val=floatval($v['value']);
                $last_child=empty($val)?0:$val;
            }
            
        }
        return array(
            0=>array('adult'=>$first,'old'=>$first_old,'child'=>$first_child),
            1=>array('adult'=>$second,'old'=>$second_old,'child'=>$second_child),
            2=>array('adult'=>$third,'old'=>$third_old,'child'=>$third_child),
            3=>array('adult'=>$last,'old'=>$last_old,'child'=>$last_child)
            );
    }
}