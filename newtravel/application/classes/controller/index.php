<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Stourweb_Controller
{

    public function before()
    {
        parent::before();
        $this->assign('cmsurl', URL::site());

    }

    //后台首页(框架)
    public function action_index()
    {


        $configinfo = ORM::factory('sysconfig')->getConfig(0);
        $addmodule = Model_Model::getAllModule();
        $sysconfigModel = new Model_Sysconfig();
        $sysconfigModel->saveConfig(array('webid' => 0, 'cfg_admin_dirname' => $GLOBALS['cfg_backdir']));
        $menu = Common::getConfig('menu_sub');
        $this->assign('menu', $menu);
        $session = Session::instance();
        $uname = ORM::factory('admin', $session->get('userid'))->get('username');
        $admin_litpic = DB::select('litpic')->from('admin')->where('id','=',$session->get('userid'))->execute()->get('litpic');
        $rolename = ORM::factory('role', $session->get('roleid'))->get('rolename');
        $this->assign('username', $uname);
        $this->assign('admin_litpic',$admin_litpic);
        $this->assign('rolename', $rolename);
        $this->assign('addmodule', $addmodule);
        $this->assign('configinfo', $configinfo);
        $this->display('stourtravel/index/index');
    }

    //5.1-6.0
    public function action_index5()
    {

        $starttime = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));
        $endtime = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - date("w") + 7, date("Y")));
        $addmodule = Model_Model::getAllModule();
        $session = Session::instance();
        $uname = ORM::factory('admin', $session->get('userid'))->get('username');
        $admin_litpic = DB::select('litpic')->from('admin')->where('id','=',$session->get('userid'))->execute()->get('litpic');
        $rolename = ORM::factory('role', $session->get('roleid'))->get('rolename');
        $menu = Common::getConfig('menu_sub');
        $this->assign('majorVersion', Model_SystemParts::getCoreMajorVersion());
        $this->assign('year', date('Y'));
        $this->assign('menu', $menu);
        $this->assign('addmodule', $addmodule);
        $this->assign('username', $uname);
        $this->assign('admin_litpic',$admin_litpic);
        $this->assign('rolename', $rolename);
        $this->assign('starttime', $starttime);
        $this->assign('endtime', $endtime);
        //后台模板
        $templet = Common::get_back_template();
        $this->display($templet);
    }

    //新版首页
    public function action_index7()
    {

        $starttime = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));
        $endtime = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - date("w") + 7, date("Y")));
        $addmodule = Model_Model::getAllModule();
        $session = Session::instance();
        $uname = ORM::factory('admin', $session->get('userid'))->get('username');
        $admin_litpic = DB::select('litpic')->from('admin')->where('id','=',$session->get('userid'))->execute()->get('litpic');
        $rolename = ORM::factory('role', $session->get('roleid'))->get('rolename');
        $menu = Common::getConfig('menu_sub');
        $this->assign('majorVersion', Model_SystemParts::getCoreMajorVersion());
        $this->assign('year', date('Y'));
        $this->assign('menu', $menu);
        $this->assign('addmodule', $addmodule);
        $this->assign('username', $uname);
        $this->assign('admin_litpic',$admin_litpic);
        $this->assign('rolename', $rolename);
        $this->assign('starttime', $starttime);
        $this->assign('endtime', $endtime);
        //后台模板
        $templet = 'stourtravel/index/index_7';
        $this->display($templet);
    }



    //弹出框,移动端浏览
    public function action_dialog_mobile()
    {
        $mobile_url = Model_Sysconfig::get_configs(0, 'cfg_m_main_url',true);
        if($mobile_url)
        {
            if($mobile_url == $GLOBALS['cfg_basehost'])
            {
                $mobile_url.='/phone';
            }

        }
        else
        {
            $mobile_url = $GLOBALS['cfg_basehost'].'/phone';
        }

        $this->assign('mobile_url',$mobile_url);
        $this->display('stourtravel/index/dialog_mobile');

    }
    //弹框联系我们
    public function action_dialog_link()
    {
        $this->display('stourtravel/index/dialog_link');
    }
    //弹框未授权
    public function action_dialog_unauthorize()
    {
        $this->display('stourtravel/index/dialog_unauthorize');
    }



    /*
      * 删除缓存
      * */
    public function action_ajax_clearcache()
    {
        Model_Config::clear_cache();
        echo 'ok';
    }


    //订单数量(产品栏目展示)
    public function action_ajax_order_num()
    {

        $arr = ORM::factory('model')->where("isopen=1")->and_where('id','not in',DB::expr('(14,11)'))->get_all();

        $webids_arr = DB::select('id')->from('destinations')->where('iswebsite','=',1)->execute()->as_array();
        $webids=array(0);
        foreach($webids_arr as $web)
        {
            $webids[]=$web['id'];
        }


        $out = array();
        foreach ($arr as $row)
        {
            if ($row['pinyin'] == 'insurance'&&$row['id']!=111)
            {
                $sql = "select count(*) as num from sline_insurance_booking";
                $sql2 = "select count(*) as num from sline_insurance_booking where viewstatus=0";
                $ar = DB::query(1, $sql)->execute()->as_array();
                $ar2 = DB::query(1, $sql2)->execute()->as_array();
                $count = $ar[0]['num'];
                $count2 = $ar2[0]['num'];
                $out[] = array(
                    'md' => $row['pinyin'],
                    'typeid' => '7',
                    'num' => $count,
                    'comment_num' => 0,
                    'question_num' => 0,
                    'question_unans_num' =>0,
                    'unviewnum' => $count2
                );

                continue;
            }
            $sql = "select count(*) as num from sline_member_order a left join sline_member b on a.memberid=b.mid  where a.typeid='" . $row['id'] . "' and b.virtual!=2 and a.webid in (".implode(',',$webids).")";
            $sql2 = "select count(*) as num from sline_member_order  a left join sline_member b on a.memberid=b.mid where a.typeid='" . $row['id'] . "' and a.viewstatus=0 and b.virtual!=2 and a.webid in (".implode(',',$webids).")";

            $ar = DB::query(1, $sql)->execute()->as_array();
            $ar2 = DB::query(1, $sql2)->execute()->as_array();
            $count = $ar[0]['num'];
            $count2 = $ar2[0]['num'];
            $out[] = array(
                'md' => $row['pinyin'],
                'typeid' => $row['id'],
                'num' => $count,
                'unviewnum' => $count2,
                'comment_num' => Model_Comment::get_comment_num_bytypeid($row['id']),
                'comment_uncheck_num' => Model_Comment::get_comment_uncheck_num($row['id']),

                'question_num' => Model_Question::get_question_num($row['id']),
                'question_unans_num' => Model_Question::get_question_unans_num($row['id']),


            );

        }
        //自定义订单


        //私人定制定单
        if(St_Functions::is_model_exist(14))
        {
            $count = 0;
            $count2 = 0;
            $sql = "select count(*) as num from sline_customize";
            $sql2 = "select count(*) as num from sline_customize where viewstatus=0";
            $row = DB::query(1, $sql)->execute()->as_array();
            $row2 = DB::query(1, $sql2)->execute()->as_array();
            $count = $row[0]['num'];
            $count2 = $row2[0]['num'];
            $out[] = array(
                'md' => 'customize',
                'typeid'=>'14',
                'num' => $count,
                'unviewnum' => $count2,
                'comment_num' => 0,
                'question_num' => 0,
                'question_unans_num' => 0,
                'comment_uncheck_num' => 0
            );
        }
        if(St_Functions::is_model_exist(11))
        {
            //结伴定单
            $sql = "select count(*) as num from sline_jieban";
            $sql2 = "select count(*) as num from sline_jieban where status=0";
            $row = DB::query(1, $sql)->execute()->current();
            $row2 = DB::query(1, $sql2)->execute()->current();
            $out[] = array(
                'md' => 'jieban',
                'typeid'=>'11',
                'num' => $row['num'],
                'unviewnum' => $row2['num'],
                'comment_num' => 0,
                'question_num' => 0,
                'question_unans_num' => 0,
                'comment_uncheck_num' => 0
            );
        }
        //问答总数
        $sql = "select count(*) as num from sline_question";
        $sql2 = "select count(*) as num from sline_question where status=0";
        $row = DB::query(1, $sql)->execute()->current();
        $row2 = DB::query(1, $sql2)->execute()->current();
        $out[] = array(
            'md' => 'question',
            'typeid'=>'10',
            'num' => 0,
            'unviewnum' => 0,
            'comment_num' => 0,
            'question_num' => $row['num'],
            'question_unans_num' => $row2['num'],
            'comment_uncheck_num' => 0
        );
        //免费通话未处理

        $channel_call_untreated_num = DB::select(DB::expr('count(*) as num'))->from('freekefu')->where('status','=',0)->execute()->get('num');
        $out[] = array(
            'channel_call_untreated_num'=>$channel_call_untreated_num
        );

        echo json_encode($out);
    }

    //订单数量(图表展示)
    public function action_ajax_order_num_graph()
    {
        $model = DB::select(DB::expr(" id, modulename as title, pinyin "))
            ->from('model')
            ->where('is_orderable','=',1)
            ->order_by('id', 'asc')
            ->execute()
            ->as_array('id');
        $out = array();
        $starttime = strtotime(Arr::get($_POST, 'starttime'));
        $endtime = strtotime(Arr::get($_POST, 'endtime'));
        $labels = $this->getLabel($starttime, $endtime);
        foreach ($model as $key => $info)
        {
            $where = "and typeid='$key'";
            $sql = "select count(*) as num, FROM_UNIXTIME(addtime, '%Y-%m-%d') AS statistic_date " .
                " from sline_member_order where addtime>='$starttime' and addtime<='$endtime' $where  " .
                "GROUP BY statistic_date ASC";
            $ar = DB::query(1, $sql)->execute()->as_array();
            $statistic = $data = array();
            foreach ($ar as $val)
            {
                $statistic[$val['statistic_date']] = $val['num'];
            }
            foreach ($labels as $lab)
            {
                $data[] = isset($statistic[$lab]) ? $statistic[$lab] : 0;
            }
            $out[$info['pinyin']] = $data;
        }

        $out['attribute']['labels'] = $labels;
        $out['attribute']['models'] = $model;

        echo json_encode($out);
    }

    //会员统计
    public function action_ajax_member_num()
    {
        $out = array();
        $starttime = strtotime(Arr::get($_POST, 'starttime'));
        $endtime = strtotime(Arr::get($_POST, 'endtime'));
        $labels = $this->getLabel($starttime, $endtime);

        //统计新增会员
        $sql = "select  count(*) as num,FROM_UNIXTIME(jointime, '%Y-%m-%d') AS statistic_date from sline_member  where jointime>=$starttime and jointime<=$endtime group by statistic_date";
        $ar = DB::query(1, $sql)->execute()->as_array();
        $statistic = $data = array();
        foreach ($ar as $val)
        {
            $statistic[$val['statistic_date']] = $val['num'];
        }
        foreach ($labels as $lab)
        {
            $out[] = isset($statistic[$lab]) ? $statistic[$lab] : 0;
        }


        echo json_encode(array('member' => $out, 'labels' => $labels));


    }

    //清理过期访问日志
    public function action_ajax_clear_log()
    {
        $query = DB::delete('user_log')->where('logtime', '<', time() - 60 * 60 * 24 * 7);
        $query->execute();
        echo 'ok';
    }

    /*
     * 获取label
     * */
    public function getLabel($starttime, $endtime)
    {
        $label = array();
        for ($i = $starttime; $i <= $endtime; $i = $i + 60 * 60 * 24)
        {
            $lable[] = date('Y-m-d', $i);
        }
        return $lable;
    }







}