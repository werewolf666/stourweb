<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Search extends Stourweb_Controller{

    private  $_cache_key = '';
    public function before()
    {
        parent::before();
        //检查缓存
        $this->_cache_key = Common::get_current_url();
        $html = Common::cache('get',$this->_cache_key);
        if(!empty($html))
        {
            echo $html;
            exit;
        }


    }
    //订单搜索
    public function action_order()
    {
        Common::session('_platform', 'pc');
        $mobile = Common::remove_xss(Arr::get($_GET,'mobile'));
        if(!preg_match("/^[0-9]{11}$/", $mobile)){
            $mobile='';
        }

        $page = intval(Arr::get($_GET,'p'));
        $page = $page  ? $page : 0;
        $pagesize = 10;
        if(!empty($mobile))
        {
            $out = Model_Member_Order::order_list(0,'','all',$page,10,$mobile);
            $route_array = array(
                'controller' => $this->request->controller(),
                'action' => $this->request->action()
            );
            $pager = Pagination::factory(
                array(

                    'current_page' => array('source' => 'query_string', 'key' => 'p'),
                    'view'=>'default/pagination/search',
                    'total_items' => $out['total'],
                    'items_per_page' => $pagesize,
                    'first_page_in_url' => false,
                )
            );
            $pager->route_params($route_array);
            //配置访问地址 当前控制器方法

            $this->assign('list',$out['list']);
            $this->assign('pageinfo',$pager);
        }
        //token
        $token = md5(time());
        Common::session('crsf_code',$token);
        $this->assign('frmcode',$token);
        $this->assign('mobile',$mobile);

        $this->display('search/order');
        //缓存内容
        $content = $this->response->body();
        Common::cache('set',$this->_cache_key,$content);
    }

    //云搜索
    public function action_cloudsearch()
    {
        $keyword = St_String::filter_mark(Arr::get($_GET,'keyword'));
        $typeid = intval(Arr::get($_GET,'typeid'));
        $typeid = $typeid ? $typeid : 0;
        if(empty($keyword))
        {
            $this->request->redirect($this->request->referrer());
        }
        $leftnav = Model_Search::get_left_nav($keyword);
		//关键字入库
        Model_Search::add_search_key($keyword);
        $this->assign('keyword',htmlspecialchars($keyword));
        $this->assign('leftnav',$leftnav);

        $route_array = array(
            'controller'=>$this->request->controller(),
            'action'=>$this->request->action(),
            'typeid'=>$typeid
        );
        $pagesize = 10;
        $p = intval(Arr::get($_GET,'p'));

        $out = Model_Search::search_result($route_array,$keyword,$p,$pagesize);
        $pager = Pagination::factory(
            array(

                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view'=>'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pagesize,
                'first_page_in_url' => false,
            )
        );
        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('list',$out['list']);
        $this->assign('total',$out['total']);
        $this->assign('typeid',$typeid);
        $this->assign('pageinfo',$pager);
        $templet = Product::get_use_templet('cloudsearch_index');
        $templet = $templet ? $templet : 'search/cloudsearch';
        $this->display($templet);


    }

    public function action_ajax_send_msgcode()
    {

        $mobile =  St_Filter::remove_xss(Arr::get($_POST,'mobile'));//手机
        $pcode =  St_Filter::remove_xss(Arr::get($_POST,'pcode'));//验证码
        $token =  St_Filter::remove_xss(Arr::get($_POST,'token'));//
        $curtime=time();

        //安全校验码验证
        $orgCode = Common::session('crsf_code');
        if($orgCode!=$token)
        {
            echo json_encode(array('status'=>false,'msg'=>'检验码错误'));
            exit;
        }

        //验证码验证
        if(!Captcha::valid($pcode) || empty($pcode))
        {
            echo json_encode(array('status'=>false,'msg'=>'图片验证码错误'));
            exit;
        }
        Common::session('captcha_response','');

        //手机号验证
        if(empty($mobile))
        {
            echo json_encode(array('status'=>false,'msg'=>'手机号不能为空'));
            exit;
        }
        else
        {
            $sentNum = Common::session('sendnum_'.$mobile); //已发验证码次数
            $lastSentTime = Common::session('senttime_'.$mobile);//上次发送时间
            $sentNum = empty($sentNum) ? 0 : $sentNum;
            $lastSentTime=empty($lastSentTime)?0:$lastSentTime;

            if($sentNum<3&&$sentNum>0&&$lastSentTime>($curtime-60))
            {
                echo json_encode(array('status'=>false,'msg'=>'验证码发送过于频繁，请稍后再试'));
                exit;
            }

            if($sentNum>=3&&$lastSentTime>($curtime-60*15))
            {
                echo json_encode(array('status'=>false,'msg'=>'验证码发送过于频繁，15分钟后再试'));
                exit;
            }

            $code =  Common::get_rand_code(5);//验证码

            $content = "当前手机短信验证码为:{$code},请输入验证";
            $flag = Product::send_msg($mobile,'',$content);

            if($flag->Success)//发送成功
            {

                Common::session('senttime_'.$mobile,$curtime);
                $sentNum=$sentNum>=3?0:$sentNum+1;
                Common::session('sendnum_'.$mobile,$sentNum);
                Common::session('mobilecode_'.$mobile,$code);
                echo json_encode(array('status'=>true,'msg'=>'验证码发送成功'));
            }
            else
            {
                echo json_encode(array('status'=>false,'msg'=> $flag->Message . '导致发送失败'));
            }

        }

    }
    public function action_ajax_check_msgcode()
    {
        $msgCode = Arr::get($_POST,'checkcode');
        $mobile = Arr::get($_POST,'mobile');
        $flag = 'false';
        if(Common::session('mobilecode_'.$mobile) == $msgCode)
        {
            $flag = 'true';
            // Common::session('mobilecode_'.$mobile,null);
        }
        echo $flag;

    }






}