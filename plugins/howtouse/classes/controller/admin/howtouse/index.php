<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Howtouse_Index extends Stourweb_Controller
{

    public function before()
    {
        parent::before();
        $this->assign('cmsurl', URL::site());
    }

    public function action_index()
    {
        $step1_menu = $this->get_menu_data("首页设置", "", "config", "base");
        $this->assign("step1_menu", $step1_menu);

        $step2_menu = $this->get_menu_data("全局目的地", "", "destination", "destination");
        $this->assign("step2_menu", $step2_menu);

        $step3_menu = $this->get_menu_data("全局出发地", "", "startplace", "index");
        $this->assign("step3_menu", $step3_menu);

        $step4_menu = $this->get_menu_data("支付宝", "", "payset", "alipay");
        $this->assign("step4_menu", $step4_menu);

        $step5_menu = $this->get_menu_data("短信接口", "", "sms", "index");
        $this->assign("step5_menu", $step5_menu);


        $this->display('admin/howtouse/index');
    }

    public function action_prompt()
    {
        $first_usage_guide_menu = $this->get_menu_data("新手教程", "howtouse/admin", "index", "index");
        if (is_array($first_usage_guide_menu))
        {
            $this->assign("first_usage_guide_menu", $first_usage_guide_menu);
            $this->display('admin/howtouse/prompt');

        } else
        {
            exit($first_usage_guide_menu);
        }
    }

    public function action_ajax_first_usage_guide_menu()
    {
        $first_usage_guide_menu = $this->get_menu_data("新手教程", "howtouse/admin", "index", "index");
        if (is_array($first_usage_guide_menu))
        {
            echo json_encode(array('status' => 1, 'data' => $first_usage_guide_menu));

        } else
        {
            echo json_encode(array('status' => 0, 'msg' => $first_usage_guide_menu));
        }

    }

    private function get_menu_data($title, $directory, $controller, $method)
    {
        $sql = "select * from sline_menu_new where 1=1 ";
        if ($title)
        {
            $sql .= " and title='{$title}' ";
        }
        if ($directory)
        {
            $sql .= " and directory='{$directory}' ";
        }
        if ($controller)
        {
            $sql .= " and controller='{$controller}' ";
        }
        if ($method)
        {
            $sql .= " and method='{$method}' ";
        }

        $first_usage_guide_menu = DB::query(DataBase::SELECT, $sql)
            ->execute()
            ->current();

        if (empty($first_usage_guide_menu))
        {
            return "不能找到需要的菜单数据";
        } else
        {
            return $first_usage_guide_menu;
        }
    }

}