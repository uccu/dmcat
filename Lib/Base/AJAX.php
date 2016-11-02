<?php


class AJAX{

    static $type = 'JSON';


    private static function outPut($code ,$data ,$message ,$url){

        $content = (object)array();

        $content->data = (object)$data;

        $content->code = (int)$code;

        $content->url = (string)$url;

        $content->message = (string)$message;

        $content = json_encode($content);

        echo $content;

        if(Config::get('OB_GZHANDLER')){
            ob_end_flush();
        }

        exit();

    }


    static function success(array $data ,$code = 200 ,$url = ''){

        $url = !$url && is_string($code) ? $code : $url;

        $code = is_int($code) ? $code : 200;

        if(!$data)$data = array();

        $message = '';

        self::outPut($code ,$data ,$message ,$url);
        
    }


    static function error(string $message ,$code = 400 ,$url = ''){

        $url = !$url && is_string($code) ? $code : $url;

        $code = is_int($code) ? $code : 400;

        if(!$message)$message = 'Undefined Error';

        $data = array();

        self::outPut($code ,$data ,$message ,$url);
 
    }


    static function  code(){




        
    }





}