<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Comment extends Stourweb_Controller{

    /*
     * 评论总控制器
     * @author:netman
     * @data:2014-07-22
     * */

    public function before()
    {
        parent::before();
        $action = $this->request->action();
        if($action == 'index')
        {

            $param = $this->params['action'];

            $right = array(
                'read'=>'slook',
                'save'=>'smodify',
                'delete'=>'sdelete',
                'update'=>'smodify'
            );
            $user_action = $right[$param];
            if(!empty($user_action))
                Common::getUserRight('pinlun',$user_action);


        }
        else if($action == 'view')
        {
            Common::getUserRight('pinlun','sadd');
        }
        else if($action == 'ajax_save')
        {
            Common::getUserRight('pinlun','smodify');
        }
        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);

    }
    public function action_index()
    {

        $action=$this->params['action'];
        if(empty($action))  //显示问答列表
        {
            $this->display('stourtravel/comment/list');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $keyword=Arr::get($_GET,'keyword');

            $order='order by a.addtime desc';
            $w='';
            if(isset($this->params['typeid'])){
                $w.='where typeid='.$this->params['typeid'];
            }
            $sql="select a.*  from sline_comment as a $w $order limit $start,$limit";
            //echo $sql;


            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_comment a $w ")->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            $new_list=array();
            foreach($list as $k=>$v)
            {

                $v['productname'] = ORM::factory('question')->getProductName($v['articleid'],$v['typeid']);
                $v['nickname'] = Model_Comment::getMemberName($v['memberid']);
                $v['modulename'] = Model_Comment::getPinlunModule($v['typeid']);
                $new_list[] = $v;
            }
            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$new_list;
            $result['success']=true;

            echo json_encode($result);
        }
        else if($action=='save')   //保存字段
        {

        }
        else if($action=='delete') //删除某个记录
        {
            $rawdata=file_get_contents('php://input');
            $data=json_decode($rawdata);
            $id=$data->id;

            if(is_numeric($id)) //
            {
                $model=ORM::factory('comment',$id);
                $model->delete();
            }
        }
        else if($action=='update')//更新某个字段
        {
            $id=Arr::get($_POST,'id');
            $field=Arr::get($_POST,'field');
            $val=Arr::get($_POST,'val');

            $model=ORM::factory('comment',$id);
            $old_isshow = $model->isshow;
            if($model->id)
            {
                $model->$field=$val;
                $model->save();
                if($model->saved())
                {
                    if($field=='isshow' && $val==1 && $old_isshow==0)
                    {
                        $this->on_verified($id,$model->typeid,$model->memberid);
                    }
                    echo 'ok';
                }
                else
                    echo 'no';
            }
        }

    }
    public function action_add()
    {
        $typeid = $this->params['typeid'];
        $info['typeid'] = $typeid;
        $this->assign('info',$info);
        $grades = self::get_grades();
        $this->assign('grades',$grades);
        $this->display('stourtravel/comment/edit');
    }

    public function action_edit()
    {
        $id = $this->params['id'];
        $info = ORM::factory('comment',$id)->as_array();
        $info['piclist_arr'] =  json_encode(Common::getUploadPicture($info['piclist']));//图片数组

        $model = ORM::factory('model',$info['typeid']);
        if(!$model->loaded())
        {
            exit('error typeid');
        }
        $info['productname'] = DB::select('title')->from($model->maintable)->where('id','=',$info['articleid'])->execute()->get('title');
        if(!empty($info['memberid']))
        {
            $member = DB::select()->from('member')->where('mid', '=', $info['memberid'])->execute()->current();
            $member['nickname'] = empty($member['nickname'])? $member['mobile'] || $member['email']:$member['nickname'];
            $info['member'] = $member;
        }
        $grades = self::get_grades();
        $this->assign('grades',$grades);
        $this->assign('info',$info);
        $this->display('stourtravel/comment/edit');
    }
    public function action_ajax_save()
    {
        $id = $_POST['id'];
        $productid = $_POST['productid'];
        $typeid = $_POST['typeid'];
        $content = $_POST['content'];
        $level = $_POST['level'];
        $vr_nickname = $_POST['vr_nickname'];
        $vr_grade = $_POST['vr_grade'];
        $vr_jifencomment = $_POST['vr_jifencomment'];
        $vr_headpic = $_POST['vr_headpic'];
        $isshow = $_POST['isshow'];

        $curtime = $_POST['addtime'] ? strtotime($_POST['addtime']) : time();
        $model = ORM::factory('comment',$id);
        $old_isshow = $model->isshow;
        if(empty($model->memberid))
        {
            $model->articleid = $productid;
            $model->vr_nickname = $vr_nickname;
            $model->vr_grade = $vr_grade;
            $model->vr_jifencomment = $vr_jifencomment;
            $model->vr_headpic = $vr_headpic;
            $model->typeid = $typeid;

        }
        $model->addtime = $curtime;
        $model->level = $level;
        $model->content = $content;
        $model->isshow = $isshow;

        $imagestitle = $_POST['imagestitle'];
        $images = $_POST['images'];
        //图片处理
        $piclist ='';
        for($i=1;isset($images[$i]);$i++)
        {
            $desc = isset($imagestitle[$i]) ? $imagestitle[$i] : '';
            $pic = !empty($desc) ? $images[$i].'||'.$desc : $images[$i];
            $piclist .= $pic.',';
        }
        $piclist =strlen($piclist)>0 ? substr($piclist,0,strlen($piclist)-1) : '';//图片

        $model->piclist = $piclist;
        $model->save();
        if($model->saved())
        {
            if($old_isshow==0 && $isshow==1)
            {
                $this->on_verified($id,$model->typeid,$model->memberid);
            }
            echo json_encode(array('status'=>true,'msg'=>'保存成功','id'=>$model->id));
        }
        else
        {
            echo json_encode(array('status'=>false,'msg'=>'保存失败'));
        }
    }
    public function action_dialog_product_list()
    {
        $typeid = $_GET['typeid'];
        $action=$this->params['action'];
        if(empty($action))  //显示线路列表页
        {
            $this->assign('typeid',$typeid);
            $this->display('stourtravel/comment/dialog_product_list');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $keyword = Common::getKeyword($_GET['keyword']);
            $model = ORM::factory('model',$typeid);
            if(!$model->loaded())
            {
                exit('wrong typeid!');
            }
            $maintable = $model->maintable;
            $w= " where id is not null ";
           /* if(!empty($keyword))
            {
                $w.= " and title like '%{$keyword}%' ";
            }*/
            if (!empty($keyword))
            {
                $w.= is_numeric($keyword) ? " and id='$keyword'" : " and title like '%{$keyword}%' ";
                //$w .= preg_match('`^\d+$`', $keyword) ? " and id={$keyword}" : " and title like '%{$keyword}%'";
            }
            $sql = "select id,title from sline_{$maintable} {$w} limit {$start},{$limit}";
            $total_sql = "select count(*) as num from sline_{$maintable} {$w}";
            $list = DB::query(Database::SELECT,$sql)->execute()->as_array();
            $totalcount_arr = DB::query(Database::SELECT,$total_sql)->execute()->as_array();

            foreach($list as &$v)
            {
                $v['series'] =St_Product::product_series($v['id'],$typeid);//编号
                $v['url'] = self::get_product_url($v['id'],$typeid);
            }
            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$list;
            $result['success']=true;
            echo json_encode($result);
        }
    }
    public static function get_grades()
    {
        $levels = DB::select()->from('member_grade')->order_by('begin', 'asc')->execute()->as_array();
        return $levels;
    }

    public static function get_product_url($id, $typeid)
    {

        $model_info = ORM::factory('model',$typeid)->as_array();
        $field = 'title';
        $tablename = 'sline_'.$model_info['maintable'];
        $link = empty($model_info['correct'])?$model_info['pinyin']:$model_info['correct'];

        if ($typeid == 11)
        {
            $sql = "select id as aid,{$field} as title from {$tablename} where id='$id'";

        }
        else
        {
            $sql = "select aid,webid,{$field} as title from {$tablename} where id='$id'";
        }

        $row = DB::query(Database::SELECT, $sql)->execute()->current();
        $href = "/{$link}/show_{$row['aid']}.html";
        if ($row['webid'] > 0)
        {
            $destination = ORM::factory('destinations', $row['webid'])->as_array();
            $href = rtrim($destination['weburl'],'/') . $href;
        }
        return $href;
    }

    /**
     * 评论验证通过
     */
    private function on_verified($commentid,$typeid,$memberid)
    {
        $allowed_typeids = array(4,6,11,101);
        if(empty($typeid)||empty($memberid))
        {
            return;
        }
        if(in_array($typeid,$allowed_typeids))
        {
            $model_info = Model_Model::get_module_info($typeid);
            $jifen = Model_Jifen::reward_jifen('sys_comment_' . $model_info['pinyin'], $memberid);
            if (!empty($jifen)) {
                St_Product::add_jifen_log($memberid, '评论' . $model_info['modulename'] . '送积分' . $jifen, $jifen, 2);
            }
        }
        else
        {
            $orderid = DB::select('orderid')->from('comment')->where('id','=',$commentid)->execute()->get('orderid');
            if(empty($orderid))
            {
                return;
            }
            $order_info  = DB::select('jifencomment','memberid')->from('member_order')->where('id','=',$orderid)->execute()->current();
            if(!empty($order_info['memberid']) && !empty($order_info['jifencomment']))
            {
                Model_Member::operate_jifen($order_info['memberid'], $order_info['jifencomment'], 2);
                St_Product::add_jifen_log($order_info['memberid'], "评论赠送积分{$order_info['jifencomment']}", $order_info['jifencomment'], 2);
            }
        }
    }


}