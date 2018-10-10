<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Menu_New extends ORM
{

    /**
     * @function 将配置写入文件
     * @return array
     */
    public static function set_config()
    {
        static $_config = null;
        if (!is_null($_config))
        {
            return $_config;
        }
        //将用户信息保存到session
        $user_id = Session::instance()->get('userid');
        $role_id = Session::instance()->get('roleid');
        $config_file = CACHE_DIR . 'newtravel/menu_' . $user_id . '.php';
        $data = include $config_file;
        if (!$data || !is_array($data))
        {
            $data = array();
            //标准产品
            $result = DB::select()->from('menu_new')
                ->where('isshow', '=', 1)
                ->order_by(DB::expr(' pid asc, ifnull(displayorder, 9999) asc,id asc '))
                ->execute()
                ->as_array();

            //菜单权限功能
            foreach ($result as $key => $value)
            {
                //如果非系统管理员,执行权限判断功能
                if ($role_id != 1)
                {
                    $right = DB::select('right')->from('role_right')->where('roleid', '=', $role_id)->and_where('menuid', '=', $value['id'])->execute()->get('right');
                    if ($right == 0)
                    {
                        unset($result[$key]);
                    }
                }
            }

            foreach ($result as $v)
            {

                //连接地址整理
                $v['url'] = '';
                if ($v['extlink'])
                {
                    $v['url'] = $v['extparams'];
                }
                else
                {
                    //url基础参数
                    $url_base = array($v['directory'], $v['controller'], $v['method']);
                    foreach ($url_base as $base)
                    {
                        if ($base)
                        {
                            $v['url'] .= '/' . $base;
                        }
                    }
                    //url扩展参数
                    if ($v['extparams'])
                    {
                        $v['url'] .= str_replace('{$typeid}', $v['typeid'], $v['extparams']);
                    }
                    //url 编号定位
                    if (strlen($v['url']) > 0)
                    {
                        $v['url'] .= '/menuid/' . $v['id'];
                    }
                    //删除左侧斜线
                    $v['url'] = ltrim($v['url'], '/');
                    //3级菜单
                    if ($v['level'] == 3)
                    {
                        $typename = Model_Model::getModuleName($v['typeid']);
                        if (!preg_match("~^{$typename}~", $v['title']))
                        {
                            $v['alias_title'] = $typename . $v['title'];
                        }
                    }
                }

                $data["{$v['id']}"] = isset($data["{$v['id']}"]) ? array_merge($data["{$v['id']}"], $v) : $v;
                //添加子节点
                if ($v['pid'] > 0 || stripos($v['pid'], 't') !== false)
                {
                    $data["{$v['pid']}"]['child_id'][] = $v['id'];
                }
                //父节点添加order_id、question_id、comment_id
                if (in_array($v['controller'], array('order', 'comment', 'question', 'jieban')))
                {
                    if ($v['controller'] == 'order' || $v['controller'] == 'jieban')
                    {
                        $_item = 'order_id';
                    }
                    else if ($v['controller'] == 'question')
                    {
                        $_item = 'question_id';
                    }
                    else
                    {
                        $_item = 'comment_id';
                    }
                    $data["{$v['pid']}"][$_item] = $v['id'];
                }
                //整理数据
                $unset_data = array('isshow', 'directory', 'method', 'extparams', 'extlink');
                foreach ($unset_data as $unset)
                {
                    unset($v[$unset]);
                }

            }
            //补全父节点URL
            foreach ($data as $k => &$v)
            {
                $order[$k] = $v['displayorder'];
                $id[$k] = $v['id'];
                if (!$v['url'] && isset($v['child_id']))
                {
                    $v['url'] = $data[$v['child_id'][0]]['url'];
                    $v['alias_title'] = isset($data[$v['child_id'][0]]['alias_title']) ? $data[$v['child_id'][0]]['alias_title'] : $data[$v['child_id'][0]]['title'];
                }
            }
            //排序
            uasort($data, array('self', 'menu_sort'));
            //写入缓存文件
            file_put_contents($config_file, "<?php \r\n return " . var_export($data, true) . ';');
        }
        $_config = $data;
        return $_config;
    }



    /**
     * @function 菜单排序
     * @param $param1
     * @param $param2
     * @return int
     */
    public static function menu_sort($param1, $param2)
    {
        if ($param1['displayorder'] == $param2['displayorder'])
        {
            if ($param1['id'] > $param2['id'])
            {
                return 1;
            }
            else if ($param1['id'] < $param2['id'])
            {
                return -1;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return $param1['displayorder'] > $param2['displayorder'] ? 1 : -1;
        }

    }

    /**
     * @function 根据Pid获取菜单
     * @param $pid
     * @return array
     */
    public static function get_config_by_pid($pid, $filter = array())
    {
        $node = array();
        $pid = strval($pid);
        $config = self::set_config();
        foreach ($config as $v)
        {
            if ($v['pid'] === $pid && !in_array($v['id'], $filter))
            {
                $node[] = $v;
            }
        }
        return $node;

    }

    /**
     * @function 获取同级节点
     * @param $id
     * @return array
     */
    public static function get_siblings_node($id)
    {
        $node = array();
        $config = self::set_config();
        $pid = $config[$id]['pid'];
        foreach ($config as $v)
        {
            if ($v['pid'] == $pid)
            {
                $node[] = $v;
            }
        }
        return $node;
    }

    /**
     * @function 根据子节点ID获取父节点
     * @param $id
     * @return mixed
     */
    public static function get_perent_node($id)
    {
        $config = self::set_config();
        $parent_id = $config[$id]['pid'];
        return $config[$config[$parent_id]['pid']];
    }

    /**
     * 根据标题获取链接
     * @param $title
     * @return mixed
     */
    public static function get_by_title($title)
    {
        $data = array();
        $config = self::set_config();
        foreach ($config as $item)
        {
            if ($title == $item['title'])
            {
                $data = $item;
                break;
            }
        }
        return $data;
    }

    /**
     * @function 左侧菜单
     * @param $id
     * @return mixed
     */
    public static function get_left_nav($id)
    {
        $config = self::set_config();
        $id = strval($id);
        $node = $config[$id];
        if (!isset($node['child_id']))
        {
            $node = $config[$node['pid']];
            if($node['title']=='免费通话')
            {
                $node['title'] .= Common::get_help_icon('national_free_call');
            }
        }

        return $node;
    }

    /**
     * @function 获取当前产品在导航上的名称
     * @param $typeid
     * @param null $fields
     * @return string
     */
    public static function get_nav_title($typeid, $fields = null)
    {
        $result = DB::select()->from('nav')->where('typeid', '=', $typeid)->execute()->current();
        if (!$result)
        {
            return '';
        }
        if (!is_null($fields))
        {
            return $result[$fields];
        }
        return $result;
    }

    /**
     * @function 根据id获取菜单
     * @param $id mixed
     * @param int $level 0:一维数组 1:二维数组
     * @return array
     */
    public static function get_config_by_id($id, $level = 0)
    {
        $node = array();
        $config = self::set_config();
        if (!is_array($id))
        {
            $id = array($id);
        }
        foreach ($id as $v)
        {
            $node[] = $config[$v];
        }
        if (!$level)
        {
            $node = $node[0];
        }
        return $node;
    }

    /**
     * @function 获取产品评论URL
     * @param $typeid
     * @return string
     */
    public static function get_commnet_url($typeid){
        $commentUrl = '';
        $config = self::set_config();
        foreach($config as $v){
          if($v['typeid']==$typeid && $v['controller']=='comment'){
              $commentUrl=$v['url'];
            break;
          }
        }
        return $commentUrl;
    }

    /**
     * @function 菜单按主导航顺序排列
     * @param $menu
     * @return array
     */
    public static function ordey_by_nav($menu)
    {
        //检查是否写入缓存.
        $cache = Cache::instance('default');
        $cache_menu = $cache->get('back_menu_cache',array());
        if(empty($cache_menu))
        {
            foreach($menu as $k =>$v)
            {
                if($v['typeid'])
                {
                    $rs  = DB::select('displayorder','shortname')
                        ->from('nav')
                        ->where('typeid','=',$v['typeid'])
                        ->execute()
                        ->current();
                    $menu[$k]['displayorder'] = $rs['displayorder'] ? $rs['displayorder'] : 9999;
                    $menu[$k]['title'] = !empty($rs['shortname']) ? $rs['shortname'] : $rs['title'];
                }
                else
                {
                    $menu[$k]['displayorder'] = 9999;
                }
            }
            $new_list = St_Array::list_sort_by($menu,'displayorder','asc');
            $cache->set('back_menu_cache',$new_list);
            return $new_list;
        }
        else
        {
            return $cache_menu;
        }






    }
}