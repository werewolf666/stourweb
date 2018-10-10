<?php
/**
 *  思途版汇率计算的具体实现
 */
class Currency_St extends Currency_Base{

    public static function factory($code)
    {
        return new self($code);
    }

    public function get_ratio_units($currencyCode)
    {
        if($this->get_code()==$currencyCode)
            return array(1,1);
        $sql="select * from sline_currency_rate where (currencycode1=:code1 and currencycode2=:code2) or (currencycode1=:code2 and currencycode2=:code1) limit 1";
        $tempArr=DB::query(Database::SELECT,$sql)->parameters(array(':code1'=>$this->get_code(),':code2'=>$currencyCode))->execute()->as_array();
        if(empty($tempArr)||count($tempArr)<1)
            return false;
        $rateInfo=$tempArr[0];
        $rateInfo['ratio1']=doubleval($rateInfo['ratio1']);
        $rateInfo['ratio2']=doubleval($rateInfo['ratio2']);
        if($rateInfo['currencycode1']==$this->get_code())
            return array($rateInfo['ratio1'],$rateInfo['ratio2']);
        else
            return array($rateInfo['ratio2'],$rateInfo['ratio1']);
    }
    public function attach_infomation($code)
    {
        //$model=ORM::factory('currency')->where('code','=',$code)->find();
        $model = DB::select()->from('currency')->where('code','=',$code)->execute()->current();
        return $model;
        //if($model->loaded())
         //   return $model->as_array();
        // TODO: Implement setInfomation() method.
    }

    public function get_symbol()
    {
        return $this->symbol;
        // TODO: Implement getSymbol() method.
    }
}