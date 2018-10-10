<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 支付类
 * Class Controller_Index
 */
class Controller_Index extends Stourweb_Controller
{
    //支付平台对象
    private $_platFrom;
    //模板目录
    private $_templte;
    //错误信息
    private $_error;
    //错误提示
    const ORDERSN_ERROR = '订单错误';
    const ORDERSN_FORMAT_ERROR = '格式错误';
    const ORDERSN_NOT_EXISTS = '订单不存在';
    const ORDERSN_PAYED = '订单已支付';
    const TOKEN_ERROR = '口令错误';
    const POST_ERROR = '提交异常数据';

    /**
     * 初始化支付对象
     */
    public function before()
    {
        parent::before();
        Common::C('base_url', $GLOBALS['base_url'] . Common::C('base_url'));
        $platFromClass = 'Pay_' . ucfirst(Common::C('platform'));
        $this->_platFrom = new $platFromClass();
        $this->_templte = Common::C('template_dir');
    }

    /**
     * 支付页显示
     * URI:/payment/ $_POST数据
     */
    public function action_index()
    {

        $ordersn = $_REQUEST['ordersn'];
        $this->_ordersn_checked($ordersn);
        //支付模板
        $view = View::factory($this->_platFrom->template);
        //支付方式
        $view->pay_method = $this->_platFrom->pay_method();



        //动态口令
        $view->__token__ = Common::C('token_on') ? Common::token() : false;
        //订单信息
        $view->info = Model_Member_Order::info($ordersn);




        //"0元"订单
        if ($view->info['total'] == 0)
        {
            St_Payment::zero_pay($ordersn);
        }
        //编号
        $view->number = St_Product::product_series($view->info['productautoid'],$view->info['typeid']);
        $view = str_replace(array('<stourweb_title/>', '<stourweb_pay_content/>'), array($view->info['status']!=0?'确认订单':'查看订单', $view->render()), $this->_platFrom->content);
        $this->response->body($view);
    }

    /**
     * 支付确认
     */
    public function action_confirm()
    {

        //支付宝微信客户端
        if (Common::C('platform') == 'mobile' && $_GET['method'] == 1 && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)
        {
            $view = View::factory($this->_templte . "mobile/alipay_wxclient");
            $view = str_replace(array('<stourweb_pay_content/>', '<stourweb_title/>', '确认订单', '产品支付'), array($view->render(), '支付宝微信端支付', '订单支付', '订单支付'), $this->_platFrom->content);
            exit($view);
        }
        //根据支付方式选择
        $this->_ordersn_checked($_GET['ordersn']);
        //支付数据格式化
        $info = Model_Member_Order::info($_GET['ordersn']);
        $platFrom = Common::C('platform');
        $conf = Common::C($platFrom);
        $className = 'Pay_' . ucfirst($platFrom) . '_' . $conf['method'][$_GET['method']]['en'];
        //实列化对象
        $obj = new $className();
        $isWx = $_GET['method'] == '8' ? 1 : 0;
        switch ($isWx)
        {
            //微信支付
            case 1:
                if ($platFrom == 'pc')
                {
                    //PC微信扫码支付
                    $html = $obj->submit($info);
                    if ($html != false)
                    {
                        $view = str_replace(array('<stourweb_pay_content/>', '<stourweb_title/>'), array($html, '微信扫码支付'), $this->_platFrom->content);
                        $this->response->body($view);
                    }
                }
                else
                {
                    //mobile 微信公众号
                    $arr = $obj->submit($info);
                    $view = View::factory($arr['template']);
                    $view->parameter = $arr['parameter'];
                    $view->productname = $arr['productname'];
                    $view->total_fee = $arr['total_fee'];
                    $view->addtime = date('Y-m-d H:i:s',$info['addtime']);
                    $view->ordersn = $info['ordersn'];
                    $this->response->body($view);
                }
                break;
            case 0:
                $obj->submit($info);
                break;
        }
    }

    /**
     * 检测订单号是否正确
     * @param $ordersn
     * @return bool
     */
    private function _ordersn_checked($ordersn)
    {
        $bool = false;
        $info['ordersn'] = $ordersn;
        $order_info = Model_Member_Order::order_info($ordersn);
        if (!preg_match('~^\d+$~', $ordersn))
        {
            //订单号格式错误
            $info['sign'] = 25;
            new Pay_Exception("订单{$ordersn}" . self::ORDERSN_FORMAT_ERROR);
        }
        else if (Model_Member_Order::not_exists($ordersn))
        {
            //订单不存在
            $info['sign'] = 26;
            new Pay_Exception("订单{$ordersn} " . self::ORDERSN_NOT_EXISTS);
        }
        else if (Model_Member_Order::payed($ordersn))
        {
            //订单已支付
            $info['sign'] = 24;
            new Pay_Exception("订单{$ordersn} " . self::ORDERSN_PAYED);
        }
        else if ($order_info['status']!=1)
        {
            //只有等待支付订单才能进行支付.
            $info['sign'] = 27;
            new Pay_Exception("订单{$ordersn} " . self::POST_ERROR);
        }
        else
        {
            $bool = true;
        }
        //订单号错误提示
        if (!$bool)
        {
            Common::pay_status($info);
        }
    }

    /**
     * AJAX 检测是否支付
     */
    public function action_ajax_ispay()
    {
        $result = array(
            'result' => false
        );
        if (preg_match('~^\d+$~', $_POST['ordersn']) && Model_Member_Order::payed($_POST['ordersn']))
        {
            $result['result'] = true;
        }
        echo json_encode($result);
    }
}