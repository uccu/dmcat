<?php

namespace App\Resource\Tool;
use Lib\Sharp\SingleInstance;
use Config;

class Format implements SingleInstance{

    static function getInstance(){
        static $object;
		if(empty($object)) $object = new self();
		return $object;
    }



    /**
    *
    *   @初始化
    *
    *
    */

    function init(&$name){

        $pattern = [

            '【','】','「','」','+','_','/','&','★','[',']','!','！','~',
        ];

        $name = str_ireplace($pattern,' ',$name);

        $name = preg_replace('#\(.*?\)#',' ',$name);

        $array = [

            '#\[.*?\]#i'
        ];



    }

    /**
    *
    *   @TAG
    *
    *
    */

    function tagOfMotionPicture(&$name){

        $array = [

            '720p','360p','1080p','\d{4}x1080','\d{3,4}x\d{3}',

            '(繁|简)(体|體)?','( |^)(GB|BIG5)( |$)','( |^)CH(T|S)( |$)','(内|外)(嵌|挂)(版)?','(^| )[^ ]+版',

            'MP4','MKV','IOS','RMVB',

            '(^| )(10|1|4|7|一|四|七|十)月(新番)?( |$)',

            'OVA','OAD','MOVIE',

            'h264','x264','10bit','8bit','ACC','AC3','FLAC',
            
            'BD(RIP)?','DVD(RIP)?','网盘'
        ];

        $tag = [];

        foreach($array as $a)$name = mb_ereg_replace_callback($a,function($matches) use ( &$tag ){
            $tag[] = trim($matches[0]);
            return ' ';

        },$name,'i');
 
        $name = preg_replace('# +#',' ',$name);
        
        $name = trim( $name );

        return $tag;

        
    }



    function multibyteUnicodeNameOfResource(&$name){

        $pieces = explode(' ',$name);

        $tag = [];

        foreach($pieces as $p){

            if( preg_match('#[^0-9a-z!-]#i',$p)){

                if(!in_array($p,['-']))$tag[] = trim($p);

                $name = str_ireplace($p,'',$name);

            }

        }

        $name = preg_replace('# +#',' ',$name);

        $name = trim( $name );

        return $tag;
        
    }


    function numberOfResource(&$name){

        preg_match_all('#(^| )([0-9]|-)+( |$)#',$name,$matches,PREG_SET_ORDER);

        if($matches){

            $number = end($matches);

            $name = str_ireplace($number[0],'',$name);

            $name = preg_replace('# +#',' ',$name);

            $name = trim( $name );

            return trim($number[0]);
        }


        return null;
        
    }







}