<?php

use Uccu\DmcatTool\Traits\InstanceTrait;

class Response{

    use InstanceTrait;

    function __construct(){
        
    }


    function cookie($name,$value=null,$expire='',$path='/'){


        if(!is_int($expire))return strlen($_COOKIE[$name])?$_COOKIE[$name]:$value;

        return setcookie($name,$value,$expire?$expire+time():0,$path,$domain);

    }
    
    function r302($path = '/'){

        header('Location:'.$path);
        exit();

    }



    

    











}