<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Comment extends Stourweb_Controller
{
    public function before()
    {
        parent::before();
    }

    //上传图片
    public function action_upload_pic()
    {
        $pl_crsfcode = Common::session('pl_crsfcode');
        $token = $_POST['token'];
        if(empty($pl_crsfcode) || $pl_crsfcode != $token)
        {
            echo json_encode(array('code'=>false,'msg'=>'表单验证数据错误'));
            exit;
        }

        $piccount =$_POST['piccount'];
        if(!is_numeric($piccount) || $piccount < 0 || $piccount >10)
        {
            echo json_encode(array('code'=>false,'msg'=>'评论图片不能超过10张'));
            exit;
        }

        if (!empty($_FILES) )
        {
            //错误检测
            if ($_FILES['Filedata']['error'] > 0)
            {
                switch ($_FILES ['Filedata'] ['error'])
                {
                    case 1 :
                        $error_log = '您上传的图片过大，请上传小于2M的图片';
                        break;
                    case 2 :
                        $error_log = '您上传的图片过大，请上传小于2M的图片';
                        break;
                    case 3 :
                        $error_log = '图片上传不完整';
                        break;
                    case 4 :
                        $error_log = '没有图片被上传';
                        break;
                    default :
                        break;
                }
                echo json_encode(array('code'=>false,'msg'=>$error_log));
                exit;
            }
            $tempFile = $_FILES['Filedata']['tmp_name'];
            if (!is_file($tempFile))
            {
                echo json_encode(array('code'=>false,'msg'=>'图片文件异常'));
                exit;
            }
            $filesize = filesize($tempFile);
            if($filesize > 2*1024*1024)
            {
                echo json_encode(array('code'=>false,'msg'=>'您上传的图片过大，请上传小于2M的图片'));
                exit;
            }
            // Define a destination
            $targetFolder = '/uploads/pinglun/'; // Relative to the root
            $targetPath = $_SERVER['DOCUMENT_ROOT'];
            $midPath = $targetFolder. date('Y') . '/' . date('md') . '/';

            $ext = '.' . pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION);
            $filename = md5($_FILES['Filedata']['name'] . date('His')) . $ext;

            $targetFile = rtrim($targetPath,'/') . $midPath . $filename;
            // Validate the file type
            $fileTypes = array('.jpg','.jpeg','.gif','.png'); // File extensions

            //echo $targetFile;

            if (in_array($ext,$fileTypes))
            {
                if(!file_exists(dirname($targetFile)))
                {
                    mkdir(dirname($targetFile), 0755, true);
                }
                $flag = move_uploaded_file($tempFile,$targetFile);
                if($flag)
                {
                    echo json_encode(array('code'=>true,'filename'=>$midPath .$filename));
                }
                else
                {
                    echo json_encode(array('code'=>false,'msg'=>'文件移动失败'));
                }
            }
            else
            {
                echo json_encode(array('code'=>false,'msg'=>'图片格式错误,允许的图片类型为:*.jpg,*.jpeg,*.gif,*.png'));
            }

        }
        else
        {
            echo json_encode(array('code'=>false,'msg'=>'服务器没有获取到图片数据!'));
        }

    }


    //得到评论列表
    public function action_ajax_get_pinlun()
    {
        $pagesize = 5;
        $typeid = intval(Arr::get($_POST,'typeid'));
        $articleid = intval(Arr::get($_POST,'articleid'));
        $pageno = intval(Arr::get($_POST,'pageno'));
        $pageno = $pageno <= 0 ? 1 : $pageno;
        //评论类型:pic all well mid bad
        $flag = Common::remove_xss(Arr::get($_POST,'flag'));

        $out = Model_Comment::search_result($typeid, $articleid, $flag, $pageno, $pagesize);
        $out['pageno']=$pageno;
        $out['pagesize']=$pagesize;
        echo json_encode($out);

    }


}