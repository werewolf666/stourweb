<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Finance extends Stourweb_Controller
{
    public function before()
    {
        parent::before();

        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
    }

    public function action_index()
    {

        $this->display('stourtravel/finance/drawcash_list');
    }

    public function action_ajax_get_drawcash_list()
    {

        $drawcashModel = new Model_Finance_Drawcash();

        $pageno = intval(Arr::get($_GET, 'page'));
        $pageno = $pageno <= 0 ? 1 : $pageno;
        $pagesize = intval(Arr::get($_GET, 'limit'));
        $sort = json_decode(Common::remove_xss(Arr::get($_GET, 'sort')));
        if (is_null($sort))
        {
            $drawcashData = $drawcashModel->get_all_drawcash('addtime', 'DESC', $pageno, $pagesize);
        } else
        {
            $drawcashData = $drawcashModel->get_all_drawcash($sort[0]->property, $sort[0]->direction, $pageno, $pagesize);
        }

        $result['total'] = $drawcashData['rowcount'];
        $result['lists'] = $drawcashData['list'];;
        $result['success'] = true;

        echo json_encode($result);
    }


    public function action_drawcash_detail()
    {
        $applyid = Common::remove_xss($this->params['applyid']);
        $applyusertype = Common::remove_xss($this->params['applyusertype']);

        $drawcashModel = new Model_Finance_Drawcash();
        $drawcashData = $drawcashModel->get_drawcash_detail($applyusertype, $applyid);

        $this->assign('info', $drawcashData[0]);
        $this->display('stourtravel/finance/drawcash_detail');
    }

    public function action_config()
    {
        $cfg_member_withdraw_way = Model_Sysconfig::get_configs(0,'cfg_member_withdraw_way',true);
        $cfg_member_withdraw_way = explode(',',$cfg_member_withdraw_way);
        $this->assign('cfg_member_withdraw_way',$cfg_member_withdraw_way);
        $this->display('stourtravel/finance/config');
    }

    public function action_ajax_config_save()
    {
        $cfg_member_withdraw_way = $_POST['cfg_member_withdraw_way'];
        $cfg_member_withdraw_way = implode(',',$cfg_member_withdraw_way);
        $result = array('webid'=>0);
        $result['cfg_member_withdraw_way'] = $cfg_member_withdraw_way;
        Model_Sysconfig::save_config($result);
        echo json_encode(array('status'=>1,'msg'=>'保存成功'));
    }

    public function action_ajax_auditing_drawcash_apply()
    {
        $applyid = Common::remove_xss(Arr::get($_POST, 'applyid'));
        $applyusertype = Common::remove_xss(Arr::get($_POST, 'applyusertype'));
        $status = Common::remove_xss(Arr::get($_POST, 'status'));
        $audit_description = Common::remove_xss(Arr::get($_POST, 'audit_description'));


        $drawcashModel = new Model_Finance_Drawcash();
        $drawcashModel->auditing_drawcash_apply($applyusertype, $applyid, $status, $audit_description);

        echo json_encode(array('status' => true));
    }


}