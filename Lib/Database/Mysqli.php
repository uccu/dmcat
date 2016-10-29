<?php
namespace Lib\Database;
use Lib\Core\Autoload;
use Lib\Core\Exc as E;

class Mysqli
{
	private $mysqli;
	private $config;
	private $results;
	public $prefix;
	public $database;
	function __construct(){

		$this->init_config();
		$this->init();
	}
	private function mysql(){
		$this->mysqli = mysqli_init();
		if (!$this->mysqli)die('mysqli_init failed');
		if (!$this->mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 1'))die('Setting MYSQLI_INIT_COMMAND failed');
		if (!$this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5))die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
		if (!$this->mysqli->real_connect($this->config['host'],$this->config['user'],$this->config['password']))die('Connect Error ('.mysqli_connect_errno().')'.mysqli_connect_error());
		$this->select_db($this->config['db']);
		$this->set_charset($this->config['charset']);
	}


	private function init(){

		if(!function_exists('mysqli_init'))
			E::throw('Mysqli Not Open');
		
		$this->mysqli = mysqli_init();

		
		return $this;
	}


	private function init_config(){

		$name = basename( __CLASS__);
		$this->config = Autoload::conf($name);
		foreach($this->config as $k=>&$v)
			$this->$k = &$v;
		
		return $this;

	}

	private function _init_config(){
		require PLAY_ROOT.'/source/config/mysql.php';
		$this->prefix = $config['prefix']?$config['prefix']:'';
		$this->config = $config;
		if(file_exists(PLUGIN_ROOT.'/'.PLUGIN_NAME.'/config/mysql.php')){
			require PLUGIN_ROOT.PLUGIN_NAME.'/config/mysql.php';
			$this->prefix = $config['prefix']?$config['prefix']:$this->prefix;
			$this->config = array_merge($this->config,$config);
		}
		$this->prefix = $config['prefix']?$config['prefix']:'';
		$this->config = $config;
	}
	function autocommit($bool = true){
		return $this->mysqli->autocommit($bool);
	}
	function commit(){
		return $this->mysqli->commit();
	}
	function rollback(){
		return $this->mysqli->rollback();
	}
	function select_db ($db){
		$this->database = $db;
		return $this->mysqli->select_db ($db);
	}
	function set_charset($charset='utf8'){
		return $this->mysqli->set_charset($charset);
	}
	function fetch_array($resulttype=MYSQLI_ASSOC){
		if(!$this->results)return false;
		return $this->results->fetch_array($resulttype);
	}
	function fetch_assoc(){
		if(!$this->results)return false;
		return $this->results->fetch_assoc();
	}
	function free_result(){
		if(!$this->results)return false;
		return $this->results->free();
	}
	function query($sql){
		$this->results = $this->mysqli->query($sql);
		if(!$this->results)throw new \Exception($this->mysqli->error);

		return $this->results;
	}
	function multi_query($sql){
		$this->results = $this->mysqli->multi_query($sql);
		if(!$this->results)throw new \Exception($this->mysqli->error);
		return $this->results;
	}
	function insert_id(){
		if(($id = $this->mysqli->insert_id) >= 0){
            //var_dump($id);
            return $id;
        }
		$this->query("SELECT last_insert_id()");
		return $this->result();
	}
	function affected_rows(){
		return $this->mysqli->affected_rows;
	}
	function result($row = 0){
		$r = $this->fetch_array(MYSQLI_BOTH);
		return $r[$row];
	}
	function data_seek($row = 0) {
		if(!$this->results)return false;
		return $this->results->data_seek($row);
	}









}



?>
