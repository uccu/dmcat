<?php

use Lib\Sharp\SingleInstance;

class Request implements SingleInstance{

    function __construct(){
        $this->flesh_path();
    }

    public $path;

    public static function getInstance(){
        static $object;
		if(empty($object))$object = new self();
		return $object;
    }



    public function flesh_path($request = REQUEST_PATH){

        $this->folder = $request ? explode('/',$request) : array();
        $this->path = REQUEST_PATH;

    }



    public function __get($name){

        if($name == 'get'){
            
            return $this->$name = $_GET;

        }elseif($name == 'post'){

            return $this->$name = $_POST;

        }elseif($name == 'request'){

            return $this->$name = $_REQUEST;
            
        }elseif($name == 'file'){

            return $this->$name = $_FILE;

        }
        return null;

    }

    











}