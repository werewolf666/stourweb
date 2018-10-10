<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Image extends Stourweb_Controller
{
    /**
     * 图片首页
     */
    public function action_index()
    {
        global $cfg_public_url;
        $group_id = !empty($this->params['id']) ? intval($this->params['id']) : 0;
        $group = DB::select()->from('image_group')->where('group_id', '=', $group_id)->execute()->current();
        $pid = $group ? $group['pid'] : 0;//var_dump($group,$group_id,$this->params['id']);exit;
        $level = $group ? $group['level'] : 0;
        $this->assign('menuid', $this->params['menuid']);
        $this->assign('pulic', $cfg_public_url);
        $this->assign('pid', $pid);
        $this->assign('level', $level);
        $this->display('stourtravel/image/index');
    }

    /**
     * 目录导入
     */
    public function action_dir_import()
    {

        $sql = "select * from sline_image_group";
        $rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $this->assign('dir', $rows);
        $this->assign('uploads', Common::getConfig('image.upload_path'));
        $this->display('stourtravel/image/import');
    }

    /**
     * 扫描目录名
     */
    public function action_dir_scan()
    {
        require(Kohana::find_file('image', 'image'));
        $obj = new Image();
        $path = Arr::get($_POST, 'path');
        $path = $path == Common::getConfig('image.upload_path') ? '' : $path;
        $dirs = $obj->image_dir_list($path);
        echo json_encode(array('dirs' => $dirs, 'count' => count($dirs)));
    }

    /**
     * 扫描指定目录
     */
    public function action_target_dir()
    {
        require(Kohana::find_file('image', 'image'));
        $obj = new Image();
        $path = Arr::get($_POST, 'path');
        $group = Arr::get($_POST, 'group');
        $data = $obj->image_dir_scan($path);
        foreach ($data as $v)
        {
            $image = ORM::factory('image');
            $url = str_replace(array($this->baseDir, '\\'), array('', '/'), $v[0]);
            $result = $image->where('url', '=', $url)->find();
            if (!$result->loaded())
            {
                $image->group_id = $group;
                $image->url = $url;
                $image->size = $v[1];
                $image->save();
            }
        }
        echo 'success!';

    }

    /**
     * 添加分组
     */
    public function action_group_view()
    {

        $this->display('stourtravel/image/view');
    }

    /**
     * 图片管理
     * 重命名、删除、移动
     */
    public function action_image_manage()
    {
        $action = Arr::get($_POST, 'action');
        $name = Arr::get($_POST, 'name');
        $id = Arr::get($_POST, 'id');
        $group_id = Arr::get($_POST, 'pid');
        switch ($action)
        {
            case 'rename':
                $sql = "update sline_image set image_name='{$name}' where id={$id}";
                $rows = DB::query(Database::UPDATE, $sql)->execute();
                break;
            case 'delete':
                $rows = 0;
                $image = DB::select()->from('image')->where('id', 'in', DB::expr("($id)"))->execute()->as_array();
                if ($image)
                {
                    list(, $path) = $this->_find_group_parent($image[0]['group_id']);
                    $path = implode('|$|', $path);
                    foreach ($image as $item)
                    {
                        $original = $item;
                        $data['url'] = $item['url'];
                        $data['name'] = $item['image_name'];
                        unset($original['id'], $original['group_id']);
                        $data['content'] = serialize(array(array('path' => $path, 'data' => array($original))));
                        DB::insert('image_recycle', array_keys($data))->values(array_values($data))->execute();
                    }
                    $rows = DB::delete('image')->where('id', 'in', DB::expr("($id)"))->execute();
                }
                break;
            case 'find':
                $search_type = Arr::get($_POST, 'search_type');
                $page = Arr::get($_POST, 'page');
                $keyword = trim(Arr::get($_POST, 'keyword'));
                //关键词搜索
                if ($keyword && in_array($search_type, array(0, 2)))
                {
                    $image_obj = DB::select()->from('image');
                    $image_obj->and_where('image_name', 'like', "%{$keyword}%");
                }
                else
                {
                    $image_obj = DB::select()->from('image')->where('group_id', '=', $group_id);
                }
                $rows = $image_obj->offset(($page - 1) * 50)->limit(50)->order_by('id', 'desc')->execute()->as_array();
                foreach ($rows as $k => &$v)
                {
                    if (!isset($v['image_name']{0}))
                    {
                        $v['image_name'] = preg_replace('/\.(jpg|jpeg|gif|png)/', '', basename($v['url']));
                    }
                    if (strlen(Common::getConfig('image.img_domain')) > 0)
                    {
                        $v['url'] = rtrim(Common::getConfig('image.img_domain'), '/') . $v['url'];
                    }
                    $v['_type'] = 'image';
                    $v['do_not'] = 0;
                }
                break;
            case 'move':
                $sql = "update sline_image set group_id={$group_id} where id in ({$id})";
                $rows = DB::query(Database::DELETE, $sql)->execute();
                break;
        }
        echo json_encode($rows);
    }

    /**
     * 批量移动视图
     */
    public function action_image_move()
    {
        global $cfg_public_url;
        $group_pid = $this->params['group_pid'] ? $this->params['group_pid'] : 0;
        $rows = DB::select()->from('image_group')->where('pid', '=', $group_pid)->execute()->as_array();
        foreach ($rows as &$item)
        {
            $has_directory = DB::select()->from('image_group')->where('pid', '=', $item['group_id'])->execute()->current();
            $item['has_directory'] = $has_directory ? true : false;
        }
        if ($group_pid)
        {
            $group_parent = DB::select()->from('image_group')->where('group_id', '=', $group_pid)->execute()->current();
            $group_parent['level'] += 1;
            $group_parent['group_pid'] = $group_pid;
        }
        else
        {
            $group_parent = array('level' => 1, 'group_pid' => $group_pid);
        }
        $this->assign('publicPath', $cfg_public_url);
        $this->assign('group', $rows);
        $this->assign('ids', $this->params['id']);
        $this->assign('group_parent', $group_parent);
        $this->display('stourtravel/image/move');
    }

    /**
     * 分组管理
     * 重命名、添加、删除、移动
     */
    public function action_group_manage()
    {
        global $cfg_public_url;
        $action = Arr::get($_POST, 'action');
        $group_name = Arr::get($_POST, 'name');
        $description = Arr::get($_POST, 'description');
        $group_id = Arr::get($_POST, 'id');
        $group_pid = Arr::get($_POST, 'pid');
        switch ($action)
        {
            case 'rename':
                $sql = "update sline_image_group set group_name='{$group_name}' where group_id={$group_id}";
                $rows = DB::query(Database::UPDATE, $sql)->execute();
                break;
            case 'add':
                if (!$group_name)
                {
                    echo json_encode(0);
                    exit();
                }
                $insert_data = array('group_name' => $group_name, 'description' => $description, 'pid' => $group_pid, 'level' => Arr::get($_POST, 'level'));
                list($rows) = DB::insert('image_group', array_keys($insert_data))->values(array_values($insert_data))->execute();
                break;
            case 'delete':
                //删除组
                $group = $this->find_group_child($group_id);
                array_unshift($group, $group_id);
                $data = array('name' => '', 'content' => array());
                foreach ($group as $k => $item)
                {
                    list(, $path) = $this->_find_group_parent($item);
                    if ($k == 0)
                    {
                        $data['name'] = $path[count($path) - 1];
                    }
                    $image = DB::select()->from('image')->where('group_id', '=', $item)->execute()->as_array();
                    $single = array('path' => implode('|$|', $path), 'data' => array());
                    foreach ($image as $value)
                    {
                        unset($value['id'], $value['group']);
                        array_push($single['data'], $value);
                    }
                    array_push($data['content'], $single);
                }
                $data['content'] = serialize($data['content']);
                list(, $rows) = DB::insert('image_recycle', array_keys($data))->values(array_values($data))->execute();
                if ($rows)
                {
                    $group = implode(',', $group);
                    //删除分类
                    DB::delete('image_group')->where('group_id', 'in', DB::expr("({$group})"))->execute();
                    //删除图片
                    DB::delete('image')->where('group_id', 'in', DB::expr("({$group})"))->execute();
                }
                break;
            case 'find':
                $search_type = Arr::get($_POST, 'search_type');
                $keyword = trim(Arr::get($_POST, 'keyword'));
                $image_group_obj = DB::select()->from('image_group')->where('pid', '=', $group_pid);
                //关键词搜索
                if ($keyword && in_array($search_type, array(0, 1)))
                {
                    $image_group_obj->and_where('group_name', 'like', "%{$keyword}%");
                }
                $rows = $image_group_obj->order_by('group_id', 'desc')->execute()->as_array();
                $data_prefix=array();
                foreach ($rows as $k => $v)
                {
                    $rows[$k]['url'] = $cfg_public_url . 'images/nopic.gif';
                    $rows[$k]['_type'] = 'directory';
                    if($v['do_not']==1){
                      array_push($data_prefix,$rows[$k]);
                      unset($rows[$k]);
                    }
                }
                $rows=$data_prefix?array_merge($data_prefix,$rows):$rows;
                break;
            case 'find_position':
                list($pid,$path)=$this->_find_group_parent($group_pid);
                $result=array('<a href=\'javascript:void(0)\' data=\'{pid:0}\'>图库管理</a>');
                $count=count($pid)-1;
                foreach($pid as $k=>$item){
                    array_push($result,$k!=$count?"<a href='javascript:void(0)' data='{pid:{$item}}'>{$path[$k]}</a>":"<span>{$path[$k]}</span>");
                }
                $rows = array('level' => count($pid), 'position' => implode('&nbsp;&gt;&nbsp;', $result));
                break;
        }
        echo json_encode($rows);
    }

    /**
     * 图片上传视图
     */
    public function action_upload_view()
    {
        global $cfg_public_url;
        $items = $this->_group();
        $this->assign('publicPath', $cfg_public_url);
        $this->assign('group', $items);
        $this->assign('id', $this->params['groupid']);
        $this->display('stourtravel/image/upload_view');
    }


    private function _group()
    {
        $rows = DB::select()->from('image_group')->order_by('group_id', 'asc')->execute()->as_array();
        $items = array();
        foreach ($rows as $v)
        {
            $v['_path'] = $v['pid'] == 0 ? "0_{$v['group_id']}" : '';
            $items[$v['group_id']] = $v;
        }
        unset($rows);
        while (true)
        {
            $bool = true;
            foreach ($items as $k => &$v)
            {
                if (!$v['_path'])
                {
                    //不存在父级节点，则删除
                    if (empty($items[$v['pid']]))
                    {
                        unset($items[$k]);
                        continue;
                    }
                    if ($items[$v['pid']]['_path'])
                    {
                        $v['_path'] = $items[$v['pid']]['_path'] . "_{$v['group_id']}";
                    }
                    else
                    {
                        $bool = false;
                        continue;
                    }
                }
                if (!isset($v['_depth']))
                {
                    $v['_depth'] = substr_count($v['_path'], '_') - 1;
                }
            }
            if ($bool)
            {
                break;
            }
        }
        usort($items, array($this, '_sort'));
        return $items;
    }

    private function _sort($a, $b)
    {
        $i = 0;
        $a_path = explode('_', $a['_path']);
        $b_path = explode('_', $b['_path']);
        while (true)
        {
            if ($a_path[$i] == $b_path[$i])
            {
                $i++;
                continue;
            }
            return $a_path[$i] < $b_path[$i] ? -1 : 1;
        }
    }

    /**
     * 图片上传
     */
    public function action_upload()
    {
        is_uploaded_file($_FILES['file']['tmp_name']) or exit;
        require(Kohana::find_file('image', 'image'));
        $obj = new Image();
        $ext = '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $path = "/" . date('Y') . '/' . date('md') . '/' . md5($_FILES['file']['name'] . date('His')) . $ext;

        $file_info = pathinfo($_FILES['file']['name']);
        $filename = $file_info['filename']; //文件本身名称

        $filesize = filesize($_FILES['file']['tmp_name']);
        $temp = dirname(DOCROOT) . '/uploads/image.temp';
        if (move_uploaded_file($_FILES['file']['tmp_name'], $temp))
        {
            $_FILES['file']['tmp_name'] = $temp;
        }
        $bool = $obj->image_move($_FILES['file']['tmp_name'], $path);
        if ($bool)
        {

            $image = ORM::factory('image');
            $url = Common::getConfig('image.upload_path') . $path;
            $result = $image->where('url', '=', $url)->find();
            if (!$result->loaded())
            {
                $user_set_name = Arr::get($_GET, 'name');
                $image->group_id = $this->params['groupid'];
                $image->image_name = !empty($user_set_name) ? $user_set_name : $filename;
                $image->size = $filesize;
                $config = Kohana::$config->load('image');
                foreach ($config as $k => $item)
                {
                    if (is_array($item) && $item['is_open'] && method_exists($item['callback'], 'move_image'))
                    {
                        $value = $item['callback']::move_image($item, $url);
                        if ($value)
                        {
                            $url = $item['domain'] . '/' . $value;
                        }
                    }
                }
                $image->url = $url;
                if (strlen(Common::getConfig('image.img_domain')) > 0)
                {
                    $url = rtrim(Common::getConfig('image.img_domain'), '/') . $url;
                }
                $image->save();
            }
            echo $url;

        }
        echo '';
    }

    /**
     * 编辑器插入图片
     */
    public function action_insert_view()
    {
        global $cfg_public_url;
        $this->assign('publicPath', $cfg_public_url);
        $this->assign('group', $this->_group());
        $this->display('stourtravel/image/insert_view');
    }

    /**
     * 图库配置
     */
    public function action_config()
    {
        $local_image_domain = DB::select()->from('sysconfig')->where('varname', '=', 'cfg_m_img_url')->execute()->current();
        $config = array('cfg_m_img_url' => $local_image_domain['value']);
        if (isset($this->params['set']))
        {
            DB::update('sysconfig')->set(array('value' => rtrim(Arr::get($_POST, 'cfg_m_img_url'), '/')))->where('varname', '=', 'cfg_m_img_url')->execute();
            Model_Config::clear_cache();
            echo 'success';
        }
        else
        {
            $this->assign('config', $config);
            $this->display('stourtravel/image/config');
        }
    }


    /**
     * 图片加速
     */
    public function action_promote()
    {
        $system_config = DB::select()->from('sysconfig')->where('varname', 'in', DB::expr('("cfg_image_quality_open","cfg_image_quality","cfg_m_img_url")'))->execute()->as_array();
        $config = array();
        foreach ($system_config as $item)
        {
            $config[$item['varname']] = $item['value'];
        }
        $this->assign('config', $config);
        $this->display('stourtravel/image/promote');
    }

    //回收站
    public function action_recycle()
    {

        $this->display('stourtravel/image/recycle');
    }

    //读取回收站数据
    public function action_ajax_recycle()
    {
        global $cfg_public_url;
        $page = Arr::get($_POST, 'p');
        $page = $page ? $page : 1;
        $search_type = Arr::get($_POST, 'search_type');
        $keyword = trim(Arr::get($_POST, 'keyword'));
        $obj = DB::select()->from('image_recycle')->where('id', '>', 0);
        switch ($search_type)
        {
            case 1:
                $obj->and_where('url', 'is', DB::expr('null'));
                break;
            case 2:
                $obj->and_where('url', 'is', DB::expr('not null'));
                break;
        }
        if ($keyword)
        {
            $obj->and_where('name', 'like', DB::expr("'%{$keyword}%'"));
        }
        $result = $obj->limit(30)->offset(($page - 1) * 30)->execute()->as_array();
        foreach ($result as &$item)
        {
            if (!$item['url'])
            {
                $item['url'] = $cfg_public_url . 'images/nopic.gif';
                $item['_type'] = 'directory';
            }
            else
            {
                $item['_type'] = 'image';
            }

        }
        echo json_encode($result);
    }

    //回收站数据还原
    public function action_ajax_restore()
    {
        $id_str = implode(',', $_POST['data']);
        $data = DB::select()->from('image_recycle')->where('id', 'in', DB::expr("({$id_str})"))->execute()->as_array();
        foreach ($data as $item)
        {
            $item['content'] = unserialize($item['content']);
            foreach ($item['content'] as $sub_item)
            {
                $group_id = $this->_find_group_id($sub_item['path']);
                foreach ($sub_item['data'] as $k => $value)
                {
                    $value['group_id'] = $group_id;
                    DB::insert('image', array_keys($value))->values(array_values($value))->execute();
                }
            }
        }
        $rows = DB::delete('image_recycle')->where('id', 'in', DB::expr("({$id_str})"))->execute();
        echo json_encode(array('status' => $rows ? true : false));
    }

    private function _find_group_id($path)
    {
        $path = explode('|$|', $path);
        $pid = 0;
        foreach ($path as $k => $item)
        {
            $group = DB::select()->from('image_group')->where('pid', '=', $pid)->and_where('group_name', '=', $item)->execute()->current();
            if ($group)
            {
                $pid = $group['group_id'];
            }
            else
            {
                $data = array('group_name' => "{$item}", 'pid' => $pid);
                list($pid) = DB::insert('image_group', array_keys($data))->values($data)->execute();
            }
        }
        return $pid;
    }

    //回收站数据还原
    public function action_ajax_delete()
    {

        $id_str = implode(',', $_POST['data']);
        $data = DB::select()->from('image_recycle')->where('id', 'in', DB::expr("({$id_str})"))->execute()->as_array();
        foreach ($data as $item)
        {
            $item['content'] = unserialize($item['content']);
            foreach ($item['content'] as $sub_item)
            {
                foreach ($sub_item['data'] as $k => $value)
                {
                    if ($k == 'url')
                    {
                        $file = BASEPATH . $value['url'];
                        if (file_exists($file))
                        {
                            unlink($file);
                        }
                    }
                }
            }
        }
        $rows = DB::delete('image_recycle')->where('id', 'in', DB::expr("({$id_str})"))->execute();
        echo json_encode(array('status' => $rows ? true : false));
    }

    //回收站数据删除

    /**
     * @function 获取指定分组下的所有分组
     * @param $group_id
     * @return mixed
     */
    private function find_group_child($group_id)
    {
        static $group = array();
        if (!$group)
        {
            $group = DB::select()->from('image_group')->execute()->as_array();
        }
        $group_list = array($group_id);
        list($data, $i) = array(array(), 0);
        while ($i < 10000 && ($group_id = array_shift($group_list)) !== null)
        {
            foreach ($group as $item)
            {
                if ($item['pid'] == $group_id)
                {
                    array_push($group_list, $item['group_id']);
                    array_push($data, $item['group_id']);
                }
            }
            $i++;
        }
        return $data;
    }

    /**
     * 根据group_id确定父级目录
     * @param $group_id
     * @return mixed
     */
    private function _find_group_parent($group_id)
    {
        static $group;
        if (is_null($group))
        {
            $group = DB::select()->from('image_group')->execute()->as_array();
        }
        list($i, $data) = array(0, array(array(), array()));
        while ($i < 1000)
        {
            if ($group_id)
            {
                foreach ($group as $item)
                {
                    if ($item['group_id'] == $group_id)
                    {
                        array_unshift($data[0], $item['group_id']);
                        array_unshift($data[1], $item['group_name']);
                        $group_id = $item['pid'];
                    }
                }
            }
            else
            {
                break;
            }
        }
        return $data;
    }


//end
}