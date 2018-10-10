<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 数据库备份操作类
 * */

class Model_Backup
{
    /**
     * 备份数据库...
     */
    private $_sqlfiledatapath = "";
    private $_sqlfilestructpath = "";
    private $_sqlstringbufferlength = 0;

    private $_tablelist = null;

    public $timestamp = "";

    public function __construct($ts = '')
    {
        if (empty($ts))
            $ts = time();
        $this->timestamp = $ts;

        $dir = BASEPATH . '/data/backup/' . $this->timestamp;
        $this->_sqlfiledatapath = $dir . '/data/';
        if (!file_exists($this->_sqlfiledatapath))
            mkdir($this->_sqlfiledatapath, 0777, true);

        $this->_sqlfilestructpath = $dir . '/tables/';
        if (!file_exists($this->_sqlfilestructpath))
            mkdir($this->_sqlfilestructpath, 0777, true);

        $this->_sqlstringbufferlength = intval(10240 * 1024 * 0.982);
    }

    public function tablelist()
    {
        if ($this->_tablelist == null)
        {
            $this->_tablelist = array();

            $tables = DB::query(Database::SELECT, "show tables")->execute()->as_array();

            foreach ($tables as $table)
            {
                foreach ($table as $tablename)
                {
                    if (in_array($tablename, array('sline_stats', 'sline_stat', 'sline_talist', 'sline_tmptag', 'sline_search', 'sline_user_log')))
                        continue;

                    $tableinfo = DB::query(Database::SELECT, "show create table {$tablename}")->execute()->as_array();

                    foreach ($tableinfo[0] as $key => $value)
                    {
                        if ($key == 'Table')
                        {
                            $this->_tablelist[] = $value;
                        }
                    }
                }
            }

        }

        return $this->_tablelist;
    }

    public function backupAll()
    {
        $tables = $this->tablelist();
        foreach ($tables as $table)
        {
            $this->backup($table);
        }

        $storedir = dirname(dirname($this->_sqlfilestructpath)) . "/";
        $file = $storedir . $this->timestamp . ".zip";

        if (!class_exists("PclZip"))
        {
            include(PUBLICPATH . '/vendor/pclzip.lib.php');
        }
        $archive = new PclZip($file);
        $archive->create(array(dirname($this->_sqlfilestructpath)), PCLZIP_OPT_REMOVE_PATH, $storedir);

        Common::rrmdir(dirname($this->_sqlfilestructpath));
    }

    public static function recover($backFolderPath, array $tables)
    {
        $tablenamejoin = implode(',', $tables);
        $rs = DB::query(Database::INSERT, "DROP TABLE IF EXISTS {$tablenamejoin}")->execute();
        if ($rs == false)
        {
            return false;
        }

        //执行SQL
        foreach ($tables as $tablename)
        {
            $structpath = $backFolderPath . '/tables/' . $tablename . '.sql';
            $datapath = $backFolderPath . '/data/' . $tablename . '.sql';
            $files = array($structpath, $datapath);

            foreach ($files as $file)
            {
                $str = file_get_contents($file);
                $sqlarr = explode('-- <xjx> --', $str);

                foreach ($sqlarr as $sql)
                {
                    $sql = trim($sql);
                    if (!empty($sql) && $sql != '')
                    {
                        $rs = DB::query(Database::INSERT, $sql)->execute();
                        if ($rs == false)
                        {
                            return false;
                        }
                    }
                }
            }

        }
        return true;
    }

    private function backup($table)
    {
        $this->sqlcreate($table);
        $this->sqlinsert($table);
    }

    private function writeSqlFile($filename, $data)
    {
        $fp = fopen($filename, 'a+');

        flock($fp, 2);
        fwrite($fp, $data);
        fclose($fp);
    }

    private function sqlcreate($table)
    {
        $temp = DB::query(Database::SELECT, "SHOW CREATE TABLE {$table}")->execute()->as_array();
        $tableinfo = $temp[0];

        $sql = "-- 表的结构：{$tableinfo['Table']} --\r\n";
        $sql .= "{$tableinfo['Create Table']}";
        $sql .= ";-- <xjx> --\r\n\r\n";

        $sqlfile = $this->_sqlfilestructpath . "/{$table}.sql";
        $this->writeSqlFile($sqlfile, $sql);
    }


    private function sqlinsert($table)
    {
        $sqlfile = $this->_sqlfiledatapath . "/{$table}.sql";

        $totalarr = DB::query(Database::SELECT, "SELECT count(*) as totalcount FROM {$table}")->execute()->as_array();
        $total = $totalarr[0]['totalcount'];
        if ($total <= 0)
            return;

        $sql = "-- 表的数据：{$table} --\r\n";
        $pagesize = 1000;
        $pagecount = ((int)($total / $pagesize)) + 1;
        for ($page = 1; $page <= $pagecount; $page++)
        {
            $startIndex = ($page - 1) * $pagesize;
            $data = DB::query(Database::SELECT, "SELECT * FROM {$table} LIMIT {$startIndex},{$pagesize}")->execute()->as_array();

            foreach ($data as $datarow)
            {
                $rowsql = "INSERT INTO `{$table}` VALUES\r\n";

                $rowsql .= '(';
                foreach ($datarow as $val)
                {
                    if ($val === null)
                    {
                        $rowsql .= 'NULL,';
                    } else
                    {
                        $val = mysql_real_escape_string($val);
                        $rowsql .= "'{$val}',";
                    }
                }
                $rowsql = mb_substr($rowsql, 0, -1);
                $rowsql .= ')';

                $rowsql .= ";-- <xjx> --\r\n\r\n";

                $sql .= $rowsql;
                if (strlen($sql) >= $this->_sqlstringbufferlength)
                {
                    $this->writeSqlFile($sqlfile, $sql);
                    $sql = "";
                }
            }
        }
        $this->writeSqlFile($sqlfile, $sql);
    }

}