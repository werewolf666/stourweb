<?php

//kohana目录
define('TOOLS_KOHANA_PATH', dirname(dirname(__FILE__)) . '/'.'core/');
//TOOLS目录
define('TOOLS_PATH', dirname(__FILE__) . '/');
//TOOLS 扩展目录
define('TOOLS_Lib', TOOLS_PATH . 'lib/');
//Tools 通用目录
define('TOOLS_COMMON', TOOLS_PATH . 'common/');
//Tools 通用配置
define('TOOLS_CONF', TOOLS_PATH . 'conf/');
//cookies 加密参数
define('COOKIES_SALT', 'stourwebcms');
//cookies 路径
define('COOKIES_PATH', '/');
//是否设置系统运行在开发模式
define('DEVELOPMENT_MODE', false);
//定义统一缓存目录
define('CACHE_DIR',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
//定义统一日志目录
define('LOGS_DIR',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR);



