<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Quickmenu extends ORM
{
    /**
     * @function 多数组菜单
     * @param $items
     * @return array
     */
    public static function all_menu($items)
    {
        foreach ($items as $item)
        {
            $items[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
        }
        return isset($items[0]['son']) ? $items[0]['son'] : array();
    }

    /**
     * @function 获取快捷菜单的pid
     * @param $items
     * @param $id
     * @return array
     */
    public static function menu_parent($items, $id, $qickMenu = array())
    {
        foreach ($items as $v)
        {
            if ($v['id'] == strval($id))
            {
                $qickMenu[] = $v['id'];
                $qickMenu = self::menu_parent($items, $v['pid'], $qickMenu);
            }
        }
        return $qickMenu;
    }

    /**
     * 获取菜单指定字段
     * @param $items
     * @param $params
     * @param string $field
     * @return array
     */
    public static function menu_title($items, $params, $field = 'title')
    {
        $result = array();
        if (is_string($params))
        {
            $params = array($params);
        }

        foreach ($params as $id)
        {
            foreach ($items as $v)
            {
                if ($v['id'] == $id)
                {
                    $result[] = $v[$field];
                }
            }
        }
        return $result;
    }

    /**
     * @function 查询所有快捷菜单
     * @param $adminId
     * @return array
     */
    public static function quick_menu_all($adminId)
    {
        $exists = array();
        $menuQuick = DB::select()->from('menu_quick')->where('admin_id', '=', $adminId)->execute()->as_array();
        foreach ($menuQuick as $v)
        {
            $exists[] = $v;
        }
        return $exists;
    }
}