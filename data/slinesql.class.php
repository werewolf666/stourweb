<?php if (!defined('DATAPATH')) exit("Request Error!");

require_once(dirname(__FILE__) . '/sql.class.php');
// +----------------------------------------------------------------------
// |MySQL操作类
// +----------------------------------------------------------------------
class SlineSQL extends MySQL
{
    /**
     * +----------------------------------------------------------
     * 错误信息
     * +----------------------------------------------------------
     * @access public
     * +----------------------------------------------------------
     * @param
    +----------------------------------------------------------
     */
    public function error()
    {
        $error = '';
        $this->error = mysql_error($this->conn);
        if ('' != $this->error)
        {
            $error = mysql_errno() . ':' . $this->error;
            exit($error);
        }
        //exit($error);
    }
}

require_once(DATAPATH . '/common.inc.php');
$mysql = new SlineSQL($cfg_dbhost, $cfg_dbuser, $cfg_dbpwd, $cfg_dbname);