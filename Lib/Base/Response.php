<?php

use Lib\Sharp\SingleInstance;

class Response implements SingleInstance{

    function __construct(){
        
    }



    public static function getInstance(){
        static $object;
		if(empty($object))$object = new self();
		return $object;
    }


    function cookie($name,$value=null,$expire='',$path='/'){

        if($value!==null){
            
            if(!is_int($expire))return strlen($_COOKIE[$name])?$_COOKIE[$name]:$value;

            return setcookie($name,$value,$expire?$expire+time():0,$path,$domain);
        }else{
            
            return $_COOKIE[$name];
        }
    }
    



    

    











}