<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Question 问答
 */
class Controller_Question extends Stourweb_Controller
{
    private $_typeid = 10;   //产品类型

    public function before()
    {
        parent::before();

        $channelname = Model_Nav::get_channel_name_mobile($this->_typeid);
        $this->assign('typeid',$this->_typeid);
        $this->assign('channelname',$channelname);
    }
    /**
     * 首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo_mobile($this->_typeid);
        $page = Common::remove_xss(intval(Arr::get($_GET, 'page')));
        $page = $page < 1 ? 1 : $page;
        $this->assign('seoinfo',$seoinfo);
        $this->assign('page',$page);
        $this->display('question/index');
    }

    /**
     * 首页
     */
    public function action_add()
    {
        $seoinfo = Model_Nav::get_channel_seo_mobile($this->_typeid);
        $this->assign('seoinfo',$seoinfo);
        $this->display('question/add');
    }

    public function action_ajax_checkValidateCode()
    {
        $validateCode = Common::remove_xss(Arr::get($_POST, 'ValidateCode'));
        if(Captcha::valid($validateCode))
            echo "true";
        else
            echo "false";
    }

    public function action_save()
    {
        $title = Common::remove_xss(Arr::get($_POST, 'txtTitle'));
        $content = Common::remove_xss(Arr::get($_POST, 'txtContent'));
        $nickname = Common::remove_xss(Arr::get($_POST, 'txtNickname'));
        if(empty($nickname))
            $nickname = "匿名";

        $validateCode = Common::remove_xss(Arr::get($_POST, 'txtValidateCode'));
        $tel = Common::remove_xss(Arr::get($_POST, 'txtTel'));

        if(!Captcha::valid($validateCode))
        {
            Common::message(array('message' => __("error_code"), 'jumpUrl' => $this->cmsurl. 'questions/add'));
            exit;
        }
        Common::session('captcha_response', null);
        $member = Common::session('member');

        $validataion = Validation::factory($this->request->post());
        $validataion->rule('txtTitle', 'not_empty');
        $validataion->rule('txtContent', 'not_empty');
        $validataion->rule('txtTel', 'not_empty');
        $validataion->rule('txtTel', 'phone');
        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($error);
            if($keys[0] == 'txtTitle')
            {
                Common::message(array('message' => __("error_question_title_not_empty"), $this->cmsurl . 'questions/add'));
            }
            elseif($keys[0] == 'txtContent')
            {
                Common::message(array('message' => __("error_question_content_not_empty"), $this->cmsurl . 'questions/add'));
            }
            elseif($keys[0] == 'txtTel')
            {
                Common::message(array('message' => __("error_linktel_phone"), $this->cmsurl . 'questions/add'));
            }
            else
            {
                Common::message(array('message' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), $this->cmsurl . 'questions/add'));
            }
            exit;
        }

        list($insert_id, $total_rows) = DB::insert('question', array('content', 'nickname', 'ip','status', 'addtime', 'webid', 'phone', 'title','questype','memberid'))
            ->values(array($content, $nickname, Common::GetIP(), '0', time(), $GLOBALS['sys_webid'], $tel, $title,'1',$member['mid']))
            ->execute();
        if ($total_rows)
        {

            $model_info = Model_Model::get_module_info(10);
            $jifen = Model_Jifen::reward_jifen('sys_write_' . $model_info['pinyin'], $member['mid']);
            if (!empty($jifen)) {
                St_Product::add_jifen_log($member['mid'], '发布' . $model_info['modulename'] . '送积分' . $jifen, $jifen, 2);
            }

            Common::message(array('message' => __("error_question_success_add"), 'jumpUrl' => $this->cmsurl . 'questions'));
        }
        else
        {
            Common::message(array('message' => __("error_question_error_add"), 'jumpUrl' => $this->cmsurl . 'questions/add'));
        }
    }
    /**
     * 首页
     */
    public function action_ajax_question_search($pagesize=10)
    {
        if (!$this->request->is_ajax()) return '';
        $page = Common::remove_xss(intval(Arr::get($_GET, 'page')));
        $page = $page < 1 ? 1 : $page;
        $offset = (intval($page) - 1) * $pagesize;
        //$status = Common::remove_xss(Arr::get($_GET, 'status'));
        //$webid = Common::remove_xss(Arr::get($_GET, 'webid'));
        $keyword = Common::remove_xss(Arr::get($_GET, 'keyword'));

        $data = Model_Question::search_question(1, 0, $keyword, $offset, $pagesize);
        $count = Model_Question::search_question_count(1, 0, $keyword, 0, 1);

        if ($count <= 0)
        {
            echo json_encode(false);
            return;
        }

        foreach($data as &$v)
        {
            if(!empty($v['replytime']))
            {
                $v['replytime'] = date('Y-m-d', $v['replytime']);
            }
            if(!empty($v['addtime']))
            {
                $v['addtime'] = date('Y-m-d', $v['addtime']);
            }
			$v['replycontent'] = strip_tags($v['replycontent'],'<img>');
            $v['replycontent']=preg_replace('~src="[^http](.*?)\.(jpg|gif|png|jpeg)"~', "src=\"{$GLOBALS['cfg_m_img_url']}/\\1_511x0.\\2\"", $v['replycontent']);

        }
        if(count($data) < $pagesize)
        {
            $page = -1;
        }
        else
        {
            $page++;
        }
        echo json_encode(array('list' => $data, 'count' => $count[0]['num'], 'page' => $page));
    }

    //产品问答列表
    public function action_product_question_list()
    {
        $articleid = intval(Arr::get($_GET,'articleid'));//文章id
        $typeid = intval(Arr::get($_GET,'typeid'));//栏目ID
        $this->assign('articleid',$articleid);
        $this->assign('typeid',$typeid);
        $this->display('question/product_list');

    }
    //产品问答读取
    public function action_ajax_product_question_more()
    {
        if (!$this->request->is_ajax())
            return '';
        $pagesize = 5;
        $typeid = intval(Arr::get($_GET,'typeid'));
        $articleid = intval(Arr::get($_GET,'articleid'));
        $pageno = intval(Arr::get($_GET,'page'));
        $pageno = $pageno <= 0 ? 1 : $pageno;

        $out = Model_Question::search_result($pageno,$pagesize,0,$typeid,$articleid);
        foreach($out['list'] as &$row)
        {
            $row['pubdate'] = date('Y-m-d H:i',$row['addtime']);
            $row['replydate'] = date('Y-m-d H:i',$row['replytime']);
            $row['nickname'] = empty($row['nickname']) ? '匿名' : $row['nickname'];
        }
        $out['page'] = count($out['list']) < $pagesize ? -1 : $pageno + 1;
        echo json_encode($out);

    }

    //写产品问答
    public function action_product_question_write()
    {
        $articleid = intval(Arr::get($_GET,'articleid'));//文章id
        $typeid = intval(Arr::get($_GET,'typeid'));//栏目ID
        if(empty($articleid) ||empty($typeid))
        {
            exit();
        }
        //表单校验码
        $token = md5(time());
        Common::session('token', $token);
        $this->assign('token', $token);
        $this->assign('articleid',$articleid);
        $this->assign('typeid',$typeid);
        $this->assign('member',Product::get_login_user_info());
        $this->display('question/product_write');



    }

    //产品问答保存
    public function action_ajax_product_question_save()
    {
        $token = Arr::get($_POST,'token');
        $articleid = intval(Arr::get($_POST,'articleid'));
        $typeid = intval(Arr::get($_POST,'typeid'));
        $checkcode = Common::remove_xss(Arr::get($_POST,'checkcode'));
        $is_anonymous = intval(Arr::get($_POST,'is_anonymous'));
        $content = Common::remove_xss(Arr::get($_POST,'content'));
        if(Common::session('token')!=$token)
        {
            echo json_encode(array('status'=>0,'msg'=>'安全校验码出错'));
            exit;
        }

        //验证码验证
        if(!Captcha::valid($checkcode) || empty($checkcode))
        {
            echo json_encode(array('status'=>0,'msg'=>'验证码错误'));
            exit;
        }
        else
        {
            //清空验证码
            Common::session('captcha_response',null);
        }

        $arr = array();
        $arr['typeid'] = $typeid;
        $arr['productid'] = $articleid;
        $arr['content'] = $content;
        $arr['addtime'] = time();
        //是否匿名
        if(!$is_anonymous)
        {
            $member = Product::get_login_user_info();
            $arr['memberid'] = $member['mid'];
            $arr['nickname'] = $member['nickname'];
        }
        else
        {
            $arr['memberid'] = 0;
            $arr['nickname'] = '匿名';
        }
        $flag = DB::insert('question',array_keys($arr))->values(array_values($arr))->execute();
        if($flag)
        {
            $model_info = Model_Model::get_module_info(10);
            $jifen = Model_Jifen::reward_jifen('sys_write_' . $model_info['pinyin'], $arr['memberid']);
            if (!empty($jifen)) {
                St_Product::add_jifen_log($arr['memberid'], '发布' . $model_info['modulename'] . '送积分' . $jifen, $jifen, 2);
            }
        }
        echo json_encode(array('status'=>$flag));
        exit;

    }




}