<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 会员收货地址
 * Class Controller_Member_Receive
 */
class Controller_Member_Receive extends Stourweb_Controller
{
    public function before()
    {
        parent::before();
        $this->member = Common::session('member');
        $order_query_token = Common::session('order_query_token', null);
        if (empty($this->member) && empty($order_query_token))
        {
            Common::message(array('message' => __('unlogin'), 'jumpUrl' => $this->cmsurl . 'member/login'));
        }
    }

    public function action_address()
    {
        $this->display('member/receive/address_manage');
    }

    //获取更多地址
    public function action_ajax_more()
    {
        $page = intval($_GET['page']);
        $page = $page < 1 ? 1 : $page;
        $pageSize = 10;
        $list = DB::select()->from('member_address')->where('memberid', '=', $this->member['mid'])->limit($pageSize)->offset(($page - 1) * $pageSize)->execute()->as_array();
        foreach ($list as &$v)
        {
            $v['url'] = $this->cmsurl . "member/receive/update?id={$v['id']}";
        }
        echo Product::list_search_format($list, $page, $pageSize);
    }

    //更改默认地址
    public function action_ajax_default()
    {
        $row = DB::update('member_address')->set(array('is_default' => 1))->where('memberid', '=', $this->member['mid'])->and_where('id', '=', intval($_POST['id']))->execute();
        if ($row)
        {
            DB::update('member_address')->set(array('is_default' => 0))->where('memberid', '=', $this->member['mid'])->and_where('id', '!=', intval($_POST['id']))->execute();
        }
    }

    //删除地址
    public function action_ajax_delete_address()
    {
        $row = DB::delete('member_address')->where('memberid', '=', $this->member['mid'])->and_where('id', '=', intval($_POST['id']))->execute();
        echo $row ? 1 : 0;
    }

    //编辑地址
    public function action_update()
    {
        $info = array();
        if (isset($_GET['id']))
        {
            $info = DB::select()->from('member_address')->where('memberid', '=', $this->member['mid'])->and_where('id', '=', intval($_GET['id']))->execute()->current();
        }
        $title = $info ? '编辑' : '新增';
        $this->assign('title', $title);
        $this->assign('info', $info);
        $this->display('member/receive/address_edit');
    }

    //保存地址
    public function action_ajax_save()
    {
        $data['bool'] = 0;
        $_POST = Common::remove_xss($_POST);
        if (empty($_POST['id']))
        {
            $_POST['memberid'] = $this->member['mid'];
            list($id, $rows) = DB::insert('member_address', array_keys($_POST))->values(array_values($_POST))->execute();
            if ($rows > 0)
            {
                $data['bool'] = 1;
                $data['status'] = 1;
                $data['msg'] = __('success_add');
                //更新默认
                if ($_POST['is_default'] == 1)
                {
                    DB::update('member_address')->set(array('is_default' => 0))->where('id', '!=', $id)->execute();
                }
            }
            else
            {
                $data['msg'] = __('error_add');
            }
        }
        else
        {
            $id = $_POST['id'];
            unset($_POST['id']);
            $rows = DB::update('member_address')->set($_POST)->where("id={$id} and memberid={$this->member['mid']}")->execute();
            if ($rows > 0)
            {
                //更新默认
                if ($_POST['is_default'] == 1)
                {
                    DB::update('member_address')->set(array('is_default' => 0))->where('id', '!=', $id)->execute();
                }
                $data['bool'] = 1;
                $data['status'] = 1;
                $data['msg'] = __('success_edit');
            }
            else
            {

                $data['msg'] = __('error_edit');
            }
        }
        echo json_encode($data);
    }

    //其他产品引入
    public function action_select()
    {
        $defaultAddress = DB::select()->from('member_address')->where('memberid', '=', $this->member['mid'])->and_where('is_default', '=', 1)->execute()->current();
        $this->assign('address', $defaultAddress);
        $this->display('member/receive/address_select');
    }

    public function action_select_list()
    {
        $defaultAddress = DB::select()->from('member_address')->where('memberid', '=', $this->member['mid'])->execute()->as_array();
        $this->assign('address', $defaultAddress);
        $this->display('member/receive/address_select_list');
    }
}