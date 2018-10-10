<?php

class Controller_Admin_Destination extends Controller_Destination
{
    public function action_index()
    {
        $this->display('admin/destination/index');
    }

    //读取数据
    public function action_read()
    {
        $node = Arr::get($_GET, 'node');
        $node_arr = explode('_', $node);
        $list = DB::select()->from('destinations')->where('pid', '=', $node_arr[1] == 'root' ? 0 : $node_arr[1])->execute()->as_array();
        $template = Common::getUserTemplteList('dest_index');
        foreach ($list as &$v) {
            $numbers = DB::select(array(DB::expr("count(*)"), 'num'))->from('destinations')->where('pid', '=', $v['id'])->execute()->get('num');
            if ($numbers <= 0) {
                $v['leaf'] = true;
            }
            $v['is_open'] = intval(in_array(12, explode(',', $v['opentypeids'])));

            if (!$v['templet']) {
                $v['templet_name'] = '标准';
            }
            else {
                foreach ($template as $item) {
                    if ($item['path'] == $v['templet']) {
                        $v['templet_name'] = $item['templetname'];
                        break;
                    }
                }
            }
        }
        echo json_encode(array('success' => true, 'text' => '', 'children' => $list));
    }

    //更新数据
    public function action_update()
    {
        $id = Arr::get($_POST, 'id');
        $field = Arr::get($_POST, 'field');
        $val = Arr::get($_POST, 'val');
        $data = array($field => $val);
        if ($field != 'is_open') {
            if ($field == 'displayorder' && $val == '') {
                $data['displayorder'] = 9999;
            }
            DB::update('destinations')->set($data)->where('id', '=', $id)->execute();
        }
        else {
            $result = DB::select('id', 'opentypeids')->from('destinations')->where('id', '=', $id)->execute()->current();
            $open_type['opentypeids'] = explode(',', $result['opentypeids']);
            if ($val > 0) {
                array_push($open_type['opentypeids'], 12);
            }
            else {
                if (($index = array_search('12', $open_type['opentypeids'])) !== false) {
                    unset($open_type['opentypeids'][$index]);
                }
            }
            $open_type['opentypeids'] = implode(',', $open_type['opentypeids']);
            DB::update('destinations')->set($open_type)->where('id', '=', $result['id'])->execute();
        }
        echo 'ok';
    }

    //设置模板
    public function action_set_template()
    {
        $info = DB::select()->from('destinations')->where('id', '=', $this->params['id'])->execute()->current();
        $this->assign('info', $info);
        $this->assign('template_list', Common::getUserTemplteList('dest_index'));
        $this->display('admin/destination/set_template');
    }

    //保存模板设置
    public function action_save()
    {
        $template_name = '标准';
        $data = json_decode(file_get_contents('php://input'), true);
        DB::update('destinations')->set(array('templet' => $data['templet']))->where('id', '=', str_replace('dest_', '', $data['id']))->execute();
        if ($data['templet']) {
            foreach (Common::getUserTemplteList('dest_index') as $item) {
                if ($item['path'] == $data['templet']) {
                    $template_name = $item['templetname'];
                    break;
                }
            }
        }
        echo json_encode(array('success' => true, 'templet' => $data['templet'], 'templet_name' => $template_name));
    }

    //配置目的地
    public function action_set()
    {
        $id = (int)$this->params['id'];
        $info = DB::select()->from('destinations')->where('id', '=', $id)->execute()->current();
        $this->assign('templetlist', Common::getUserTemplteList('dest_index'));
        $this->assign('id', $id);
        $this->assign('info', $info);
        $this->display('admin/destination/set');
    }


    public function action_ajax_save()
    {
        $data =& $_POST;
        $id = $data['id'];
        $data['piclist'] = array();
        foreach ($data['images'] as $k => $v)
        {
            if ($data['imgheadindex'] == $k)
            {
                $data['litpic'] = $v;
            }
            array_push($data['piclist'], "{$v}||{$data['imagestitle'][$k]}");
        }
        if ($data['piclist'])
        {
            $data['piclist'] = implode(',', $data['piclist']);
        }
        unset($data['id'], $data['imagestitle'], $data['images'], $data['imgheadindex']);
        //没有上传图片时,清空$data['piclist']
        if (empty($data['piclist']))
        {
            unset($data['piclist']);
        }

        DB::update('destinations')->set($data)->where('id', '=', $id)->execute();

    }


    //开发者
    public function action_developer()
    {
        $this->display('admin/destination/developer');
    }

}