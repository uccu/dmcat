<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Uccu\DmcatTool\Tool\LocalConfig as Config;
use Hoa\Exception\Exception as Ex;
use Hoa\Exception\Error as Er;
class E extends Exception{

	
	# 处理异常
	final public static function handleException(){

		self::throwEx('SYSTEM Exception');
	}


	# 处理错误
	final public static function handleError(){

		self::throwEx('SYSTEM ERROR');

	}


	# 输出给前端
	final public static function output($exception,$fileName = NULL){

		$message = $exception->getFormattedMessage();
		$form = $exception->getFrom();
		$raise = $exception->raise();

		# 记录日志
		!$fileName && $fileName = DATE_TODAY;
		$enableLog = Config::get('ERROR_LOG');
		$filePath = LOG_ROOT . $fileName . '.log';

		# 判断文件写入权限
		if(
			$enableLog && 
			is_writable(LOG_ROOT) && 
			(
				!file_exists($filePath) || is_writable($filePath)
			)
		){
			
			$file = fopen($filePath,"a");
			fwrite($file,$raise."\n");
			fclose($file);

		}else{

			$enableLog && $message = 'LogError|' . $message;

		}

		# 输出类型
		$type = Config::get('EXCEPTION_OUTPUT_TYPE');
		switch($type){

			case 'string':
				echo $message;
				break;
			default:
				AJAX::error($message ,999 );
				break;

		}

		exit();

	}

	

	final public static function handleShutdown(){
		$error = error_get_last();
		if($error && $error['type']){

			$ex = new self($error['message']);

			self::throwEx('SYSTEM Shutdown');
		}
	}


	

	final public static function throwEx($message){

		$exception = new Ex($message);
		self::output($exception);

	}

	final public static function throwEr($message){

		$exception = new Er($message);
		self::output($exception);

	}

	
}



?>