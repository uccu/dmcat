<?php

use Lib\Core\Autoload;

class Config {

	private $list = array();
	private $init = false;
	private $set = true;
	private $null = true;
	function __construct(){
		$this->init_config();
		
	}

	public static function obj(){
        static $object;
		if(empty($object)) $object = new self();
		
		return $object;

    }

	public static function get($v){
		
		return self::obj()->$v;

	}

	public function __get($key){
		if(!$this->null)
			if(!isset($this->list[$key]))E::throwEx($key.' NO VALUE',1);

		return $this->list[$key];
	}

	private function init_config(){
		$name = basename( __CLASS__);
		$config = Autoload::conf($name);
		foreach($config as $k=>$v)$this->list[$k] = $v;
		$this->init = true;
		if(!$this->CONFIG_ALLOW_SET)$this->set = false;
		if(!$this->CONFIG_ALLOW_NULL)$this->null = false;
		return $this;
	}

	public function __set($key,$value){
		if(!$this->set)
			E::throwEx('NOT ALLOWED TO SET',1);
	}
	

	

}



?>