<?php
namespace Lib\Core;
use Exception;

class Autoload{

	private static $_imports = array();

	public static function import($path,$e=false){
		$path = $root.'\\class\\'.($type?$type.'\\':'');
		if(self::$_imports[$path])return true;
		if(is_file($path)){
			require $path;return self::$_imports[$path] = true;
        }elseif(!$e)
            return $_imports[$key] = false;
		else{
            $path = str_ireplace(BASE_ROOT,'',$path);
            throw new Exception('file lost: '.$path);
        }
	}
	public static function load($class){

        return self::import(BASE_ROOT.(strpos($class, '\\')?'':'Lib/Base/'.str_replace('\\','/',$class)).'.php',true);
        
    }
}



?>