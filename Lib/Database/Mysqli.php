<?php
namespace Lib\Database;
use E;

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
		
		$this->init_timeout();
		$this->connect();
		$this->set_charset();


	}
	


	function autocommit(bool $bool = true){

		return $this->mysqli->autocommit($bool);

	}



	function set_charset($charset=null){

		if(is_null($charset))$charset = $this->config->CHARSET;

		if(!$charset)$charset = 'utf8';

		$action = $this->mysqli->set_charset($charset);

		if(!$action)
			E::throw('MYSQLI_INIT_COMMAND Failed');

		return $this;

	}


	private function connect(){

		if(!$this->config->DATABASE)E::throw('Database Not Selected');

		$action = $this->mysqli->real_connect($this->config->HOST,$this->config->USER,$this->config->PASSWORD,$this->config->DATABASE);

		if(!$action){
			$error = '数据库连接失败';
			E::throw($error);
		}
		
		$auto = $this->config->AUTOCOMMIT;

		$auto = is_null($auto) ? 1 : ( $auto ? 1 : 0);

		$this->init_command('SET AUTOCOMMIT = '.$auto);

		return $this;

	}

	function select_db ($db = null){

		if(is_null($db))$db = $this->config->DATABASE;

		if(!$db)E::throw('Database Not Selected');

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
			E::throw('MYSQLI_INIT_COMMAND Failed');

		return $this;
	}


	private function init_timeout($time = 5){


		$action = $this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, $time);

		if(!$action)
			E::throw('MYSQLI_OPT_CONNECT_TIMEOUT Failed');

		return $this;
	}


	private function init_config(){

		$name = basename( __CLASS__);
		$this->config = conf($name);
		
		return $this;

	}


	
	function commit(){
		return $this->mysqli->commit();
	}
	function rollback(){
		return $this->mysqli->rollback();
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
		if(!$this->results)E::throw($this->mysqli->error);

		return $this->results;
	}
	function multi_query($sql){
		$this->results = $this->mysqli->multi_query($sql);
		if(!$this->results)E::throw($this->mysqli->error);
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









}



?>
