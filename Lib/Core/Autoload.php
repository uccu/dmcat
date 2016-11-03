<?php
namespace Lib\Core;
use E;

class Autoload{

	private static $_imports = array();
	private static $_tables  = array();
	private static $_configs  = array();

	public static function import($path,$force = true){

		
		if(isset(self::$_imports[$path]))return true;

		if(is_file($path)){

			require_once $path;
			return self::$_imports[$path] = true;

        }elseif($force)

            return self::$_imports[$path] = false;

		else{

            $path = str_ireplace(BASE_ROOT,'',$path);
            E::throw('file lost: '.$path,1);

        }
	}
	public static function load($class,$force = false){
		
		
        return self::import( BASE_ROOT.(strpos($class, '\\')?'':'Lib/Base/').str_replace('\\','/',$class).'.php',$force);
        
    }

	public static function table($class,$force = false){

		$class = str_replace('/','\\',$class);

		if(isset( self::$_tables[$class] ))
			return self::$_tables[$class];
		$z = self::load($class,$force);

		if(!$z){
			self::$_tables[$class] = false;
			return false;
		}

		self::$_tables[$class] = new $class();

		return self::$_tables[$class];

	}

	public static function conf($name){

		if(isset(self::$_configs[$name]))return self::$_configs[$name];

		$path = CONFIG_ROOT.$name.'.conf';
		$file = fopen($path, "r");

		$config = (object)array();
		if(!$file)return $config;
		
		while(!feof($file)) {

			$line = fgets($file);
			$line = trim( preg_replace('/#.*$/','',$line) );
			if(!$line)continue;
			if(!preg_match('#^[a-z_]#i',$line))continue;
			if(!preg_match('#^([a-z_][a-z_0-9]*)[ \t]*=[ \t]*(.*)$#i',$line,$match))continue;

			list(,$key,$value) = $match;
			$key = strtoupper($key);
			if(!is_null($config->$key)){
				if(is_array($config->$key))$config->$key[] = $value;
				else{
					$config->$key = array($config->$key);
					$config->$key[] = $value;
				}
			}else{
				$config->$key = $value;
			}

		}

		fclose($file);
		self::$_configs[$name] = $config;
		return $config;

	}

	

	public static function extension_check(){

		$conf = self::conf('Extension');
		foreach($conf->EXT as $e){
			if(!extension_loaded($e))
				E::throw($e.' Extension Not Loaded');
		}

	}


	public static function field($class){






	}


}



?>