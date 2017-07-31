<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Uccu\DmcatTool\Tool\LocalConfig as Config;
class E extends Exception{

	

	final public static function handleException($e,$line=null){
		
		return self::handle($e->getCode(),$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTrace(),'BASE',$line);
		
	}

	final public static function handle($code,$message,$file,$line,$trace,$base = 'BASE',$c = null){

		

		if(!is_null($c)){
			$file = $trace[$c]['file'];
			$line = $trace[$c]['line'];
		}else{
			$trace[0]['file'] = $file;
			$trace[0]['line'] = $line;
		}

		$file = str_ireplace(array(BASE_ROOT,'.php'),'',$file);
		$file = str_ireplace('/','\\',$file);

		$str =  "$base EXCEPTION : [$code][$message] FILE [$file] LINE [$line]";
		
		

		

		// create a log channel
		
		$elog = Config::get('ERROR_LOG');
		if(!$elog){

		}elseif(is_writable(LOG_ROOT) && 
			(!file_exists(LOG_ROOT.DATE_TODAY.'.log') || is_writable(LOG_ROOT.DATE_TODAY.'.log'))
		){
			$log = new Logger($base);
			$log->pushHandler(new StreamHandler(LOG_ROOT.DATE_TODAY.'.log', Logger::WARNING));
			// add records to the log
			
			$log->addError($str);
			foreach($trace as $k=>$c){
				$file = $c['file'];
				$line = isset($c['line']) ? $c['line'] : '';
				$file = str_ireplace(array(BASE_ROOT,'.php'),'',$file);
				$file = str_ireplace('/','\\',$file);
				$str2 =  "EXCEPTION FILE [$file] LINE [$line]";
				$log->addWarning($str2,[$k]);
			}
		}else{
			$str =  "LOG|".$str;
		}
		

		


		$type = Config::get('EXCEPTION_OUTPUT_TYPE');

		if(is_null($type) || $type == 'string')echo $str;
			
		else AJAX::error($str ,999 );
		
		exit();
	}

	final public static function handleError($errno, $errstr, $errfile, $errline){

		switch($errno){
			case 8:
				// if(stripos($errstr,'Undefined index')===0)return null;
				// if(stripos($errstr,'Undefined property')===0)return null;
				// if(stripos($errstr,'Undefined offset')===0)return null;
				return null;
				break;
			default:
				break;
		}

		$ex = new self($errstr);

		return self::handle($errno,$errstr,$errfile,$errline,$ex->getTrace(),'ERROR');

	}

	final public static function handleShutdown(){
		$error = error_get_last();
		if($error && $error['type']){

			$ex = new self($error['message']);

			return self::handle($error['type'],$error['message'],$error['file'],$error['line'],$ex->getTrace(),'SHUTDOWN');
		}
	}


	

	final public static function throwEx($m,$line=0){

		$e = new self($m);

		self::handleException($e,$line);

	}

	
}



?>