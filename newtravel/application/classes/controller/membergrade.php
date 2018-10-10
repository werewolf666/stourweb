<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Membergrade extends Stourweb_Controller
{

    public function before()
    {
        parent::before();

        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
        $this->assign('weblist', Common::getWebList());
    }

    /**
     * 广告列表
     */
    public function action_index()
    {
        $action = is_null($this->params['action']) ? 'null' : $this->params['action'];
        switch ($action)
        {
            case 'null':
                $this->display('stourtravel/membergrade/list');
                break;
            case 'read':
                $start = Arr::get($_GET, 'start');
                $limit = Arr::get($_GET, 'limit');
                $keyword = Arr::get($_GET, 'keyword');
                $sort = json_decode(Arr::get($_GET, 'sort'), true);
                $where = '1=1';
                if (!empty($keyword))
                {
                    $where .= " and (a.name like '%{$keyword}%')";
                }
                $order = 'order by a.begin asc';
                $sql = "select a.id,a.name,a.begin,a.end from sline_member_grade as a where {$where} {$order} limit {$start},{$limit}";
                $totalcount_arr = DB::query(Database::SELECT, "select a.*,count(0) AS num from sline_member_grade as a where {$where}")->execute()->as_array();
                $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
                foreach ($list as $k => &$v)
                {
                    $v['section'] = $v['begin'].'~'.$v['end'];
                    $where = 'jifen>='.$v['begin'].' AND jifen<='.$v['end'];
                    $member_num = DB::query(Database::SELECT, "select count(0) AS num from sline_member as a where {$where}")->execute()->as_array();
                    $v['member_num'] = $member_num[0]['num'];
                }
                $result['total'] = $totalcount_arr[0]['num'];
                $result['lists'] = $list;
                $result['success'] = true;
                echo json_encode($result);
                break;
            case 'delete':
                $rawdata = file_get_contents('php://input');
                $data = json_decode($rawdata);
                $id = $data->id;
                if (is_numeric($id))
                {
                    $sql = "delete from sline_member_grade where id={$id}";
                    $total_rows = DB::query(Database::DELETE, $sql)->execute();
                    echo $total_rows > 0 ? 'ok' : 'no';
                }
                break;
        }
    }

    /**
     * 添加
     */
    public function action_add()
    {
        $this->assign('action', 'add');
        $this->assign('title', '会员等级');
        $this->display('stourtravel/membergrade/edit');
    }

    /**
     * 修改广告
     */
    public function action_edit()
    {
        $id = $this->params['id'];
        $info = DB::query(Database::SELECT, "select a.* from sline_member_grade AS a where a.id={$id}")->execute()->as_array();
        $info = $info[0];

        $this->assign('info', $info);
        $this->assign('action', 'edit');
        $this->assign('title', '修改会员等级');
        $this->display('stourtravel/membergrade/edit');
    }


    /**
     * ajax保存广告
     */
    public function action_ajax_save()
    {
        $status = false;

        $action = $_POST['action'];
        $id = intval($_POST['id']);

        $data_arr = NULL;
        $data_arr['name'] = trim($_POST['name']);
        $data_arr['begin'] = intval($_POST['begin']);
        $data_arr['end'] = intval($_POST['end']);

        //填写积分区间有效性检测

        $where = '(('.$data_arr['begin'].'>=begin AND '.$data_arr['begin'].'<=end) OR ('.$data_arr['end'].'>=begin AND '.$data_arr['end'].'<=end))';
        if($id > 0)
        {
            $where .=  ' AND id<>'.$id;
        }
        $member_num = DB::query(Database::SELECT, "select count(0) AS num from sline_member_grade as a where {$where}")->execute()->as_array();
        if ($member_num[0]['num'] > 0)
        {
            echo json_encode(array('status' => false,'msg'=>"积分区间有重叠"));
            exit;
        }
        else
        {
            if ($action == 'add' && empty($id))
            {
                $data_arr['addtime'] = time();
                $result = DB::insert('member_grade', array_keys($data_arr))->values(array_values($data_arr))->execute();
                if (is_array($result))
                {
                    $id = $result[0];
                    $status = true;
                }
            }
            else
            {
                $sql = array();
                $data_arr['modtime'] = time();

                foreach ($data_arr as $k => $v)
                {
                    array_push($sql, $k . "='{$v}'");
                }
                $sql = implode(',', $sql);
                $sql = "UPDATE `sline_member_grade` SET {$sql} WHERE `id` = {$id}";
                $result = DB::query(3, $sql)->execute();
                if ($result)
                {
                    $status = true;
                }
            }
            echo json_encode(array('status' => $status));
        }
    }

}