<?php
namespace Lib\Core;


define('BASE_ROOT', substr(__DIR__, 0, -8));
define('LIB_ROOT', BASE_ROOT.'Lib/');
define('VENDOR_ROOT', BASE_ROOT.'vendor/');


require_once LIB_ROOT.'Core/Autoload.php';
spl_autoload_register( array(Autoload::class, 'load'));
require_once VENDOR_ROOT.'autoload.php';


use Monolog\Logger;
use Monolog\Handler\StreamHandler;


$log = new Logger('name');
$log->pushHandler(new StreamHandler('app.log', Logger::WARNING));

$log->addWarning('Foo');

use Lib\Core\Autoload;

echo Autoload::class;

?>