<?php

namespace App\Resource\Control;

class Func{

   /**
    *
    *  Source : http://www.jquerycn.cn/a_10460
    *
    *  Change : pzcat
    *
    */

    function validate_email($email){

        $exp="#^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$#";

        if(preg_match($exp,$email)){

            return checkdnsrr(array_pop(explode("@",$email)),"MX")?true:false;    
        }else{

            return false;
        }
    }





}