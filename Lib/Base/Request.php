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
        $this->path = $request;

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

        }elseif($name == 'cookie'){

            return $this->$name = $_COOKIE;

        }
        return null;

    }

    private function muti($name,$way,$filter){

        $name2 = [];

        foreach($name as $k=>$v){

            $g = $this->{$way}($v,$filter);
            if(!is_null($g))$name2[$v] = $g;
        }
        return $name2;
        

    }

    private function filter($content,$filter){

        if($filter == 'string')return (string)$content;
        elseif($filter == 'raw')return $content;

        return null;
    }

    function post($name,$filter = 'string'){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__,$filter);
        }

        return $this->filter($this->post[$name],$filter);
        
    }

    

    function get($name,$filter = 'string'){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__,$filter);
        }

        return $this->filter($this->get[$name],$filter);
        
    }

    function request($name,$filter = 'string'){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__,$filter);
        }

        return $this->filter($this->request[$name],$filter);
        
    }

    function file($name){

        if(is_array($name)){

            return $this->muti($name,__FUNCTION__);
        }

        return $this->file[$name];
        
    }

    function cookie($name,$value=null,$filter = 'string'){

        if($value!==null && !strlen($this->cookie[$name]))return $value;


        return $this->filter($this->cookie[$name],$filter);

        
    }

    











}