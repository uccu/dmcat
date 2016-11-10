<?php


class Request{

    function __construct(){
        if(!REQUEST_PATH)$this->folder = array();
        else $this->folder = explode('/',REQUEST_PATH);
        self::$path = REQUEST_PATH;
    }

    public static $path;

    public static function obj(){
        static $object;
		if(empty($object)) {
			$object = new self();
		}
		return $object;

    }

    public static function folder(){

        return self::obj()->folder;

    }


    public static function flesh_path($request = REQUEST_PATH){

        if(!$request) self::obj()->folder = array();
        else  self::obj()->folder = explode('/',$request);
        self::$path = REQUEST_PATH;

    }

    public static function get(){

        return self::obj()->get;
    }

    public function __get($name){

        if($name == 'get'){
            $this->get = $_GET;
            return $this->get;
        }
        return null;

    }

    











}