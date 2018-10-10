<?php defined('SYSPATH') or die('No direct script access.');

/**
 * mobile 支付类
 * Class Pay_Mobile
 */
class Pay_Mobile extends Pay_Platform
{
    //平台配置
    private $_conf;
    //模板文件
    public $template;
    //不同版本下获取头部底部
    public $content;

    /**
     * 初始化模板
     */
    public function __construct()
    {
        $this->_conf = Common::C('mobile');
        if (empty($this->template))
        {
            $this->template = Common::C('template_dir') . $this->_conf['template'];
        }
        if (empty($this->content))
        {
            $c_dir = CACHE_DIR . 'payment/common';
            if(!file_exists($c_dir))
            {
                mkdir($c_dir,0777,true);
            }
            $file = $c_dir . '/mobile.php';
            if (file_exists($file))
            {
                $this->content = file_get_contents($file);
            }
            else
            {
                if (!file_exists(dirname($file)))
                {
                    mkdir(dirname($file), 0777, true);
                }
                $this->content = $this->file_get_content(Common::get_main_host() . '/phone/pub/pay');
                file_put_contents($file, $this->content);
            }
        }
    }

    /**
     * 支付方式
     * @return mixed
     */
    public function pay_method()
    {
        $online = array();
        $offline = array();
        $order = array();
        //微信客户端
        $isWxClient = 0;
        $order = array();
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)
        {
            $isWxClient = 1;
        }
        //支持的支付方式
        //$support = explode(',', Common::C('cfg_pay_type'));
        $pay_method = $this->get_pay_method();
        foreach($pay_method as $way)
        {
            //手机版微信支付只能在微信里面才能使用
            if ($isWxClient == 0 && $way['id'] == 8)
            {
                continue;
            }
			
            //添加微信h5支付判断,微信h5支付不能在微信浏览器里操作
            if($isWxClient && $way['pinyin'] == 'wxh5')
            {
                continue;
            }
            //$v['id'] = $k;
            //判断是否是线下支付
            if ($way['id'] != 6)
            {
                $online["{$way['id']}"] = $way;
                $order[] = isset($way['displayorder']) ? $way['displayorder'] : 0;
            }
            else
            {
                $offline["6"] = $way;
            }

        }
       /* foreach ($this->_conf['method'] as $k => $v)
        {
            if (in_array($k, $support) || isset($v['extend']))
            {
                $v['id'] = $k;
                if ($isWxClient == 0 && $k == 8)
                {
                    continue;
                }
                $rs["{$k}"] = $v;
                $order[] = isset($v['order']) ? $v['order'] : 0;
            }
        }*/
        //未开启任何支付
        if (empty($online))
        {
            return;
        }
        //array_multisort($order, SORT_ASC, $online);
        $rs['online'] = $online;
        $rs['offline'] = $offline;
        //默认选择第一个
        $rs['online'][key($rs['online'])]['selected'] = true;
        return $rs;
    }

    /**
     * 模板解析后的html
     * @param $info
     * @return string
     */
    public function html($info)
    {
        $info['cfg_webname'] = Common::C('cfg_webname');
        return $this->status($info);
    }

    /**
     * @function 扩展支付方式,第三方支付应用.
     * @return mixed
     */
    public function get_pay_method()
    {
        $arr = DB::select()
            ->from('payset')
            ->where('isopen','=',1)
            ->and_where_open()
            ->and_where('platform','=',0)
            ->or_where('platform','=',2)
            ->and_where_close()
            ->order_by('displayorder','ASC')
            ->execute()
            ->as_array();
        foreach($arr as &$row)
        {
            if($row['issystem']!=1)
            {
                $row['payurl'] = $GLOBALS['cfg_basehost'].'/'.$row['pinyin'].'/mobile';
            }
            else
            {
                $row['payurl'] = '';
            }
			
        }
        return $arr;

    }
}