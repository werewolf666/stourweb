<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PC支付类
 * Class Pay_Pc
 */
class Pay_Pc extends Pay_Platform
{
    //平台配置
    private $_conf;
    //模板文件
    public $template;
    //通用部分(头、底部)
    public $content;

    /**
     * 初始化模板
     */
    public function __construct()
    {
        $this->_conf = Common::C('pc');
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
            $file = $c_dir . '/pc.php';
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
                $this->content = $this->file_get_content(Common::get_main_host() . '/pub/pay');
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
        $pay_method = $this->get_pay_method();
        foreach($pay_method as $way)
        {
            //$v['id'] = $k;
            //判断是否是线下支付
            if ($way['id'] != 6)
            {
                $online["{$way['id']}"] = $way;
                $order[] = isset($way['dispalyorder']) ? $way['displayorder'] : 0;
            }
            else
            {
                $offline["6"] = $way;
            }
        }
        /*
         *
         * 原来读取支付方式代码
         * */
       /* $support = explode(',', Common::C('cfg_pay_type'));
        foreach ($this->_conf['method'] as $k => $v)
        {
            if (in_array($k, $support) || isset($v['extend']))
            {
                $v['id'] = $k;
                if ($k != 6)
                {
                    $online["{$k}"] = $v;
                    $order[] = isset($v['order']) ? $v['order'] : 0;
                }
                else
                {
                    $line["{$k}"] = $v;
                }
            }
        }*/
        //未开启任何支付
        if (empty($online) && empty($offline))
        {
            return;
        }
        //线下支付
        $rs['offline'] = $offline;
        //线上支付
        $rs['online'] = $online;
        empty($rs['online']) ? ($rs['offline']['6']['selected'] = true) : ($rs['online'][key($rs['online'])]['selected'] = true);
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
        return str_replace(array('<stourweb_title/>', '<stourweb_pay_content/>'), array($info['title'], $this->status($info)), $this->content);
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
            ->and_where('id','!=',1)
            ->and_where_open()
            ->and_where('platform','=',1)
            ->or_where('platform','=',0)
            ->and_where_close()
            ->order_by('displayorder','ASC')
            ->execute()
            ->as_array();
        foreach($arr as &$row)
        {
            if($row['issystem']!=1)
            {
                $row['payurl'] = $GLOBALS['cfg_basehost'].'/'.$row['pinyin'].'/pc';
            }
            else
            {
                $row['payurl'] = '';
            }
        }
        return $arr;

    }
}