<?php

use Lib\Core\Exc;
use Lib\Core\Autoload;
use Config;


error_reporting(-1);

//定义常亮
define('TIME_NOW', time());
define('TIME_TODAY', strtotime(date('Y-m-d',TIME_NOW)));
define('TIME_YESTERDAY', TIME_TODAY-3600*24);
define('BASE_ROOT', substr(__DIR__, 0, -8));
define('LIB_ROOT', BASE_ROOT.'Lib/');
define('CONFIG_ROOT', BASE_ROOT.'config/');
define('VENDOR_ROOT', BASE_ROOT.'vendor/');
define('REQUEST_PATH',$_SERVER['REDIRECT_URL']);



//主自动加载

require_once LIB_ROOT.'Core/Autoload.php';
spl_autoload_register( array(Autoload::class, 'load'));



//错误机制

set_exception_handler(array(Exc::class,'handleException'));
set_error_handler(array(Exc::class,'handleError'));
register_shutdown_function(array(Exc::class, 'handleShutdown'));



//composer依赖的自动加载
require_once VENDOR_ROOT.'autoload.php';





?>