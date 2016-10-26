<?php
namespace Lib\Core;


define('BASE_ROOT', substr(__DIR__, 0, -8));
define('LIB_ROOT', BASE_ROOT.'Lib/');



require_once LIB_ROOT.'Core/Autoload.php';
spl_autoload_register( array(Autoload::class, 'load'));

use Rdd\dd;




?>