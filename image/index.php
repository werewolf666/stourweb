<?php

/**
 * 动态缩率图
 * Class Image
 */
class Image
{
    private $_root;
    private $_path;
    private $_thumb_path;
    private static $_instance = null;

    private function __construct()
    {
        $this->_root = dirname(dirname(__FILE__));
        $this->_path = $this->_root . '/data/thumb/{dirname}/';
        //设置缩略图地址(针对使用原图的也直接生成复制到相应的缩略图目录)
        $this->_set_thumb_path();

    }

    //单一实列
    public static function get_instance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    //生成缩率图
    public function thumb()
    {
        //检测缩略图是否存在
        if(file_exists($this->_thumb_path))
        {
              $params = array(
                  'thumb' => $this->_thumb_path
              );
        }
        //如果缩略不存在,则重新生成
        else
        {
            $params = $this->_pareurl();
            if (!$params)
            {
                return;
            }
            //缩率图存
            if (!file_exists($params['thumb']))
            {
                $params = $this->_make_thumb($params);
            }
        }

        //手动设置过期时间，单位都是秒
        $validtime = 6 * 24* 60 * 60;    // 6天
        //缓存相对请求的时间，
        header('Cache-Control: ' . 'max-age='. $validtime);

        //也很重要的Expires头，功能类似于max-age
        //time()+$validtime: 设置期限，到期后才会向服务器提交请求
        //gmdate，生成Sun, 01 Mar 2009 04:05:49 +0000  的字符串，而且是GMT标准时区
        //preg_replace,  生成Sun, 01 Mar 2009 04:05:49 GMT， 注意：可能与服务器设置有关，
        //但我都用默认设置
        header('Expires:'. preg_replace('/.{5}$/', 'GMT', gmdate('r', time()+ $validtime)));

        //文件最后修改时间
        $lasttime = filemtime($params['thumb']);

        //最后修改时间，设置了，点击刷新时，浏览器再次请求图片才会发出'IF_MODIFIED_SINCE'头，
        //从而被php程序读取
        header('Last-Modified: ' . preg_replace('/.{5}$/', 'GMT', gmdate('r', $lasttime) ));

        //重要，如果请求中的时间和 文件生成时间戳相等，则文件未修改，客户端可用缓存
        if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lasttime)
        {
            header("HTTP/1.1 304 Not Modified"); //服务器发出文件不曾修改的指令
            exit();
        }

        //$imginfo=getimagesize($params['thumb']);
        $mime = $this->_get_mime($params['thumb']);
        //输出图片
        //header("Content-type: {$imginfo['mime']}");
        header("Content-type:{$mime}");
        ob_clean();
        echo file_get_contents($params['thumb']);
    }

    //解析参数
    private function _pareurl()
    {
        $rs = array();
        if (strpos($_SERVER['QUERY_STRING'], '/') !== 0)
        {
            $_SERVER['QUERY_STRING'] = '/' . $_SERVER['QUERY_STRING'];
        }
        if (!preg_match('~_(\d+)x(\d+)~i', $_SERVER['QUERY_STRING'], $result))
        {
            return false;
        }

        list($size, $rs['width'], $rs['height']) = $result;
        $rs['file'] = $this->_root . str_replace($size, '', $_SERVER['QUERY_STRING']);
        if (!file_exists($rs['file']) || !getimagesize($rs['file']))
        {
            return false;
        }
        //缩率图路径
        $info = array_merge(pathinfo($rs['file']), getimagesize($rs['file']));

        switch ($info[2])
        {
            case 1:
                $rs['ext'] = 'gif';
                break;
            case 3:
                $rs['ext'] = 'png';
                break;
            default:
                $rs['ext'] = 'jpg';
        }
        //计算实际
        $rs['sWidth'] = $info[0];
        $rs['sHeight'] = $info[1];
        if ($rs['width'] > $rs['sWidth'])
        {
            $rs['width'] = $rs['sWidth'];
        }
        if ($rs['height'] > $rs['sHeight'])
        {
            $rs['height'] = $rs['sHeight'];
        }
       /* if ($rs['height'] >= $rs['sHeight'] && $rs['width'] >= $rs['sWidth'])
        {
            $rs['thumb'] = $rs['file'];
        }
        else
        {
            $size = $this->_size($rs);
            $rs['thumb'] = str_replace('{dirname}', "{$size['tWidth']}x{$size['tHeight']}", $this->_path) . md5($rs['file']) . ".{$info['extension']}";
        }*/
        $rs['thumb'] = $this->_thumb_path;
        return $rs;
    }

    //设置缩略图地址
    private function _set_thumb_path()
    {

        if (strpos($_SERVER['QUERY_STRING'], '/') !== 0)
        {
            $_SERVER['QUERY_STRING'] = '/' . $_SERVER['QUERY_STRING'];
        }
        if (!preg_match('~([a-z0-9]+)_(\d+)x(\d+)\.(jpg|png|jpeg|gif)~i', $_SERVER['QUERY_STRING'], $result))
        {
            return false;
        }
        if($result)
        {
            $this->_thumb_path = str_replace('{dirname}', "{$result[2]}x{$result[3]}", $this->_path) . $result[1] . ".{$result[4]}";
        }

    }



    //生成缩率图
    private function _make_thumb($params)
    {
        $params = $this->_size($params);
        switch ($params['ext'])
        {
            case 'png':

                $func = 'imagepng';
                $source = imagecreatefrompng($params['file']);
                break;
            case 'gif':
                $func = 'imagegif';
                $source = imagecreatefromgif($params['file']);
                break;
            default:
                $func = 'imagejpeg';
                $source = imagecreatefromjpeg($params['file']);
        }
        $scale = imagecreatetruecolor($params['tWidth'], $params['tHeight']);
        imagealphablending($scale, false);
        imagesavealpha($scale, true);
        imagecopyresampled($scale, $source, 0, 0, 0, 0, $params['tWidth'], $params['tHeight'], $params['sWidth'], $params['sHeight']);
        $thumb = imagecreatetruecolor($params['width'], $params['height']);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled($thumb, $scale, 0, 0, 0, 0, $params['tWidth'], $params['tHeight'], $params['tWidth'], $params['tHeight']);
        //生成目录
        $thumbDir = dirname($params['thumb']);
        if (!file_exists($thumbDir))
        {
            mkdir($thumbDir, 0777, true);
        }
        $func == 'imagejpeg' ? $func($thumb, $params['thumb'], 95) : $func($thumb, $params['thumb']);
        //释放资源
        imagedestroy($source);
        imagedestroy($thumb);
        return $params;
    }

    //缩率图尺寸
    private function _size($params)
    {
        if (($params['height'] == $params['sHeight'] && $params['width']) || ($params['width'] == $params['sWidth'] && $params['height']))
        {
            //一边与原图相等
            $params['tWidth'] = $params['width'];
            $params['tHeight'] = $params['height'];
            if ($params['height'] == $params['sHeight'])
            {
                $params['sWidth'] = $params['width'];
            }
            else
            {
                $params['sHeight'] = $params['height'];
            }
        }
        else
        {
            if (!$params['height'])
            {
                //定宽
                $percent = $params['width'] / $params['sWidth'];
                $params['height'] = floor($percent * $params['sHeight']);
            }
            else if (!$params['width'])
            {
                //定高
                $percent = $params['height'] / $params['sHeight'];
                $params['width'] = floor($percent * $params['sWidth']);
            }
            else
            {
                //两边均不同
                $percent = $params['width'] / $params['sWidth'];
                while (true)
                {
                    if ($percent * $params['sWidth'] > $params['width'] && $percent * $params['sHeight'] > $params['height'])
                    {
                        break;
                    }
                    $percent += 0.001;
                }
            }
            $params['tWidth'] = floor($percent * $params['sWidth']);
            $params['tHeight'] = floor($percent * $params['sHeight']);
        }
        return $params;
    }
    //end

    //获取图像mime类型
    private function _get_mime($file)
    {

        $ext = substr($file, strrpos($file, '.')+1);
        switch ($ext)
        {
            case 'png':

                $mime = 'image/png';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            default:
                $mime = 'image/jpeg';
                break;

        }
        return $mime;


    }

}

//防止jpeg生成图片时的报错.
ini_set('gd.jpeg_ignore_warning', 1);
$img = Image::get_instance();
$img->thumb();
