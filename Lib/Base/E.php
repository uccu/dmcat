<?php


class E extends Exception{

	

	final public static function handleException($e,$line=0){
		
		return self::handle($e->getCode(),$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTrace(),'BASE',$line);
		
	}

	final public static function handle($code,$message,$file,$line,$trace,$base = 'BASE',$c = null){

		

		if(!is_null($c)){
			$file = $trace[$c]['file'];
			$line = $trace[$c]['line'];
		}

		$file = str_ireplace(array(BASE_ROOT,'.php'),'',$file);
		$file = str_ireplace('/','\\',$file);

		$str =  "$base EXCEPTION : [$code][$message] FILE [$file] LINE [$line]";
		

		echo $str;
		
		
		die();
	}

	final public static function handleError($errno, $errstr, $errfile, $errline){

		switch($errno){
			case 8:
				if(stripos($errstr,'Undefined index')===0)return null;
				if(stripos($errstr,'Undefined property')===0)return null;
				if(stripos($errstr,'Undefined offset')===0)return null;

				break;
			default:
				break;
		}

		$ex = new self($errstr);

		return self::handle($errno,$errstr,$errfile,$errline,$ex->getTrace(),'ERROR');

	}

	final public static function handleShutdown(){
		if($error = error_get_last() && $error['type']){

			$ex = new self($errstr);

			return self::handle($error['type'],$error['message'],$error['file'],$error['line'],$ex->getTrace(),'SHUTDOWN');
		}
	}


	final public static function new($m){

		return new self($m);

	}

	final public static function throw($m,$line=0){

		$e = new self($m);

		self::handleException($e,$line);

	}

	
}



?>