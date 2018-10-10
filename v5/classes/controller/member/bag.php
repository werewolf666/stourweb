<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member
 * 会员总控制器
 */
class Controller_Member_Bag extends Stourweb_Controller
{


    private $mid = null;
    private $refer_url = null;
    private $_member = null;

    public function before()
    {
        parent::before();
        $this->refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $GLOBALS['cfg_cmsurl'];
        $this->assign('backurl', $this->refer_url);
        $user = Model_Member::check_login();
        if (!empty($user['mid']))
        {
            $this->mid = $user['mid'];
            $this->_member = $user;
        }
        else
        {
            $this->request->redirect('member/login');
        }
        $this->assign('member',$this->_member);
        $this->assign('mid', $this->mid);
    }
    //日志列表
    public function action_index()
    {
        $pagesize=10;
        $page = $_GET['p'];
        $type = $_GET['type'];

        $route_array = array(
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'p'=>$page
        );
        $params = array('type'=>$type);
        $out = Model_Member_Cash_Log::search_result($this->mid,$page,$pagesize,$params);

        $pager = Pagination::factory(
            array(
                'current_page' => array('source' => 'query_string', 'key' => 'p'),
                'view' => 'default/pagination/search',
                'total_items' => $out['total'],
                'items_per_page' => $pagesize,
                'first_page_in_url' => false
            )
        );

        //配置访问地址 当前控制器方法
        $pager->route_params($route_array);
        $this->assign('pageinfo', $pager);
        $this->assign('list', $out['list']);
        $this->assign('type',$type);
        $this->display('member/money/index');
    }
    //提现
    public function action_withdraw()
    {
        $way = Model_Sysconfig::get_configs(0,'cfg_member_withdraw_way',true);
        $way = explode(',',$way);
        $this->assign('way',$way);
        $this->display('member/money/withdraw');
    }

    //保存
    public function action_ajax_withdraw_save()
    {
        $amount = doubleval($_POST['amount']);
        $amount = floor($amount*100)/100;
        $way = $_POST['way'];
        $prefix = $way!='bank'?$way.'_':'';
        $bankaccountname = $_POST[$prefix.'bankaccountname'];
        $bankcardnumber = $_POST[$prefix.'bankcardnumber'];
        $bankname = $_POST['bankname'];
        $description = $_POST[$prefix.'description'];


        $db = Database::instance();
        $db->begin();
        try
        {
            $curtime = time();
            $useful_money = $this->_member['money']-$this->_member['money_frozen'];
            if($useful_money<$amount)
            {
                throw new Exception('可提现余额不足');
            }
            if($amount<=0)
            {
                throw new Exception('提现金额不得小于0');
            }

            $withdraw_model = ORM::factory('member_withdraw');
            $withdraw_model->memberid = $this->mid;
            $withdraw_model->withdrawamount = $amount;
            $withdraw_model->bankname = $bankname;
            $withdraw_model->bankcardnumber = $bankcardnumber;
            $withdraw_model->bankaccountname = $bankaccountname;
            $withdraw_model->description = $description;
            $withdraw_model->status = 0;
            $withdraw_model->way = $way;
            $withdraw_model->addtime = $curtime;
            $withdraw_model->save();
            if(!$withdraw_model->saved())
            {
                throw new Exception('提现失败，请重试');
            }

            //会员信息修改
            $member_model = ORM::factory('member',$this->mid);
            $member_model->money_frozen+=$amount;
            $member_model->save();
            if(!$member_model->saved())
            {
                throw new Exception('提现失败，请重试');
            }

            $log_des = '提交提现申请后冻结'.$amount.'元';
            Model_Member_Cash_Log::add_log($this->mid,2,$amount,$log_des,array('withdrawid'=>$withdraw_model->id));
            $db->commit();
            echo json_encode(array('status'=>true,'msg'=>'提交申请成功'));
        }
        catch (Exception $excep)
        {
            $db->rollback();
            echo json_encode(array('status'=>false,'msg'=>$excep->getMessage()));
        }
    }
}
