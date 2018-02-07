<?php


error_reporting(0);

//定义常量

define('BASE_ROOT', substr(__DIR__, 0, -8));
define('LIB_ROOT', BASE_ROOT.'Lib/');
define('CONFIG_ROOT', BASE_ROOT.'Config/');
define('VENDOR_ROOT', BASE_ROOT.'vendor/');
define('PUBLIC_ROOT', BASE_ROOT.'Public/');
define('VIEW_ROOT', BASE_ROOT.'View/');
define('STORAGE_ROOT', BASE_ROOT.'Storage/');
define('LOG_ROOT', BASE_ROOT.'Log/');

define('OPTION_OFF',0);
define('OPTION_ON',1);
define('OPTION_DEBUG',2);



//主自动加载
//composer依赖的自动加载

require_once VENDOR_ROOT.'autoload.php';


//错误机制

set_exception_handler(array('Uccu\DmcatTool\Tool\E','handleException'));
set_error_handler(array('Uccu\DmcatTool\Tool\E','handleError'));
register_shutdown_function(array('Uccu\DmcatTool\Tool\E', 'handleShutdown'));







// Config设置
use Uccu\DmcatTool\Tool\LocalConfig as Config;
use Uccu\DmcatTool\Tool\E;
use Uccu\DmcatHttp\Route;
Config::$_CONFIG_ROOT = CONFIG_ROOT;
E::$_BASE_ROOT = BASE_ROOT;
E::$_LOG_ROOT = LOG_ROOT;
//定义请求路径

if(!isset($argc))
    define(
        'REQUEST_PATH',
        !empty($_SERVER['PATH_INFO'])?substr($_SERVER['PATH_INFO'],1):($_SERVER['REQUEST_URI']?preg_replace('#\?.*$#','',substr($_SERVER['REQUEST_URI'],1)):''));
else define('REQUEST_PATH',$argv[1]);




//设置时区
date_default_timezone_set(Config::get('TIMEZONE'));

define('TIME_NOW', time());
define('TIME_TODAY', strtotime(date('Y-m-d',TIME_NOW)));
define('DATE_TODAY', date('Ymd'));
define('TIME_YESTERDAY', TIME_TODAY-3600*24);




//进行压缩处理，在这之前不允许输入任何字符，所以要注意不要使用 UTF-8 with BOM的编码

if(Config::get('OB_GZHANDLER')){
    ob_start('ob_gzhandler');
}


//处理请求路由
Route::getSingleInstance()->parse();

//输出内容
while (0 < ob_get_level()) {
    ob_end_flush();
}









?>