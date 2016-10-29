<?php
namespace Lib\Core;
use Lib\Core\Exc as E;

class Config{

	private $list = array();
	private $init = false;
	private $set = true;
	function __construct(){
		$this->init_config();
		
	}

	public function __get($key){
		if(!isset($this->list[$key]))E::throw($key.' NO VALUE');

		return $this->list[$key];
	}

	private function init_config(){
		$name = basename( __CLASS__);
		$config = Autoload::conf($name);
		foreach($config as $k=>$v)$this->list[$k] = $v;
		$this->init = true;
		if(!$this->CONFIG_ALLOW_SET)$this->set = false;
		return $this;
	}

	public function __set($key,$value){
		if(!$this->set)
			E::throw('NOT ALLOWED TO SET');
	}
	

	

}



?>