<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Member_Order_Log extends ORM
{
    private static $_descriptions = array(
        '0'=>'您提交了订单，请等待商家确认订单',
        '01'=>'您的订单已通过商家确认，等待您付款',
        '1'=>'您提交了订单，等待您付款',
        '2'=>'您已付款成功，请等待消费',
        '3'=>'您的订单已成功取消',
        '4'=>'退款申请已通过，请留意退款到账通知',//'您已成功提交退款申请，您的退款商家正在确认中，请稍后',
        '5'=>'您的订单已成功消费，请对产品进行评价',
        '6'=>'退款请求提交成功，等待商家确认',
        '62'=>'您提交取消退款申请，商家同意您的请求',

    );

    /**
     * @function 添加订单状态日志
     * @param $order 订单数组
     * @param null 前订单状态
     * @param bool 是否强制使用默认的描述
     * @param string $description 自定义描述
     * @return bool
     */
    public static function add_log($order,$prev_status=NULL,$defaulted=false,$description='')
    {
        $table_name = DB::select('maintable')->from('model')->where('id','=',$order['typeid'])->execute()->get('maintable');
        if(empty($table_name))
            return false;
        $main_table = 'Model_'.ucfirst($table_name);
        if (!$defaulted && class_exists($main_table) && method_exists($main_table, 'add_order_log'))
        {
            return call_user_func(array($main_table,'add_order_log'),$order,$prev_status);
        }
        if($prev_status==$order['status'] && $prev_status!==NULL)
        {
            return false;
        }
        $description =  empty($description)?self::get_description($order['status'],$prev_status):$description;
        $model = ORM::factory('member_order_log');
        $model->addtime = time();
        $model->orderid = $order['id'];
        $model->prev_status = $prev_status;
        $model->current_status = $order['status'];
        $model->description = $description;
        $model->save();
        return $model->saved();
    }

    /**
     * @function 获取某个订单的日志列表
     * @param $orderid 订单id
     * @return array
     */
    public static function get_list($orderid)
    {
        return DB::select()->from('member_order_log')->where('orderid','=',$orderid)->order_by('addtime','desc')->execute()->as_array();
    }

    /**
     * @function 获取某个订单状态的默认描述文字
     * @param $status 订单当前状态
     * @param null $prev_status 订单的先前状态
     * @return string
     */
    public static function get_description($status,$prev_status=NULL)
    {
        $key=$status==1?$prev_status.$status:$status;
        //撤销退款
        if($status==2&&$prev_status==6)
        {
            $key = 62;
        }
        return self::$_descriptions[$key];
    }
}