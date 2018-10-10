<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Help 帮助中心
 */
class Controller_Server extends Stourweb_Controller
{
    public function action_index()
    {

        $aid = $this->request->param('aid');

        $info = ORM::factory('serverlist')
            ->where("aid=:aid")
            ->param(':aid',$aid)
            ->find()
            ->as_array();

        $this->assign('info',$info);
        $this->display('server/index','footer_show');
    }
}