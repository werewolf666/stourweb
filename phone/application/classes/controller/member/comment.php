<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Member_Comment
 * 用户评论
 */
class Controller_Member_Comment extends Stourweb_Controller
{
    /**
     * 前置操作
     */
    private $_member;
    public function before()
    {
        parent::before();
        $this->_member = Common::session('member');
        if (empty($this->_member))
        {
            Common::message(array('message' => __('unlogin'), 'jumpUrl' => $this->cmsurl . 'member/login'));
        }
    }

    /**
     * 评论视图
     */
    public function action_index()
    {
        $id = Common::remove_xss($_GET['id']);
        $row = Model_Member_Order::get_order_detail($id,$this->_member['mid']);
        $model = ORM::factory('model', $row['typeid']);
        $table = $model->maintable;
        if ($table)
        {
            $info = ORM::factory($table, $row['productautoid'])->as_array();
            $row['litpic'] = $info['litpic'];
        }
        $this->assign('info', $row);
        $this->display('member/order/comment');
    }

    /**
     * 写入评论
     */
    public function action_save()
    {
        $id = intval($_POST['orderid']);

        $mid = $this->_member['mid'];
        //通过订单号码检测是否id是否合法
        $row = DB::select()->from('member_order')->where("id={$id} and memberid={$mid} and ispinlun=0")->execute()->current();
        if (empty($row))
        {
            echo json_encode(array('status'=>0,'msg'=>'操作异常'));
            exit;
        }
        $content = trim(Common::remove_xss($_POST['content']));
        if(strlen($content)<5)
        {
            echo json_encode(array('status'=>0,'msg'=>'请留下你的评论,至少5个汉字'));
            exit;
        }
        //写入评论
        $arr = array();
        $arr['addtime'] = time();
        $arr['memberid'] = $this->_member['mid'];
        $arr['isshow'] = 0;
        $arr['typeid'] = intval($_POST['typeid']);
        $arr['orderid'] = intval($_POST['orderid']);
        $arr['level'] = intval($_POST['score']);
        $arr['articleid'] = intval($_POST['articleid']);
        $arr['content'] = $content;
        $arr['piclist'] = $_POST['pic'] ? implode(',',$_POST['pic']) : '';
        list(, $row) = DB::insert('comment', array_keys($arr))->values(array_values($arr))->execute();
        if ($row > 0)
        {
            //赠送积分,写入积分日志
           /* Model_Member::operate_jifen($this->_member['mid'], $row['jifencomment'], 2);
            Product::add_jifen_log($this->_member['mid'], "评论赠送积分{$row['jifencomment']}分", $row['jifencomment'], 2);
          */
            //更改订单状态
            DB::update('member_order')->set(array('ispinlun' => 1))->where("id={$id}")->execute();
            $flag = 1;

        }
        else
        {
            $flag  = 0;
        }
        echo json_encode(array('status'=>$flag));
    }


    /**
     * 上传评论图片
     */
    public function action_uploadfile()
    {


        $pinyin =  'main';

        $file = $_FILES['Filedata'];

        $storepath = BASEPATH . '/uploads/' . $pinyin;

        $dir = BASEPATH . "/uploads/" . $pinyin . "/allimg/" . date('Ymd'); //原图存储路径.

        if (!file_exists($dir))
        {
            mkdir($dir,0777,true);
        }
        $path_info = pathinfo($_FILES['Filedata']['name']);

        $filename = date('YmdHis');
        $i = 0;

        while (file_exists($dir . '/' . $filename . '.' . $path_info['extension']))
        {

            $i = $i + 50;
            $filename = date('YmdHis') . $i;

        }

        $filename = $filename . '.' . $path_info['extension'];

        Upload::$default_directory = $dir;//默认保存文件夹
        Upload::$remove_spaces = true;//上传文件删除空格

        if (Upload::valid($file))
        {
            if (Upload::size($file, "2M"))
            {
                if (Upload::type($file, array('jpg', 'png', 'gif')))
                {

                    if (Upload::save($file, $filename))
                    {
                        $srcfile = $dir . '/' . $filename; //原图

                        $arr['success'] = 'true';
                        $arr['litpic'] = $GLOBALS['$cfg_basehost'] . substr(substr($srcfile, strpos($dir, '/uploads') - 1), 1);


                    }
                    else
                    {
                        //echo "error_no";
                        $arr['success'] = 'false';
                        $arr['msg'] = '未知错误,上传失败';
                    }
                }
                else
                {
                    $arr['success'] = 'false';
                    $arr['msg'] = '类型不支持';
                }
            }
            else
            {
                $arr['success'] = 'false';
                $arr['msg'] = '图片大小超过限制';
            }
        }
        else
        {
            $arr['success'] = 'false';
            $arr['msg'] = '未知错误,上传失败';
        }
        echo json_encode($arr);

    }
}