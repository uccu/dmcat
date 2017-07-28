<?php
namespace Lib\Core;
use E;
use Uccu\DmcatTool\Tool\LocalConfig as Config;

class Autoload{

	private static $_imports = array();
	private static $_tables  = array();
	private static $_configs  = array();

	

	

	public static function extension_check(){

		$conf = Config::extension();
		if(!is_array($conf->EXT))$conf->EXT = array($conf->EXT);
		foreach($conf->EXT as $e){
			if(!extension_loaded($e))
				E::throwEx($e.' Extension Not Loaded');
		}

	}




}



?>