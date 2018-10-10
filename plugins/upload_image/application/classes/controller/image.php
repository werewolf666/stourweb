<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Image extends Stourweb_Controller
{
    public function before()
    {
        $refer = $this->request->referrer();

        //只能由plugins目录下的应用调用.
        if(strpos($refer,'plugins')=== false&&strpos($refer,'newtravel')=== false)
        {
            exit('');
        }

        parent::before();
    }

    public function action_image_manage()
    {
        $action = Arr::get($_POST, 'action');
        $name = Arr::get($_POST, 'name');
        $id = Arr::get($_POST, 'id');
        $group_id = Arr::get($_POST, 'pid');
        $keyword = Arr::get($_POST, 'keyword');
        $index = Arr::get($_POST, 'index');
        switch ($action) {
            case 'rename':
                $sql = "update sline_image set image_name='{$name}' where id={$id}";
                $rows = DB::query(Database::UPDATE, $sql)->execute();
                break;
            case 'delete':
                $sql = "delete from sline_image where id in ({$id})";
                $rows = DB::query(Database::DELETE, $sql)->execute();
                break;
            case 'find':
                $search_type = Arr::get($_POST,'search_type') ? Arr::get($_POST,'search_type') : 1;
                //图片搜索
                if($search_type == 1)
                {
                    $p = (Arr::get($_POST, 'page') - 1) * 30;
                    $sql = "select image_name as name,url,id from sline_image where 1=1 ";
                    if($group_id != "-1")
                        $sql .= "and group_id ={$group_id} ";
                    if($keyword != "")
                        $sql .= "and (image_name like '%{$keyword}%' or url like '%{$keyword}%') ";
                    $sql .= "and is_hidden='0'";

                    //判断分页  还是全显示
                    if($index != '')
                    {
                        $sql .= "and is_hidden='0' order by id desc limit $p,30";
                    }
                    else{
                        $sql .= "and is_hidden='0' order by id desc ";
                    }
                    $rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
                    foreach ($rows as $k => $v) {
                        if (!isset($v['name']{0})) {
                            $rows[$k]['name'] = preg_replace('/\.(jpg|jpeg|gif|png)/', '', basename($v['url']));
                        }
                        if (strlen(Common::getConfig('image.img_domain')) > 0) {
                            $rows[$k]['url'] = rtrim(Common::getConfig('image.img_domain'), '/') . $v['url'];
                        }
                    }
                }
                //文件夹搜索
                else if($search_type == 2)
                {
                    $rows = DB::select()->from('image_group')->where('group_name','like','%'.$keyword.'%')->execute()->as_array();

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
     * 图片上传视图
     */
    public function action_upload_view()
    {

        global $cfg_public_url;
        $sql = "select * from sline_image_group";
        $rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $this->assign('publicPath', $cfg_public_url);
        $this->assign('group', $rows);
        $this->assign('id', $this->params['groupid']);
        $this->display('image/upload_view');
    }

    /**
     * 图片上传
     */
    public function action_upload()
    {

        is_uploaded_file($_FILES['file']['tmp_name']) or exit;

        require_once(Kohana::find_file('image', 'image'));



        $obj = new Image();
        $ext = '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $path = "/" . date('Y') . '/' . date('md') . '/' . md5($_FILES['file']['name'] . date('His')) . $ext;
        $filesize = filesize($_FILES['file']['tmp_name']);
        $image_name=preg_replace('~\.\w{3,5}$~','',$_FILES['file']['name']);
        $temp = dirname(DOCROOT) . '/uploads/image.temp';
        if (move_uploaded_file($_FILES['file']['tmp_name'], $temp)) {
            $_FILES['file']['tmp_name'] = $temp;
        }
        if ($this->params['iswater'] > 0) {
            //添加水印
            $water = Common::getConfig('watermark');
            if ($water['watermark']['photo_markon'] == '1') {
                $this->set_water(
                    $_FILES['file']['tmp_name'],
                    $water['watermark']['photo_markimg'],
                    $water['watermark']['photo_marktext'],
                    $water['watermark']['photo_fontcolor'],
                    $water['watermark']['photo_waterpos'],
                    $water['watermark']['photo_fontsize'],
                    $water['watermark']['photo_marktype'],
                    $water['watermark']['photo_diaphaneity']
                );
            }
        }
        $bool = $obj->image_move($_FILES['file']['tmp_name'], $path);
        if ($bool) {
            $image = ORM::factory('image');
            $url = Common::getConfig('image.upload_path') . $path;
            $result = $image->where('url', '=', $url)->find();
            if (!$result->loaded()) {
                $image->group_id = $this->params['groupid'];
                $image->url = $url;
                $image->image_name = $this->params['name'] ? $this->params['name'] : $image_name;
                $image->size = $filesize;
                $image->save();
            }
            if (strlen(Common::getConfig('image.img_domain')) > 0) {
                $url = rtrim(Common::getConfig('image.img_domain'), '/') . $url;
            }
            echo $url;
        }

    }

    /**
     * 编辑器插入图片
     */
    public function action_insert_view()
    {
        global $cfg_public_url;
        $sql = "select * from sline_image_group";
        $water = $this->params['iswater'] === '0' ? 0 : 1;
        $rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $this->assign('publicPath', $cfg_public_url);
        $this->assign('group', $rows);
        $this->assign('iswater', $water);
        $this->display('image/insert_view');
    }


    private function set_water($imgSrc, $markImg, $markText, $TextColor, $markPos, $fontSize, $markType, $markDiaphaneity)
    {


        $srcInfo = @getimagesize($imgSrc);
        $srcImg_w = $srcInfo[0];
        $srcImg_h = $srcInfo[1];
        if ($srcImg_w < 300) return;


        switch ($srcInfo[2]) {
            case 1:
                $srcim = imagecreatefromgif($imgSrc);
                break;
            case 2:
                $srcim = imagecreatefromjpeg($imgSrc);
                break;
            case 3:
                $srcim = imagecreatefrompng($imgSrc);
                break;
            default:
                die("不支持的图片文件类型");
                exit;
        }

        if (!strcmp($markType, "img")) //使用图片加水印.
        {
            $markImg = BASEPATH.'/data/mark/' . $markImg;
            if (!file_exists($markImg) || empty($markImg)) {
                return;
            }
            $markImgInfo = getimagesize($markImg);
            $markImg_w = $markImgInfo[0];
            $markImg_h = $markImgInfo[1];
            switch ($markImgInfo[2]) {
                case 1:
                    $markim = imagecreatefromgif($markImg);
                    break;
                case 2:
                    $markim = imagecreatefromjpeg($markImg);
                    break;
                case 3:
                    $markim = imagecreatefrompng($markImg);
                    break;
                default:
                    die("不支持的水印图片文件类型");
                    exit;
            }
            $logow = $markImg_w;
            $logoh = $markImg_h;
        }
        if (!strcmp($markType, "text")) {
            $fontType = BASEPATH."/data/mark/STXINWEI.TTF";

            if (!empty($markText)) {
                if (!file_exists($fontType)) {
                    echo " fonttype not exist";
                    return;
                }
            } else {
                return;
            }

            $box = @imagettfbbox($fontSize, 0, $fontType, $markText);

            $logow = max($box[2], $box[4]) - min($box[0], $box[6]);
            $logoh = max($box[1], $box[3]) - min($box[5], $box[7]);
        }

        if ($markPos == 0) {
            $markPos = rand(1, 9);
        }

        switch ($markPos) {
            case 1:
                $x = +5;
                $y = +20;
                break;
            case 2:
                $x = ($srcImg_w - $logow) / 2;
                $y = +20;
                break;
            case 3:
                $x = $srcImg_w - $logow - 5;
                $y = +20;
                break;
            case 4:
                $x = +5;
                $y = ($srcImg_h - $logoh) / 2;
                break;
            case 5:
                $x = ($srcImg_w - $logow) / 2;
                $y = ($srcImg_h - $logoh) / 2;
                break;
            case 6:
                $x = $srcImg_w - $logow - 5;
                $y = ($srcImg_h - $logoh) / 2;
                break;
            case 7:
                $x = +5;
                $y = $srcImg_h - $logoh - 5;
                break;
            case 8:
                $x = ($srcImg_w - $logow) / 2;
                $y = $srcImg_h - $logoh - 5;
                break;
            case 9:
                $x = $srcImg_w - $logow - 5;
                $y = $srcImg_h - $logoh - 5;
                break;
            default:
                die("此位置不支持");
                exit;
        }

        $dst_img = @imagecreatetruecolor($srcImg_w, $srcImg_h);
        imagecopy($dst_img, $srcim, 0, 0, 0, 0, $srcImg_w, $srcImg_h);
        if (!strcmp($markType, "img")) {
            imagecopy($dst_img, $markim, $x, $y, 0, 0, $logow, $logoh);
            imagedestroy($markim);

        }

        if (!strcmp($markType, "text")) {
            $TextColor = str_replace('rgb(', '', $TextColor);
            $TextColor = str_replace(')', '', $TextColor);
            $rgb = explode(',', $TextColor);

            $color = imagecolorallocate($dst_img, intval($rgb[0]), intval($rgb[1]), intval($rgb[2]));

            imagettftext($dst_img, $fontSize, 0, $x, $y, $color, $fontType, $markText);


        }
        switch ($srcInfo[2]) {
            case 1:
                imagegif($dst_img, $imgSrc);
                break;
            case 2:
                imagejpeg($dst_img, $imgSrc);
                break;
            case 3:
                imagepng($dst_img, $imgSrc);
                break;
            default:
                die("不支持的水印图片文件类型");
                exit;
        }
        imagedestroy($dst_img);
        imagedestroy($srcim);
    }

}