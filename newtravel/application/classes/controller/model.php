<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: netman
 * QQ: 87482723
 * Time: 15-1-26 下午7:53
 * @description:模型管理控制器
 */
class Controller_Model extends Stourweb_Controller
{

    public function before()
    {
        parent::before();
        $action = $this->request->action();
        if ($action == 'index')
        {
            $param = $this->params['action'];
            $right = array(
                'read' => 'slook',
                'save' => 'smodify',
                'delete' => 'sdelete',
                'update' => 'smodify'
            );
            $user_action = $right[$param];
            if (!empty($user_action))
            {
                Common::getUserRight('model', $user_action);
            }
        }
        if ($action == 'list')
        {
            Common::getUserRight('model', 'slook');
        }
        if ($action == 'add')
        {
            Common::getUserRight('model', 'smodify');
        }
        if ($action == 'ajax_add_save' || $action == 'ajax_edit_save')
        {
            Common::getUserRight('model', 'smodify');
        }
        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);


    }

    public function action_index()
    {
        $action = $this->params['action'];
        if (empty($action))  //列表
        {
            $this->display('stourtravel/model/list');
        }
        else if ($action == 'read')    //读取列表
        {
            $start = Arr::get($_GET, 'start');
            $limit = Arr::get($_GET, 'limit');

            $sql = "select a.* from sline_model a order by a.id asc limit $start,$limit";

            $total = DB::query(Database::SELECT, "select * from sline_model")->execute();

            $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $new_list = array();
            foreach ($list as $k => $v)
            {
                $new_list[] = $v;
            }
            $result['total'] = $total->count();
            $result['lists'] = $new_list;
            $result['success'] = true;

            echo json_encode($result);
        }
        else if ($action == 'save')   //保存字段
        {


        }
        else if ($action == 'update')
        {
            $id = Arr::get($_POST, 'id');
            $field = Arr::get($_POST, 'field');
            $val = Arr::get($_POST, 'val');
            $kindid = Arr::get($_POST, 'kindid');
            $model = ORM::factory('model', $id);
            if ($model->id)
            {
                $model->$field = $val;
                $model->save();
                if ($field == 'isopen')
                {
                    Model_Model::updateNav($id, $val);
                }
                if ($model->saved())
                {
                    Model_Config::clear_cache();
                    echo 'ok';
                }
                else
                {
                    echo 'no';
                }
            }
        }
        else if ($action == 'delete') //删除某个记录
        {
            $rawdata = file_get_contents('php://input');
            $data = json_decode($rawdata);
            $id = $data->id;

            if (is_numeric($id))
            {
                $typdinfo = ORM::factory('model', $id);

                if ($typdinfo->loaded())
                {
                    Model_Model::deleteModel($typdinfo);
                    Model_Config::clear_cache();
                }
            }
        }

    }

    /*
     * 添加模型
     * */
    public function action_add()
    {
        $this->display('stourtravel/model/edit');
    }

    /*
     * 拼音检查是否重复
     * */
    public function action_ajax_pinyin_check()
    {
        $rtn = 'false';
        $py = strtolower(Arr::get($_POST, 'pinyin'));
        $ignore_py_arr = array('line','hotel','car','tuan','spot','visa','jieban','article','wenda','customize','notes','photo','ship','tongyong');
        if (!in_array($py, $ignore_py_arr))
        {
            $count = DB::select('id')->from('model')->where('pinyin', '=', $py)->execute()->count();
            if ($count <= 0)
            {
                //判断目的地pinyin是否重复
                $flag = Model_Destinations::is_pinyin_exist($py);
                $rtn = $flag ? 'false' : 'true';
            }
        }
        exit($rtn);
    }
    public function action_ajax_modulename_check()
    {
        $modulename = $_POST['modulename'];
        $count = DB::select('id')->from('model')->where('modulename', '=', $modulename)->execute()->count();
        echo $count<=0?'true':'false';
    }

    /*
     * 模型保存
     * */
    public function action_ajax_model_save()
    {
        $arr = array(
            'modulename' => Arr::get($_POST, 'modulename'),
            'pinyin' => Arr::get($_POST, 'pinyin')
        );
        $status = Model_Model::createModel($arr);
        Model_Config::clear_cache();
        echo json_encode(array('status' => $status));
    }


}

