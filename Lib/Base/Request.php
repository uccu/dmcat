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

    private function muti($name,$way){

        $name2 = [];

        foreach($name as $k=>$v){

            $g = $this->{$way}($v);
            if(!is_null($g))$name2[$v] = $g;
        }
        return $name2;
        

    }

    function post($name){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__);
        }

        return $_POST[$name];
        
    }

    

    function get($name){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__);
        }

        return $_GET[$name];
        
    }

    function request($name){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__);
        }

        return $_REQUEST[$name];
        
    }

    function file($name){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__);
        }

        return $_FILES[$name];
        
    }

    function cookie($name,$value=null){

        if($value!==null){
            
            return strlen($_COOKIE[$name])?$_COOKIE[$name]:$value;

        }else{

            return $_COOKIE[$name];

        }
        
    }

    











}