<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Advertise5x extends Stourweb_Controller
{
    /*
     * 广告总控制器
     * */
    public static $rollAd = array(
        'IndexSpotRollingAd',
        'HotelRollingAd',
        'PerformRollingAd',
        'ProductRollingAd',
        'SelftripRollingAd',
        'SpotRollingAd',
        'SpotSuitAd',
        'IndexRollingAd',
        'LineRollingAd',
        'NewsRollingAd'
    );

    public function before()
    {
        parent::before();
        //系统版本
        $version = Model_Sysconfig::system_version();
        if ($version['cfg_pc_version'] == 0 && $version['cfg_mobile_version'] == 0)
        {
            $this->request->redirect('/advertise/index/parentkey/sale/itemid/1');
        }
        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
        $this->assign('weblist', Common::getWebList());
        $this->assign('isfive', $version['cfg_pc_version'] > 0 && $version['cfg_mobile_version'] > 0);
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
                $this->display('stourtravel/advertise5x/list');
                break;
            case 'read':
                $start = Arr::get($_GET, 'start');
                $limit = Arr::get($_GET, 'limit');
                $keyword = Arr::get($_GET, 'keyword');
                $prefix = Arr::get($_GET, 'adtype');
                $webid = Arr::get($_GET, 'webid');
                $sort = json_decode(Arr::get($_GET, 'sort'), true);
                $ismobile = Arr::get($_GET, 'ismobile');
                if (!empty($webid))
                {
                    $where = "a.webid={$webid}";
                }
                else
                {
                    $where = 'a.webid=0';
                }
                $where .= " and is_use=1 ";
                if (!empty($keyword))
                {
                    $keyword = preg_replace('~^c_~', '0_', $keyword);
                    $keyword = preg_replace('~^s_~', '1_', $keyword);
                    $where .= " and (a.position like '%{$keyword}%' or a.custom_label like '%{$keyword}%' or adname like '%{$keyword}%')";
                }
                $order = 'order by p.id asc,a.is_system asc,a.number asc';
                $where .= !empty($prefix) ? " and a.prefix='{$prefix}'" : '';
                if (strlen($ismobile) > 0)
                {
                    $ispc = $ismobile == 1 ? 0 : 1;
                    $where .= " and a.is_pc='{$ispc}'";
                }
                $sql = "select distinct  a.*,p.kindname,concat(a.is_system,'_',a.prefix,'_',a.number) as adId from sline_advertise_5x as a left join sline_page as p on a.prefix=p.pagename having {$where} {$order} limit {$start},{$limit}";
                $total = DB::query(Database::SELECT, "select distinct  a.*,p.kindname,concat(a.is_system,'_',a.prefix,'_',a.number) as adId from sline_advertise_5x as a left join sline_page as p on a.prefix=p.pagename having {$where}")->execute()->as_array();
                $list = DB::query(Database::SELECT, $sql)->execute()->as_array();

                $newlist = array();
                foreach ($list as $k => $v)
                {
                    $v['prefix'] = (int)$v['is_system'] == 1 ? 's_' . $v['prefix'] : 'c_' . $v['prefix'];
                    $v['webid'] = Model_Advertise_5x::site($v['webid']);
                    $adsrc = unserialize($v['adsrc']);
                    $adlink = unserialize($v['adlink']);
                    $adname = unserialize($v['adname']);
                    $adorder = unserialize($v['adorder']);
                    $newlist[] = $v;
                    if (is_array($adsrc))
                    {
                        $child_ids = array();
                        $prent_id = $v['id'];
                        foreach ($adsrc as $adk => $adv)
                        {
                            $add = array();
                            $add['id'] = 'ad_' . $v['id'] . '_' . $adk;
                            $add['ad_id'] = $v['id'];
                            $add['ad_src'] = $adv;
                            $add['ad_index'] = $adk;
                            $add['isuse'] = $v['isuse'];
                            if (is_array($adlink))
                            {
                                $add['ad_link'] = $adlink[$adk];
                            }
                            if (is_array($adname))
                            {
                                $add['ad_name'] = $adname[$adk];
                            }
                            if (is_array($adorder))
                            {
                                $add['ad_order'] = $adorder[$adk];
                            }
                            array_push($child_ids, $add['id']);
                            $newlist[] = $add;
                        }
                        if ($child_ids)
                        {
                            foreach ($newlist as $_k => $item)
                            {
                                if ($item['id'] == $prent_id)
                                {
                                    $newlist[$_k]['child'] = $child_ids;
                                    break;
                                }
                            }
                        }
                    }
                }
                $result['total'] = count($total);
                $result['lists'] = $newlist;
                $result['success'] = true;
                echo json_encode($result);
                break;
            case 'delete':
                $rawdata = file_get_contents('php://input');
                $data = json_decode($rawdata);
                $id = $data->id;
                if (is_numeric($id))
                {
                    $data = array(
                        'adorder' => serialize(array()),
                        'adname' => serialize(array()),
                        'adlink' => serialize(array()),
                        'adsrc' => serialize(array())
                    );
                    DB::update('advertise_5x')->set($data)->where('id', '=', $id)->execute();
                }
                else
                {
                    //删除广告图片
                    $index = $data->ad_index;
                    $id = $data->ad_id;
                    $data = DB::select()->from('advertise_5x')->where('id', '=', $id)->execute()->current();
                    if ($data)
                    {
                        $adorder = unserialize($data['adorder']);
                        $adname = unserialize($data['adname']);
                        $adlink = unserialize($data['adlink']);
                        $adsrc = unserialize($data['adsrc']);
                        if (isset($adorder[$index]))
                        {
                            unset($adorder[$index]);
                        }
                        if (isset($adname[$index]))
                        {
                            unset($adname[$index]);
                        }
                        if (isset($adlink[$index]))
                        {
                            unset($adlink[$index]);
                        }
                        if (isset($adsrc[$index]))
                        {
                            unset($adsrc[$index]);
                        }
                        //序列化
                        $adorder = serialize($adorder);
                        $adname = serialize($adname);
                        $adlink = serialize($adlink);
                        $adsrc = serialize($adsrc);
                        DB::update('advertise_5x')->set(
                            array(
                                'adorder' => $adorder,
                                'adname' => $adname,
                                'adlink' => $adlink,
                                'adsrc' => $adsrc)
                        )->where('id', '=', $id)->execute();
                    }
                }
                break;
        }
    }

    public function action_update()
    {
        $id = Arr::get($_POST, 'id');
        $field = Arr::get($_POST, 'field');
        $index = Arr::get($_POST, 'index');
        $val = Arr::get($_POST, 'val');
        $data = DB::select()->from('advertise_5x')->where('id', '=', $id)->execute()->current();
        $rows = 0;
        if (!empty($data))
        {
            switch ($field)
            {
                case  'ad_order':
                    $ad_order = unserialize($data['adorder']);
                    if (!$ad_order)
                    {
                        $ad_order = array();
                    }
                    $ad_order[$index] = $val;
                    $ad_order = serialize($ad_order);
                    DB::update('advertise_5x')->set(array('adorder' => $ad_order))->where('id', '=', $id)->execute();
                    break;
            }
        }
        echo 'ok';
    }

    /**
     * 添加广告
     */
    public function action_add()
    {

        $app_api = new Model_AppApi();
        $my_templet_list_result = $app_api->get_my_templet_list();
        $templet_info_list = $app_api->templet_data_format($my_templet_list_result->data->data, false, array('page' => 1, 'pageSize' => 10000));
        $template = array();
        foreach ($templet_info_list['data'] as $item)
        {
            array_push($template, array('name' => $item['name'], 'handle_advertise_name' => $item['handle_advertise_name']));
        }
        $data = DB::select('id', 'is_system', 'prefix', 'number', 'position', 'webid', 'flag', 'is_pc', 'size', 'remark')->from('advertise_5x')->or_where_open()->where('adsrc', 'in', DB::expr('("","a:1:{i:0;s:0:"";}","N;","a:0:{}")'))->or_where('adsrc','is',DB::expr('null'))->or_where_close()->and_where('is_use', '=', 1)->execute()->as_array();
        foreach ($data as &$item)
        {
            $tag = ($item['is_system'] > 0 ? 's_' : 'c_') . "{$item['prefix']}_{$item['number']}";
            if (!$item['position'])
            {
                $item['position'] = $tag;
            }
            $item['is_pc'] = (int)$item['is_pc'];
            $item['flag'] = (int)$item['flag'];
            $item['webid'] = (int)$item['webid'];
            $item['size'] = preg_replace('~px~i', '', $item['size']);
            unset($item['is_system'], $item['number']);
        }
        unset($item);
        $platform = array();
        foreach ($data as $item)
        {
            array_push($platform, $item['is_pc']);
        }
        if ($platform)
        {
            $platform = array_unique($platform);
            rsort($platform);
        }
        $this->assign('data', $data);
        $this->assign('platform', $platform);
        $this->assign('template', json_encode($template));
        $this->display('stourtravel/advertise5x/edit');
    }

    /**
     * 修改广告
     */
    public function action_edit()
    {
        $id = $this->params['id'];
        $info = DB::query(Database::SELECT, "select a.*,p.kindname from sline_advertise_5x as a left join sline_page as p on a.prefix=p.pagename having a.id={$id}")->execute()->as_array();
        $adsrc = unserialize($info[0]['adsrc']);
        $adname = unserialize($info[0]['adname']);
        $adlink = unserialize($info[0]['adlink']);
        $adorder = unserialize($info[0]['adorder']);
        $image = array();
        foreach ($adsrc as $k => $r)
        {
            $image[] = array($adsrc[$k], $adname[$k], $adlink[$k], $adorder[$k]);
        }
        $info = $info[0];
        $info['image'] = $image;
        $this->assign('info', $info);
        //显示位置
        $position = array(array('name' => $info['is_pc'] ? '电脑端' : '移动端'));
        $web_list = Common::getWebList();
        array_unshift($web_list, array('webid' => 0, 'webname' => '主站'));
        foreach ($web_list as $item)
        {
            if ($item['webid'] == $info['webid'])
            {
                array_push($position, array('name' => $item['webname']));
                break;
            }
        }
        $page = Common::format_page_name(false);
        foreach ($page['page'] as $item)
        {
            if ($item['page_name'] == $info['prefix'])
            {
                foreach ($page['mould'] as $v)
                {
                    if ($v['id'] == $item['pid'])
                    {
                        array_push($position, array('name' => $v['name']));
                        break;
                    }
                }
                array_push($position, array('name' => $item['name']));
                break;
            }
        }
        if (!$info['position'])
        {
            array_push($position, array('name' => ($info['is_system'] > 0 ? 's_' : 'c_') . "{$info['prefix']}_{$info['number']}"));
        }
        else
        {
            array_push($position, array('name' => $info['position']));
        }
        $this->assign('position', $position);
        $this->display('stourtravel/advertise5x/edit');
    }


    /**
     * 获取广告位标示
     */
    public function action_ajax_number()
    {
        $prefix = Arr::get($_POST, 'prefix');
        $issystem = Arr::get($_POST, 'is_system');
        $ispc = Arr::get($_POST, 'is_pc');
        $webid = Arr::get($_POST, 'webid');
        $sql = "select number from sline_advertise_5x where webid={$webid} and  prefix='{$prefix}' and is_system='{$issystem}' and is_pc='{$ispc}' order by number desc limit 1";
        $num = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $num = empty($num) ? 1 : ($num[0]['number'] + 1);
        echo $num;
    }

    /**
     * ajax保存广告
     */
    public function action_ajax_save()
    {
        $data =& $_POST;
        $status = false;
        if ($data['id'])
        {
            if ($data['flag'] == 3)
            {
                list($data['adsrc'], $data['adlink'], $data['adname']) = array($data['video_src'], $data['video_name'], $data['video_link']);
            }
            $update_data = array();
            $update_data['adsrc'] = serialize($data['adsrc']);
            $update_data['adlink'] = serialize($data['adlink']);
            $update_data['adname'] = serialize($data['adname']);
            $update_data['adorder'] = serialize($data['adorder']);
            $update_data['is_show'] = $data['is_show'];
            $status = DB::update('advertise_5x')->set($update_data)->where('id', '=', $data['id'])->execute();
        }
        echo json_encode(array('status' => $status));
    }

    //广告高级
    public function action_senior()
    {
        $this->display('stourtravel/advertise5x/senior');
    }

    public function action_ajax_save_senior()
    {
        $status = false;
        $data =& $_POST;
        $update_data["kindlist"] = implode(',', $data["kindlist"]);
        $update_data["is_pc"] = $data["is_pc"];
        $update_data["webid"] = $data["webid"];
        $update_data["prefix"] = $data["page"];
        $update_data["flag"] = $data["flag"];
        $update_data["custom_label"] = $data["custom_label"];
        $update_data["position"] = $data["position"];
        $update_data["is_system"] = 0;
        $update_data["size"] = "{$data['width']}*{$data['height']}";
        $update_data["adsrc"] = serialize(array());
        $update_data["is_use"] = 1;
        $obj = DB::select()->from('advertise_5x')->or_where_open()->where('custom_label', '=', $update_data["custom_label"])->or_where('position', '=', $update_data["position"])->or_where_close();
        if (isset($data['id']) && $data['id'])
        {
            $obj->and_where('id', '!=', $data['id']);
        }
        if ($check_result = ($obj->execute()->current()))
        {
            $message = $check_result['custom_label'] == $update_data["custom_label"] ? 'custom_label' : 'position';

        }
        else
        {
            if (isset($data['id']) && $data['id'])
            {
                DB::update('advertise_5x')->set($update_data)->where('id', '=', $data['id'])->execute();
                $status = true;
                $message = '';
            }
            else
            {
                $number = DB::select()->from('advertise_5x')->where('webid', '=', $update_data["webid"])->and_where('prefix', '=', $update_data["prefix"])->and_where('is_system', '=', 0)->and_where('is_pc', '=', $update_data["is_pc"])->order_by('number', 'desc')->execute()->current();
                $update_data["number"] = empty($number) ? 1 : ($number['number'] + 1);
                list($message) = DB::insert('advertise_5x', array_keys($update_data))->values(array_values($update_data))->execute();
                $status = true;
            }
        }
        echo json_encode(array('status' => $status, 'message' => $message));
    }

    /**
     * ajax 切换广告位显示状态
     */
    public function action_ajax_statu()
    {
        $statu = (int)$_GET['statu'];
        $id = $_GET['id'];
        if ($statu > 1 || $statu < 0)
        {
            exit('0');
        }
        $rows = DB::update('advertise_5x')->set(array('is_show' => "$statu"))->where("id={$id}")->execute();
        echo $rows > 0 ? true : false;
    }

    /**
     * 自定义标签位检测
     */
    public function action_ajax_custom()
    {
        $custom = $_POST['custom_label'];
        $sql = "select * from sline_advertise_5x where custom_label='{$custom}' limit 1";
        $arr = DB::query(1, $sql)->execute()->current();
        if (empty($arr))
        {
            $msg = 1;
        }
        else
        {
            $msg = isset($_POST['id']) && $_POST['id'] == $arr['id'] ? 1 : 0;
        }
        echo $msg;
    }

    //开发者
    public function action_developer()
    {
        $this->display('stourtravel/advertise5x/developer');
    }

}