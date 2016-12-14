<?php
namespace Lib\Database;
use E;
use Lib\Sharp\SingleInstance;

class Mysqli implements SingleInstance
{
	private $mysqli;
	private $config;
	private $results;
	public $prefix;
	public $database;
	function __construct(){

		$this->init_config();
		$this->init();
		
		$this->init_timeout();
		$this->connect();
		$this->set_charset();


	}
	


	function autocommit($bool = true){

		return $this->mysqli->autocommit($bool);

	}



	function set_charset($charset=null){

		if(is_null($charset))$charset = $this->config->CHARSET;

		if(!$charset)$charset = 'utf8';

		$action = $this->mysqli->set_charset($charset);

		if(!$action)
			E::throwEx('MYSQLI_INIT_COMMAND Failed');

		return $this;

	}


	private function connect(){

		if(!$this->config->DATABASE)E::throwEx('Database Not Selected');

		$auto = $this->config->AUTOCOMMIT;

		$auto = is_null($auto) ? 1 : ( $auto ? 1 : 0);

		$this->init_command('SET AUTOCOMMIT = '.$auto);

		$action = $this->mysqli->real_connect($this->config->HOST,$this->config->USER,$this->config->PASSWORD,$this->config->DATABASE);

		if(!$action){
			$error = '数据库连接失败';
			E::throwEx($error);
		}

		return $this;

	}

	function select_db ($db = null){

		if(is_null($db))$db = $this->config->DATABASE;

		if(!$db)E::throwEx('Database Not Selected');

		$this->mysqli->select_db ($db);

		return $this;
	}


	private function init(){

		$this->mysqli = mysqli_init();

		return $this;
	}

	private function init_command($command){


		$action = $this->mysqli->options(MYSQLI_INIT_COMMAND, $command);

		if(!$action)
			E::throwEx('MYSQLI_INIT_COMMAND Failed');

		return $this;
	}


	private function init_timeout($time = 5){


		$action = $this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, $time);

		if(!$action)
			E::throwEx('MYSQLI_OPT_CONNECT_TIMEOUT Failed');

		return $this;
	}


	private function init_config(){


		$this->config = conf('Mysqli');
		
		return $this;

	}


	
	function commit(){
		return $this->mysqli->commit();
	}
	function rollback(){
		return $this->mysqli->rollback();
	}
	function start(){
		if(method_exists($this->mysqli,'begin_transaction')){
			return $this->mysqli->begin_transaction();
		}
		
		return $this->query("START TRANSACTION");
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
		if(!$this->results)E::throwEx($this->mysqli->error);

		return $this->results;
	}
	function multi_query($sql){
		$this->results = $this->mysqli->multi_query($sql);
		if(!$this->results)E::throwEx($this->mysqli->error);
		return $this->results;
	}
	function insert_id(){
		if(($id = $this->mysqli->insert_id) >= 0){
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

	public static function getInstance(){
        static $object;
		if(empty($object))$object = new self();
		return $object;
    }







}



?>
