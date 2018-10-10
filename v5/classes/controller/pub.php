<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Pub extends Stourweb_Controller
{
    /*
     * 公共请求控制器,此控制器不能删除.
     *
     * */

    public function before()
    {
        parent::before();

    }

    //请求CSS资源,合并输出
    public function action_css()
    {


        $this->response->headers('Content-Type', 'text/css');
        $this->response->headers('charset', 'utf-8');

        if (isset($_GET['file']))
        {
            $files = explode(",", $_GET['file']);
            $fc = '';
            foreach ($files as $val)
            {
                $fc .= file_get_contents(DOCROOT . $val);
            }
            //$fc = self::replace_note($fc);
            $fc = str_replace("\/t", "", $fc);
            $fc = str_replace("\/n", "", $fc);
            $fc = str_replace("\/r\/n", "", $fc);
            echo $fc;
        }
    }

    //请求js资源,合并输出
    public function action_js()
    {
        //输出JS

        $this->response->headers('Content-Type', 'application/x-javascript');
        $this->response->headers('charset', 'utf-8');
        if (isset($_GET['file']))
        {
            $files = explode(",", $_GET['file']);
            $str = '';
            foreach ($files as $val)
            {
                $str .= file_get_contents(DOCROOT . $val);
            }
            //$str = self::replace_note($str);
            $str = str_replace("\/t", "", $str);
            $str = str_replace("\/n", "", $str);
            //$str = preg_replace('#\/\/[^\n]*#','',$str);//行注释
            echo $str;
        }
    }

    /*
     * 网站头部
     * */
    public function action_header()
    {
        $uid = Cookie::get('st_userid');
        $loginname = Cookie::get('st_username');
        $searchModel = Model_Model::get_search_model();
        $this->assign('loginname', $loginname);
        $this->assign('searchmodel', $searchModel);
        $this->assign('uid', $uid);
        $is_search = $this->params['issearch'];
        if($is_search==1)
        {
            $this->display('pub/search_header');
        }
        else
        {
            $templet = Product::get_use_templet('header');
            $templet = $templet ? $templet : 'pub/header';
            $this->display($templet);
        }

    }

    /*
     * 网站底部
     * */
    public function action_footer()
    {
        foreach ($this->params as $k => $v)
        {
            $this->assign($k, $v);
        }
        $templet = Product::get_use_templet('footer');
        $templet = $templet ? $templet : 'pub/footer';
        $this->display($templet);

    }

    public function action_flink()
    {

        foreach ($this->params as $k => $v)
        {
            $this->assign($k, $v);
        }
        $templet = Product::get_use_templet('flink');
        $templet = $templet ? $templet : 'pub/flink';
        $this->display($templet);
    }

    /*
     * 帮助
     * */
    public function action_help()
    {
        foreach ($this->params as $k => $v)
        {
            $this->assign($k, $v);
        }
        $templet = Product::get_use_templet('help');
        $templet = $templet ? $templet : 'pub/help';
        $this->display($templet);
    }

    public function action_pay()
    {
        $this->display('pub/pay');
    }

    //显示详情
    public function action_status()
    {
        $statusToken = $_POST['_status_token_'];
        unset($_POST['_status_token_']);
        $dbInfo = Kohana::$config->load('database.default');
        $suffix = implode(',', array_values($dbInfo['connection']));
        foreach ($_POST as $k => $v)
        {
            $suffix .= "{$k}=$v";
        }
        $status = $statusToken == md5($suffix) ? $_POST : array('result' => false, 'referurl' => $this->cmsurl, 'msgtitle' => '提示信息', 'msg' => '该页面不能直接访问');
        if (!isset($status['title']))
        {
            $status['title'] = '提示信息';
        }
        if (is_array(unserialize($status['msg'])))
        {
            $status['msg'] = unserialize($status['msg']);
        }
        $status['indexurl'] = $this->cmsurl;
        $this->assign('status', $status);
        $this->display('pub/status');
    }

    /**
     * 添加浏览次数
     */
    public function action_ajax_add_shownum()
    {
        $typeid = intval(Arr::get($_GET, 'typeid'));
        $aid = Common::remove_xss(Arr::get($_GET, 'productid'));
        if ($typeid)
        {
            Product::update_click_rate($aid, $typeid);
        }
    }

    /**
     * 添加提问
     */
    public function action_ajax_add_question()
    {
        if (!$this->request->is_ajax()) exit();
        $checkcode = Arr::get($_POST, 'checkcode');
        $productid = Arr::get($_POST, 'productid');
        $nickname = Arr::get($_POST, 'nickname');
        $content = Arr::get($_POST, 'content');
        $typeid = Arr::get($_POST, 'typeid');
        $questype = Arr::get($_POST, 'questype');
        $mobile = Arr::get($_POST, 'mobile');
        //验证码
        $checkcode = strtolower($checkcode);
        if (!Captcha::valid($checkcode))
        {
            echo 1; //验证码错误
            exit;
        }
        if (!preg_match('~^(\+?0?86\-?)?1[345789]\d{9}$~', $mobile))
        {
            echo 2; //手机号格式错误
            exit;
        }
        $ip = Common::get_ip();
        $nickname = $nickname ? $nickname : '匿名';
        $memberId = Cookie::get('st_userid') ? Cookie::get('st_userid') : '0';
        $m = ORM::factory('question');
        $m->productid = $productid;
        $m->content = $content;
        $m->typeid = $typeid;
        $m->content = $content;
        $m->nickname = $nickname;
        $m->ip = $ip;
        $m->addtime = time();
        $m->memberid = $memberId;
        $m->kindlist = '';
        $m->questype = $questype;
        $m->phone = $mobile;
        $m->save();
        if ($m->saved())
        {
            $model_info = Model_Model::get_module_info(10);
            $jifen = Model_Jifen::reward_jifen('sys_write_' . $model_info['pinyin'], $memberId);
            if (!empty($jifen))
            {
                St_Product::add_jifen_log($memberId, '发布' . $model_info['modulename'] . '送积分' . $jifen, $jifen, 2);
            }
            echo 3;
            exit;
        }


    }

    /**
     * 添加评论
     */
    public function action_ajax_add_comment()
    {
        if (!$this->request->is_ajax()) exit();
        $checkcode = Common::remove_xss(Arr::get($_POST, 'checkcode'));
        $productid = Common::remove_xss(Arr::get($_POST, 'productid'));

        $content = Common::remove_xss(Arr::get($_POST, 'content'));
        $typeid = Common::remove_xss(Arr::get($_POST, 'typeid'));
        //验证码
        $checkcode = strtolower($checkcode);
        if (!Captcha::valid($checkcode))
        {
            echo 1; //验证码错误
            exit;
        }
        $memberId = Cookie::get('st_userid') ? Cookie::get('st_userid') : '0';
        $m = ORM::factory('comment');
        $m->articleid = $productid;
        $m->content = $content;
        $m->typeid = $typeid;
        $m->memberid = $memberId;
        $m->addtime = time();
        $m->save();
        if ($m->saved())
        {
            echo 3;
            exit;
        }


    }

    /*
     * 验证验证码是否正确
     * */
    public function action_ajax_check_code()
    {
        $flag = 'false';
        $checkcode = strtolower(Arr::get($_POST, 'checkcode'));
        if (Captcha::valid($checkcode))
        {
            $flag = 'true';
        }
        echo $flag;
    }

    /**
     * 附件下载
     */
    public function action_download()
    {
        $file_name = $_GET['name'];
        $path = $_GET['file'];
        $full_path = BASEPATH . $path;
        $full_path = str_replace('..', '', $full_path);
        $full_path = str_replace('\\', '/', $full_path);
        $full_path = str_replace('//', '/', $full_path);
        if (strpos($full_path, 'uploads') === false || !file_exists($full_path) || empty($file_name))
        {
            exit('文件路径或名称错误!');
        }
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-Type:application/force-download");
        header("Content-Disposition:attachment;filename={$file_name}");
        header("Accept-Length:" . filesize($full_path));
        readfile($full_path);
    }

    public function action_commit_fenxiao()
    {
        //file_put_contents('xxxx.txt',$_GET['ordersn']);
        $new_order = Model_Member_Order::order_info($_GET['ordersn']);
        if(!empty($new_order))
        {
            Plugin_Core_Factory::factory()->add_listener('on_orderstatus_changed', $new_order)->execute();
        }
    }

    /**
     * 取消超时未支付的订单
     */
    public function action_cancel_unpay_order()
    {
        $order_type_list = explode(",", trim($_GET['order_type']));
        $process_order_model_list = explode(",", trim($_GET['process_order_model']));
        $timeout = intval(trim($_GET['timeout']));
        if ($timeout < 15)
        {
            $timeout = 15;
        }
        $log = trim($_GET['log']);
        if (in_array($log, array('true', 'false')))
        {
            if ($log == 'true')
            {
                $log = true;
            } else
            {
                $log = false;
            }
        } else
        {
            $log = false;
        }

        $result = "";
        if (count($order_type_list) != count($process_order_model_list))
        {
            $result = "错误：订单类型与订单处理模型的数量不匹配";
            if ($log)
            {
                Kohana::$log->add(Kohana_Log::INFO, $result);
            }
            exit($result);
        }

        foreach ($process_order_model_list as $process_order_model)
        {
            if (!class_exists($process_order_model) || !method_exists($process_order_model, "storage"))
            {
                $result = "错误：处理模型类 {$process_order_model} 或方法 storage 不存在";
                if ($log)
                {
                    Kohana::$log->add(Kohana_Log::INFO, $result);
                }
                exit($result);
            }
        }

        $order_type_process_mapping = array();
        for ($index = 0; $index < count($order_type_list); $index++)
        {
            $order_type_process_mapping[$order_type_list[$index]] = $process_order_model_list[$index];
        }

        $sql = "SELECT
	id,
	ordersn,
	typeid,
	STATUS,
	from_unixtime(addtime) AS addtime_h
FROM
	sline_member_order
WHERE
	STATUS = 1
AND (unix_timestamp() - addtime) > ({$timeout} * 60)";

        $order_list = DB::query(DataBase::SELECT, $sql)->execute()->as_array();
        if (count($order_list) <= 0)
        {
            $result = "没有超过 {$timeout} 分钟未支付的订单";
            if ($log)
            {
                Kohana::$log->add(Kohana_Log::INFO, $result);
            }
            exit($result);
        }

        foreach ($order_list as $order_info)
        {
            if (empty($order_info['typeid']) || !array_key_exists($order_info['typeid'], $order_type_process_mapping))
            {
                $result .= "没有找到类型为 {$order_info['typeid']} 的订单 {$order_info['ordersn']} 的处理模型" . PHP_EOL;
                continue;
            }

            $result .= "订单 {$order_info['ordersn']} 创建时间 {$order_info['addtime_h']}，仍未支付，准备取消" . PHP_EOL;

            $process_order_model = $order_type_process_mapping[$order_info['typeid']];

            $model = ORM::factory('member_order', $order_info['id']);
            $oldstatus = $model->status; //原来状态.
            $newstatus = 3;
            $model->remark = "订单超过 {$timeout} 分钟未支付，已被系统取消";
            $model->status = $newstatus;
            $model->update();

            if ($model->saved())
            {
                $result .= "修改订单 {$order_info['ordersn']} 的状态成功" . PHP_EOL;

                $order = DB::select()->from('member_order')->where('id', '=', $order_info['id'])->execute()->current();
                if (Model_Member_Order::back_order_status_changed($oldstatus, $order, $process_order_model))
                {
                    $result .= "订单 {$order_info['ordersn']} 的库存还原成功" . PHP_EOL;
                } else
                {
                    $result .= "错误：订单 {$order_info['ordersn']} 的库存还原失败" . PHP_EOL;
                }
            } else
            {
                $result .= "错误：修改订单 {$order_info['ordersn']} 的状态失败" . PHP_EOL;
            }
        }

        if ($log)
        {
            Kohana::$log->add(Kohana_Log::INFO, $result);
        }
        echo $result;
    }
}