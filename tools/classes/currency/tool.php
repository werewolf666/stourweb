<?php
/*
 * 货币工具体，仅包含静态函数，用于当前网站的具体调用
 */
class Currency_Tool{
    private static $defaultCurrencyCode='CNY';
    private static $configs=null;
    private static $currencyObj=null;
    private static $backCurrencyObj=null;
    public static function get_config()
    {

        if(empty(self::$configs))
        {
            $fields = array('cfg_front_currencycode', 'cfg_back_currencycode', 'cfg_front_currency_precise');

            $list = DB::select()->from('sysconfig')->where('varname','in',$fields)->execute()->as_array(); // ORM::factory('sysconfig')->where('varname', 'in', $fields)->get_all();
            $arr = array('cfg_front_currencycode'=>'', 'cfg_back_currencycode'=>'', 'cfg_front_currency_precise'=>0);
            foreach ($list as $v) {
                $arr[$v['varname']] = $v['value'];
            }
            if(empty($arr['cfg_front_currencycode'])||empty($arr['cfg_back_currencycode'])) {
                $arr['cfg_front_currencycode'] = self::$defaultCurrencyCode;
                $arr['cfg_back_currencycode'] = self::$defaultCurrencyCode;
            }
            $arr['cfg_front_currency_precise']=empty($arr['cfg_front_currency_precise'])?0:$arr['cfg_front_currency_precise'];
            self::$configs = $arr;
        }
        return self::$configs;
    }

    /**@desc 获取货币符号
     * @return char
     */
    public static function symbol()
    {
        $currencyObj=self::get_currency_obj();
        return $currencyObj->get_symbol();
    }

    public static function get_currency_obj()
    {
        $configs=self::get_config();
        if(empty(self::$currencyObj)) {
            self::$currencyObj = Currency_St::factory($configs['cfg_front_currencycode']);
        }
        return self::$currencyObj;
    }
    public static function price($price)
    {
        $configs=self::get_config();
        $currencyObj=self::get_currency_obj();
        $priceTemp=$currencyObj->get_exchange($configs['cfg_back_currencycode'],$price,false,$configs['cfg_front_currency_precise']);
        if(empty($priceTemp))
            return $price;
        return $priceTemp;
    }

    public static function get_back_currency_obj()
    {
        $configs=self::get_config();
        if(empty(self::$backCurrencyObj)) {
            self::$backCurrencyObj = Currency_St::factory($configs['cfg_back_currencycode']);
        }
        return self::$backCurrencyObj;
    }
    public static function back_symbol()
    {
        $backCurrencyObj=self::get_back_currency_obj();
        return $backCurrencyObj->get_symbol();
    }

}