<?php

class Taglib_Position
{
    private $_conf = array();
    private $_column_url = '';

    //兼容老版本
    public function nav($param){}
    //列表页导航
    public function list_crumbs($param = array('destid' => null))
    {
        $position = array(array('name' => $GLOBALS['cfg_indexname'], 'href' => '/'));

        $nav = DB::select()->from('nav')->where('typeid', '=', $param['typeid'])->and_where('webid','=',$GLOBALS['sys_webid'])->order_by('id', 'desc')->execute()->current();
        $model = DB::select()->from('model')->where('id', '=', $param['typeid'])->execute()->current();
        $column_title = !empty($nav['shortname'])?$nav['shortname']:$model['modulename'];
        $path = $model['correct'] ? $model['correct'] : $model['pinyin'];

        $keyword = strip_tags($_GET['keyword']);
        $keyword = St_String::filter_mark($keyword);
        if (empty($keyword))
        {

            //导航标题
            array_push($position, array('name' => $column_title, 'href' => "/{$path}/"));
            $dest = array();
            if ($param['destid'])
            {
                //目的地
                $dest = self::_recursion_dest($param['destid']);
                foreach ($dest as &$v)
                {
                    $v['name'].=$column_title;
                    $v['href'] = sprintf('/%s/%s/', $path, $v['pinyin']);
                }
                if(!empty($dest[count($dest)-1]))
                {
                    unset($dest[count($dest)-1]['href']);
                }
                $position = array_merge($position, $dest);
            }
            if(empty($dest))
            {
                $list_title = $column_title . '列表';
                array_push($position, array('name' => $list_title));
            }
        }
        else
        {
            array_push($position, array('name' => $column_title, 'href' => "/{$path}/"));
            array_push($position, array('name' =>$keyword.'相关搜索'));
        }

        //格式化输出
        $this->_crumebs_formate($position);
    }

    public function show_crumbs($param)
    {
        $model = DB::select()->from('model')->where('id', '=', $param['typeid'])->execute()->current();
        $path = $model['correct'] ? $model['correct'] : $model['pinyin'];
        //首页
        $position = array(array('name' =>$GLOBALS['cfg_indexname'], 'href' => '/'));
        //导航标题
        $nav = DB::select()->from('nav')->where('typeid', '=', $param['typeid'])->and_where('webid','=',$GLOBALS['sys_webid'])->order_by('id', 'desc')->execute()->current();
        $column_title=$nav && $nav['shortname'] ? $nav['shortname'] : $model['modulename'];
        array_push($position, array('name' => $column_title, 'href' => "/{$path}/"));
        //目的地
        $dest = self::_recursion_dest($param['info']['finaldestid']);
        foreach ($dest as &$v)
        {
            $v['name'].=$column_title;
            $v['href'] = sprintf('/%s/%s/', $path, $v['pinyin']);
        }
        $position = array_merge($position, $dest);
        //网站标题
        array_push($position, array('name' => $param['info']['title']));
        //格式化输出
        $this->_crumebs_formate($position,$column_title);
    }

    /**
     * @function 格式化输出面包屑导航
     * @param $positionParam
     */
    private function _crumebs_formate($positionParam)
    {
        $data = array();
        foreach ($positionParam as $v)
        {
            $val = !empty($v['href']) ? sprintf('<a href="%s" title="%s">%s</a>', $v['href'], $v['name'],$v['name']) : $v['name'];
            array_push($data, $val);
        }
        echo implode('&nbsp;&nbsp;&gt;&nbsp;&nbsp;', $data);
    }

    /**
     * @function 递归查询目的地
     * @param $id
     * @return array
     */
    private static function _recursion_dest($id)
    {
        $position = array();
        while (true)
        {
            $data = DB::select()->from('destinations')->where('id', '=', $id)->and_where('isopen', '=', 1)->execute()->current();
            if ($data)
            {
                array_push($position, array('name' => $data['kindname'], 'pinyin' => $data['pinyin']));
                if (!$data['pid'])
                {
                    break;
                }
                $id = $data['pid'];
            }
            else
            {
                break;
            }
        }
        return count($position) > 1 ? array_reverse($position) : $position;
    }
}