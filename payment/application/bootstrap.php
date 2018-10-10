<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT))
{
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
} else
{
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
//date_default_timezone_set('America/Chicago');
date_default_timezone_set('Asia/Shanghai');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('ch'); //读取语言包

if (isset($_SERVER['SERVER_PROTOCOL']))
{
    // Replace the default protocol.
    HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}
Kohana::$environment = DEVELOPMENT_MODE ? Kohana::DEVELOPMENT : Kohana::PRODUCTION;
/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
//缓存目录,日志目录指定
$cache_dir = CACHE_DIR.'payment';
$logs_dir = LOGS_DIR.'payment';
if(!file_exists($cache_dir))
{
    mkdir($cache_dir,0777,true);
}
if(!file_exists($logs_dir))
{
    mkdir($logs_dir,0777,true);
}


Kohana::init(array(
    'base_url' => '/payment/',
    'index_file' => '',
    'errors'=> DEVELOPMENT_MODE,
    'cache_dir' => $cache_dir
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File($logs_dir));

Kohana::$log_errors = false;//关闭日志记录

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
$default_modules = array(
    // 'auth'       => MODPATH.'auth',       // Basic authentication
    'cache' => MODPATH . 'cache',      // Caching with multiple backends
    'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    'database' => MODPATH . 'database',   // Database access
    'image' => MODPATH . 'image',      // Image manipulation
    // 'minion'     => MODPATH.'minion',     // CLI Tasks
    'orm' => MODPATH . 'orm',       // Object Relationship Mapping
    'pagination' => MODPATH . 'pagination', //分页
    'captcha' => MODPATH . 'captcha',//验证码类
    //'unittest'   => MODPATH.'unittest',   // Unit testing
    //'userguide' => MODPATH . 'userguide' // User guide and API documentation
);
$plugins_modules = include(BASEPATH.'/data/module.php');
foreach($plugins_modules as $plugin_key=>$plugin)
{
    $plugins_modules[$plugin_key] = BASEPATH.$plugin;
}
$default_modules =is_array($plugins_modules)?array_merge($default_modules,$plugins_modules):$default_modules;
Kohana::modules($default_modules);



/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<param>)))', array(
    'param' => '.*'
))->defaults(array(
    'controller' => 'index',
    'action' => 'index',
));
/**
 * Initialization configuration
 */
Common::init_config();
$cfg_basehost = (St_Functions::is_SSL() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']; //网站域名
$cfg_cmspath = '/';
$cfg_default_templet = '/default/';
$cfg_public_url = '/public/';
$base_url = Common::get_main_host();
//Cookie设置
Cookie::$salt = 'stourwebcms';
cookie::$path = '/';
Cookie::$domain = Common::cookie_domain();