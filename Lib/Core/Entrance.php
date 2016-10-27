<?php
use Lib\Core\Autoload;





//定义常亮
define('BASE_ROOT', substr(__DIR__, 0, -8));
define('LIB_ROOT', BASE_ROOT.'Lib/');
define('CONFIG_ROOT', BASE_ROOT.'config/');
define('VENDOR_ROOT', BASE_ROOT.'vendor/');


//主自动加载

require_once LIB_ROOT.'Core/Autoload.php';
spl_autoload_register( array(Autoload::class, 'load'));







use Lib\Database\Mysqli;
//Autoload::table(Mysqli::class);

new Mysqli;






//composer依赖的自动加载
require_once VENDOR_ROOT.'autoload.php';





?>