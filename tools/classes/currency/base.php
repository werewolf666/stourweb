<?php

abstract class Currency_Base
{
    protected $_code;  //货币代码或者唯一标识符,不同的平台可以不一样
    protected $_infomation=array();  //货币附加信息

    public function __construct($code)
    {
        $this->_code=$code;
        $this->_infomation['code']=$code;
        $infomation=$this->attach_infomation($code);
        if(is_array($infomation))
            $this->_infomation=array_merge($this->_infomation,$infomation);
    }
    public function get_code()
    {
        return $this->_code;
    }

    /**@desc 获取货币汇率比值
     * @param $currencyCode
     * @return array  带有两个数值的数组，其中第一个数字表示当前货币比值，第二个数字表示$currencyCode的比值
     */
    abstract public function get_ratio_units($currencyCode);
    public function get_ratio($currencyCode,$ispositive=true)
    {
         if($this->get_code()==$currencyCode)
             return 1;
         $radioUnits=$this->get_ratio_units($currencyCode);
         if(!$radioUnits)
             return false;
         $ratio1=doubleval($radioUnits[0]);
         $ratio2=doubleval($radioUnits[1]);
         if($ratio1==0||$ratio2==0)
             return false;
         if($ispositive)
             return $ratio1/$ratio2;
        else
             return $ratio2/$ratio1;
     }

    /**@desc 获取实际价格
     * @param $currencyCode
     * @param $money
     * @param $precise   结果精度，如果为false,表示不
     * @param bool $ispositive 如果为true,表示将当前货币换算成$currencyCode的货币，反之也然
     * @return number
     */
     public function get_exchange($currencyCode,$money,$ispositive=true,$precise=false)
     {
         $money=doubleval($money);
         $ratio=$this->get_ratio($currencyCode,!$ispositive);
         if(!$ratio||$money==0)
           return false;
         $exchange=$money*$ratio;
         if($precise===false)
             return $exchange;
         $precise=intval($precise);

         if($precise<=0)
         {
             $tempExchange=floor($exchange);
         }
         else {
             $tempExchange = floor($exchange * pow(10,$precise));
             $tempExchange = $tempExchange / pow(10,$precise);
         }
         return $tempExchange;
     }
    /**
     * @desc 设置当前货币的附加信息，请返回一个关联数组.
     * @return array
     */
    abstract protected function attach_infomation($code);

    /**
     * @desc 获取货币符号
     * @return char
     */
    abstract public function get_symbol();

    public function __set($key,$value)
    {
        $this->_infomation[$key]=$value;
    }
    public function __get($key)
    {
        if(isset($this->_infomation[$key]))
            return $this->_infomation[$key];
        else
            return null;
    }
}
