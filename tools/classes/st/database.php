<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 数据库相关处理类
 */
class St_Database{
    public static function is_table_exists($table)
    {
        $result = DB::query(Database::SELECT,"show tables like '{$table}'")->execute()->current();
        return !empty($result)?true:false;

    }
}