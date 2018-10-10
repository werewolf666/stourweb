<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Databackup extends Stourweb_Controller
{

    public function before()
    {
        parent::before();

        $this->assign('cmsurl', URL::site());
    }

    /*
       首页
    */
    public function action_index()
    {
        $this->display('stourtravel/databackup/list');
    }

    /*
       备份
    */
    public function action_ajax_do_databackup()
    {
        set_time_limit(0);

        $result = array("status" => 0, "msg" => "");

        $databackModel = new Model_Backup();
        $databackModel->backupAll();

        $result["status"] = 1;
        echo json_encode($result);
    }


    /*
     * 数据备份列表
     */
    public function action_ajax_databackup_read()
    {
        //当前页
        $page = Arr::get($_GET, 'page');
        //分页数
        $page_size = Arr::get($_GET, 'limit');


        $backuppath = BASEPATH . '/data/backup';
        $back_arr = $this->get_dir_zip_files($backuppath);
        rsort($back_arr);

        $list = array();
        foreach ($back_arr as $k => $v)
        {
            $list[] = array(
                "id" => pathinfo($v, PATHINFO_FILENAME),
                "name" => "DataBackup" . date("YmdHis", pathinfo($v, PATHINFO_BASENAME)),
                "time" => date("Y-m-d H:i:s", pathinfo($v, PATHINFO_BASENAME)),
                "size" => Model_Upgrade3Api::format_bytes(filesize($backuppath . "/" . $v))
            );
        }

        //分页
        $total = count($list);
        $start_index = ($page - 1) * $page_size;
        $end_index = ($start_index + $page_size) - 1;
        for ($index = 0; $index < $total; $index++)
        {
            if ($index < $start_index || $index > $end_index)
            {
                unset($list[$index]);
            }
        }
        $list = array_values($list);

        $result['success'] = true;
        $result['total'] = $total;
        $result['list'] = $list;

        echo json_encode($result);
    }

    private function get_dir_zip_files($dir)
    {
        $objects = array();

        $handler = opendir($dir);
        while ($file = readdir($handler))
        {
            $fullpath = $dir . '/' . $file;
            if (is_file($fullpath) && strtolower(pathinfo($fullpath, PATHINFO_EXTENSION)) == "zip")
            {
                array_push($objects, $file);
            }
        }
        closedir($handler);

        $objects = array_diff($objects, array('.', '..'));

        return $objects;
    }


    public function action_ajax_delete_databackup()
    {
        $databackup_list = Arr::get($_POST, 'databackup_list');

        if (count($databackup_list) > 0)
        {
            foreach ($databackup_list as $databackup)
            {
                unlink(BASEPATH . '/data/backup/' . $databackup["id"] . ".zip");
            }
        }

        echo json_encode(array("status" => 1, "msg" => ""));
    }

}