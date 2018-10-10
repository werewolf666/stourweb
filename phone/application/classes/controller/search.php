<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search extends Stourweb_Controller
{

    public static $typeArr = array(
        '1' => array('typeid' => 1, 'channelname' => '线路'),
        '2' => array('typeid' => 2, 'channelname' => '酒店'),
        '3' => array('typeid' => 3, 'channelname' => '车辆'),
        '4' => array('typeid' => 4, 'channelname' => '攻略'),
        '5' => array('typeid' => 5, 'channelname' => '门票'),
        '6' => array('typeid' => 6, 'channelname' => '相册'),
        '8' => array('typeid' => 8, 'channelname' => '签证'),
        '13' => array('typeid' => 13, 'channelname' => '团购'),
        '104' => array('typeid' => 104, 'channelname' => '邮轮'),
        '105' => array('typeid' => 105, 'channelname' => '活动'),
        '106' => array('typeid' => 106, 'channelname' => '导游'),
        '111' => array('typeid' => 111, 'channelname' => '保险'),
        '113' => array('typeid' => 113, 'channelname' => '特产'),
        '114' => array('typeid' => 114, 'channelname' => '户外活动')
    );

    public function before()
    {
        parent::before();
    }

    public function action_index()
    {
        $keyword = Common::remove_xss(trim(Arr::get($_GET, 'keyword')));
        $page = intval(Arr::get($_GET, 'page'));
        $typeid = intval(Arr::get($_GET, 'typeid'));
        $page = $page < 1 ? 1 : $page;

        $count = $this->get_result_count($keyword, 0);
        $typeArr = NULL;
        //全部
        $temp['typeid'] = 0;
        $temp['channelname'] = '全部';
        $temp['count'] = $count;
        $typeArr[] = $temp;
        //具体栏目搜索结果
        $channels = DB::query(Database::SELECT, "select * from sline_m_nav where m_isopen=1 and m_issystem=1 and m_typeid in (select distinct typeid from sline_search)")->execute()->as_array();
        foreach ($channels as $row) {
            $item = array();
            $item['typeid'] = $row['m_typeid'];
            $item['channelname'] = $row['m_title'];
            $item['count'] = $this->get_result_count($keyword, $row['m_typeid']);

            if (empty($item['count'])) {
                continue;
            }
            $typeArr[] = $item;
        }

        $this->assign('typeid', $typeid);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('typeArr', $typeArr);
        $this->display('search/index', 'cloudsearch_index');
    }


    /**
     * @function 获取列表背景类型
     * @param $typeid
     */
    private function get_bg_type($typeid)
    {
        switch ($typeid) {
            case 1:
                return 2;
                break;
            case 2:
                return 3;
                break;
            case 3:
                return 4;
                break;
            case 4:
                return 1;
                break;
            case 5:
                return 7;
                break;
            case 6:
                return 8;
                break;
            case 8:
                return 5;
                break;
            case 13:
                return 6;
                break;
            case 104:
                return 12;
                break;
            case 105:
                return 11;
                break;
            case 106:
                return 10;
                break;
            case 111:
                return 14;
                break;
            case 113:
                return 13;
                break;
        }
    }

    /**
     * ajax请求 加载更多
     */
    public function action_ajax_search_more()
    {
        $keyword = Common::remove_xss(trim(Arr::get($_GET, 'keyword')));
        $page = intval(Arr::get($_GET, 'page'));
        $typeid = intval(Arr::get($_GET, 'typeid'));
        $list = $this->get_result($page, $keyword, $typeid);
        $count = $this->get_result_count($keyword, 0);

        $typeArr = NULL;
        //全部
        $temp['typeid'] = 0;
        $temp['channelname'] = '全部';
        $temp['count'] = $count;
        $typeArr[] = $temp;
        //具体栏目搜索结果
        $channels = DB::query(Database::SELECT, "select * from sline_m_nav where m_isopen=1 and m_issystem=1 and m_typeid in (select distinct typeid from sline_search)")->execute()->as_array();
        foreach ($channels as $row) {
            $item = array();
            $item['typeid'] = $row['m_typeid'];
            $item['channelname'] = $row['m_title'];
            $item['count'] = $this->get_result_count($keyword, $row['m_typeid']);

            if (empty($item['count'])) {
                continue;
            }
            $typeArr[] = $item;
        }

        $page = count($list) > 0 ? intval($page) + 1 : -1;
        echo json_encode(array('list' => $list, 'typeArr' => $typeArr, 'page' => $page));
    }


    /**
     * @param $page
     * @return mixed
     * 获取搜索结果
     */

    public function get_result($page, $keyword, $typeid = 0)
    {
        $pageSize = 10;
        $page = empty($page) ? 1 : $page;
        $offset = ($page - 1) * $pageSize;
        $valueArr = array();
        $w = '';
        $valueArr = array();
        if (preg_match('`([a-zA-Z])(\d{3,8})`', $keyword, $preg)) {
            $id = ltrim($preg[2], '0');
            $w = " tid=:id and ishidden=0";
            $valueArr[':id'] = $id;
        }
        else if (is_numeric($keyword)) {
            $result = St_Product::product_series($keyword, '', true);
            $w = "  tid='{$result['id']}' and typeid='{$result['typeid']}' ";
        }
        else {
            //关键字查询
            $w = " title like :keyword and ishidden=0";
            $valueArr[':keyword'] = '%' . $keyword . '%';

        }
        if (!empty($typeid)) {
            $w .= " and typeid=:typeid";
            $valueArr[':typeid'] = $typeid;
        }
        $sql = "SELECT * FROM `sline_search` WHERE $w LIMIT $offset,$pageSize";
        $query = DB::query(Database::SELECT, $sql)->parameters($valueArr);
        $list = $query->execute()->as_array();
        foreach ($list as $k => $v) {
            $list[$k]['url'] = $this->getUrl($v['webid'], $v['aid'], $v['typeid']);
            $list[$k]['litpic'] = !empty($v['litpic']) ? Common::img($v['litpic']) : '';
            $list[$k]['channelname'] = self::$typeArr[$v['typeid']]['channelname'];
            $list[$k]['description'] = Common::cutstr_html($v['description'], 50);
            $list[$k]['bg_type'] = $this->get_bg_type($v['typeid']);
        }


        return $list;
    }

    /**
     * @param $keyword
     * @param int $typeid
     * @return mixed
     */
    public function get_result_count($keyword, $typeid = 0)
    {

        $model_info = ORM::factory('model', $typeid)->as_array();
        $table = 'sline_search';
        if (!empty($model_info['id'])) {
            $table = 'sline_' . $model_info['maintable'];
        }

        $ck_sql = "show columns from {$table} like 'title'";
        $ch_row = DB::query(1, $ck_sql)->execute()->current();
        if (empty($ch_row)) {
            return 0;
        }

        $valueArr = array();

        $w = '';
        $valueArr = array();
        if (preg_match('`([a-zA-Z])(\d{3,8})`', $keyword, $preg)) {
            $id_field = $typeid == 0 ? 'tid' : 'id';
            $id = ltrim($preg[2], '0');
            $w = " {$id_field}=:id and ishidden=0";
            $valueArr[':id'] = $id;
        }
        else if (is_numeric($keyword)) {

            $result = St_Product::product_series($keyword, '', true);
            if ($typeid == 0) {
                $w = " tid='{$result['id']}' and typeid='{$result['typeid']}' ";
            }
            else {
                $w = " id='{$result['id']}' ";
            }

        }
        else {
            //关键字查询
            $w = " title like :keyword and " . ($typeid != 101 ? 'ishidden=0' : 'status=1');
            $valueArr[':keyword'] = '%' . $keyword . '%';
        }

        /* if($table == 'sline_model_archive')
         {
             $w.= " and typeid=:typeid ";
             $valueArr[':typeid']=$typeid;
         }*/


        $sql = "SELECT count(0) as num FROM `{$table}` WHERE $w ";
        $query = DB::query(Database::SELECT, $sql)->parameters($valueArr);


        $data = $query->execute()->as_array();
        return $data[0]['num'];
    }

    /**
     * @param $aid
     * @param $typeid
     * @return string
     * 获取产品地址.
     */

    public function getUrl($webid, $aid, $typeid)
    {
        $cmsUrl = Common::get_web_url($webid);
        $url = '';
        switch ($typeid) {
            case 1:
                $url = $cmsUrl . '/lines/show_' . $aid . '.html';
                break;
            case 2:
                $url = $cmsUrl . '/hotels/show_' . $aid . '.html';
                break;
            case 3:
                $url = $cmsUrl . '/cars/show_' . $aid . '.html';
                break;
            case 4:
                $url = $cmsUrl . '/raiders/show_' . $aid . '.html';
                break;
            case 5:
                $url = $cmsUrl . '/spots/show_' . $aid . '.html';
                break;
            case 6:
                $url = $cmsUrl . '/photos/show_' . $aid . '.html';
                break;
            case 8:
                $url = $cmsUrl . '/visa/show_' . $aid . '.html';
                break;
            case 13:
                $url = $cmsUrl . '/tuan/show_' . $aid . '.html';
                break;
            case 104:
                $url = $cmsUrl . '/ship/show_' . $aid . '.html';
                break;
            default:
                $sql = "SELECT pinyin FROM `sline_model` where id={$typeid} ";
                $arr = DB::query(1, $sql)->execute()->as_array();
                $url = $cmsUrl . '/' . $arr[0]['pinyin'] . '/show_' . $aid . '.html';
                break;

        }
        return $url;
    }


}