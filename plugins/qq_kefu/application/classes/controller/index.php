<?php

/**
 * Class Controller_Index
 */

include  TOOLS_PATH.'common/sms/smsservice.php';
class Controller_Index extends Stourweb_Controller
{
    //客服配置文件
    private  $_kefu_config_file = NULL;

    private  $_conf = array();
    //初始化设置
    public function before()
    {
        $this->_kefu_config_file = BASEPATH.'/data/config.qq.kefu.php';

        $this->_init_conf();
        parent::before();
    }
    //首页
    public function action_index()
    {
        if ($this->_conf['display'] == 1 && !empty($this->_conf['templet'])) {
            $m = new Model_Qq_Kefu();
            $group = $m->get_qq();
            //全局变量获取

            $glbs = DB::select()->from('sysconfig')->where('webid','=',0)->execute()->as_array();
            $glb=array();
            foreach($glbs as $v)
            {
                $glb[$v['varname']]=$v['value'];
            }
            $this->assign('group', $group);
            $this->assign('conf', $this->_conf);
            $this->assign('Glb', $glb);
            $this->display($this->_conf['templet']);
        }
    }

    //免费客服
    public function action_ajax_add_freekefu()
    {
        $phone=$_POST['phone'];
        if(empty($phone))
        {
            echo json_encode(array('status'=>false,'msg'=>'电话号码不能为空'));
            return;
        }
        if(!preg_match('/^1[123456789]{1}\d{9}$/',$phone))
        {
            echo json_encode(array('status'=>false,'msg'=>'电话号码格式错误空'));
            return;
        }


        $curtime=time();
        $model = ORM::factory('freekefu')->where('phone','=',$phone)->and_where('status','=',0)->find();

        if($model->loaded())
        {
            if($curtime-$model->addtime<30*60)
            {
                echo json_encode(array('status'=>false,'msg'=>'你的请求过于频繁,请稍后重试'));
                return;
            }

        }
        $model->phone=$phone;
        $model->addtime=$curtime;
        $model->ip=Common::get_ip();
        $model->save();
        if($model->saved())
        {
            self::_seed_admin_msg($phone);
            echo json_encode(array('status'=>true,'msg'=>'我们稍后会给您来电，请注意接听'));
        }
        else
        {
            echo json_encode(array('status'=>false,'msg'=>'提交失败,请重试'));
        }
    }

    /**
     * @function 发送管理员短信
     */
    private function _seed_admin_msg($phone)
    {

        $config_data = DB::select()->from('sms_msg')->where('msgtype','=','free_tel_msg')->and_where('isopen','=','1')->execute()->current();
        if(empty($config_data))
        {
            return false;
        }
        $admin_phone = functions::get_sys_para("cfg_webmaster_phone");//管理员号码
        if(empty($admin_phone))
        {
            return false;
        }
        $cfg_webname = functions::get_sys_para("cfg_webname");//网站名称
        $member_id = intval(Cookie::get('st_userid'));
        if($member_id)
        {
            $memberinfo = DB::select('nickname','mobile','email')->from('member')->where('mid','=',$member_id)->execute()->current();
            if($memberinfo)
            {
                $membername = $memberinfo['nickname'] ? $memberinfo['nickname'] : ($memberinfo['mobile'] ? $memberinfo['mobile']:$memberinfo['email']);
            }
            else
            {
                $membername = $phone;
            }
        }
        else
        {
            $membername = $phone;
        }
        $content = $config_data["msg"];
        $content = str_ireplace('{#WEBNAME#}', $cfg_webname, $content);
        $content = str_ireplace('{#FREEPHONE#}', $phone, $content);
        $content = str_ireplace('{#MEMBERNAME#}', $membername, $content);
        SMSService::send_msg($admin_phone,'',$content);
        return true;

    }
    /**
     * 初始化参数
     */
    private function _init_conf()
    {
        if(file_exists($this->_kefu_config_file))
        {
            include_once($this->_kefu_config_file);
            $this->_conf['pos'] = $pos;
            $this->_conf['posh'] = $posh;
            $this->_conf['post'] = $post;
            $this->_conf['display'] = $display;
            $this->_conf['phonenum'] = $phonenum;
            $this->_conf['templet'] = 'tpl'.$qqcl;
            $this->_conf['qqcl']=$qqcl;
        }
        else
        {
            exit();
        }
    }

 

  
}