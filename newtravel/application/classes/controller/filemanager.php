<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Filemanager extends Stourweb_Controller
{
    /*
     * 文件管理总控制器
     * */
    public function before()
    {
        parent::before();
    }

    private function get_basefolder($ismobile)
    {
        if ($ismobile)
        {
            return BASEPATH . '/phone/usertpl/';
        } else
        {
            return BASEPATH . '/usertpl/';
        }
    }

    /*
     * 文件管理列表
     * */
    public function action_index()
    {

        $action = $this->params['action'];
        $folder = $this->params['folder']; //文件目录名称
        $ismobile = $this->params['ismobile']; //是否是手机站点模板
        $embedpage = $this->params['embedpage']; //输出嵌入式页面或是独立页面
        $menuid = $this->params['menuid']; //页面归属菜单id

        if (empty($action))
        {
            $this->assign('folder', $folder);
            $this->assign('ismobile', $ismobile);
            $this->assign('menuid', $menuid);
            if ($embedpage)
            {
                $this->display('stourtravel/filemanager/embed_list');
            } else
            {
                $this->display('stourtravel/filemanager/list_raw');
            }
        } else if ($action == 'read')
        {
            $node = Arr::get($_GET, 'node');
            $folder = Arr::get($_GET, 'folder');
            $ismobile = Arr::get($_GET, 'ismobile');

            $list = array();
            $node = $node == 'root' ? $folder : $node;
            // echo $version.'aa';exit;
            $list = $this->get_dir_file($node, $ismobile);
            echo json_encode(array('success' => true, 'text' => '', 'children' => $list));
        }

    }

    /*
     * 页面编辑
     * */
    public function action_pageedit()
    {
        $file = Arr::get($_GET, 'file'); //编辑文件
        $ismobile = Arr::get($_GET, 'ismobile');

        $basefolder = $this->get_basefolder($ismobile);

        $filename = $file;
        $this->assign('filename', $filename);
        $filename = $basefolder . $filename;

        $content = file_exists($filename) ? file_get_contents($filename) : '';
        $this->assign('content', htmlentities($content, ENT_COMPAT, 'UTF-8'));

        $this->assign('ismobile', $ismobile);
        $this->display('stourtravel/filemanager/page_edit');

    }

    /*
     * 页面编辑保存
     * */
    public function action_ajax_page_save()
    {
        $flag = 0;
        $data = file_get_contents('php://input');
        parse_str($data, $_POST);
        $content = Arr::get($_POST, 'content');
        $filename = Arr::get($_POST, 'filename');
        $ismobile = Arr::get($_POST, 'ismobile');

        $basefolder = $this->get_basefolder($ismobile);

        $file = $basefolder . $filename;

        if (file_exists($file))
        {
            if (file_put_contents($file, $content))
            {
                $flag = true;
            }
        }

        echo json_encode(array('status' => $flag));

    }

    /*
     * 获取模板目录文件
     * */
    private function get_dir_file($node, $ismobile)
    {

        $directory = $this->get_basefolder($ismobile);
        $directory .= $node.'/';

        //$nodes = array();
        $folder_list = array();
        if (is_dir($directory))
        {
            $d = dir($directory);

            while ($f = $d->read())
            {

                if ($f == '.' || $f == '..' || substr($f, 0, 1) == '.') continue;

                $filename = $directory . '/' . $f;

                $lastmod = date('Y-m-d H:i:s', filemtime($filename));

                //如果是目录
                if (is_dir($filename))
                {

                    $qtip = '类型: 文件夹<br />最后修改时间: ' . $lastmod;
                    $folder_list[] = array(
                        'text' => $f,
                        'id' => $node . '/' . $f,
                        'qtip' => $qtip,
                        'ext' => ''
                    );

                } else
                {
                    $size = $this->format_bytes(filesize($filename), 2);
                    $ext = $this->get_extension($filename);
                    $qtip = '类型: 文件<br />最后修改时间: ' . $lastmod . '<br />大小: ' . $size;
                    $file_list[] = array(
                        'text' => $f,
                        'id' => $node . '/' . $f,
                        'leaf' => true,
                        'qtip' => $qtip,
                        'ext' => $ext
                    );
                }

            }

            $d->close();
        }
        $addinfo = array(
            'leaf' => true,
            'id' => $node . 'add',
            'text' => '<span class="btn btn-secondary size-S radius" onclick="uploadFile(this,\'' . $node . '\')">上传文件</span>',
            'ext' => ''
        );

        array_push($file_list, $addinfo);

        $nodes = array_merge($folder_list, $file_list);

        return $nodes;
    }

    /*
     * 获取文件修改时间
     * */
    private function format_bytes($val, $digits = 3, $mode = 'SI', $bB = 'B')
    {
        //$mode == 'SI'|'IEC', $bB == 'b'|'B'
        $si = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
        $iec = array('', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi');
        switch (strtoupper($mode))
        {
            case 'SI' :
                $factor = 1000;
                $symbols = $si;
                break;
            case 'IEC' :
                $factor = 1024;
                $symbols = $iec;
                break;
            default :
                $factor = 1000;
                $symbols = $si;
                break;
        }
        switch ($bB)
        {
            case 'b' :
                $val *= 8;
                break;
            default :
                $bB = 'B';
                break;
        }
        for ($i = 0; $i < count($symbols) - 1 && $val >= $factor; $i++)
            $val /= $factor;
        $p = strpos($val, '.');
        if ($p !== false && $p > $digits) $val = round($val);
        elseif ($p !== false) $val = round($val, $digits - $p);
        return round($val, $digits) . ' ' . $symbols[$i] . $bB;
    }

    /*
     * 获取文件扩展名
     * */
    public function get_extension($file)
    {
        return end(explode('.', $file));
    }


}