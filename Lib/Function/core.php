<?php
use Lib\Core\Autoload;

function conf($name){

    return Autoload::conf($name);


}

function import($path){

    $path = BASE_ROOT.preg_replace('#/+#','',$path);
    
    return Autoload::import($path);

}


function table($class){

    return Autoload::table($class);
}