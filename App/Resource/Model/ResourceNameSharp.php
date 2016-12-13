<?php

namespace App\Resource\Model;


class ResourceNameSharp{

    public $rawName;

    public $name;

    public $fName;

    public $f2Name;

    public $f3name;

    public $number;

    public $tag = [];

    public $otherNumber = [];

    public $nameArray = [];

    function __construct($name){

        $this->init($name);

    }

  
    function init($name){

        $name = trim( $name );

        $this->rawName = $name;

        $pattern = ['# ?({|【|「|\[) ?#','# ?(}|】|」|\]) ?#'];

        $name = preg_replace($pattern,['[',']'],$name);




        $pattern = array( 

            '０' , '１' , '２' , '３' , '４' , '５' , '６' , '７' , '８' , '９' , 'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' , 
            'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' , 'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' , 'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' , 
            'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' , 'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' , 'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' , 
            'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' , 'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' , 'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' , 
            'ｙ' , 'ｚ' , '－' , '　' , '：' , '．' , '，' , '／' , '％' , '＃' , '！' , '＠' , '＆' , '（' , '）' ,
            '＜' , '＞' , '＂' , '＇' , '？' , '［' , '］' , '｛' , '｝' , '＼' , '｜' , '＋' , '＝' , '＿' , '＾' , '￥' , '￣' , '｀'
        );
 
        $replace = array(

            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 
            'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 
            'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 
            'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 
            'y', 'z', '-', ' ', ':','.', ',', '/', '%', ' #','!', '@', '&', '(', ')',
            '<', '>', '"', '\'','?','[', ']', '{', '}', '\\','|', '+', '=', '_', '^','￥','~', '`'
        );

        $name = str_replace( $pattern, $replace, $name );

        $this->name = $name;


        $name = preg_replace_callback('#\[(\d{2,3})\]#',function($r){

            $this->number = $r[1];
            $this->otherNumber[] = $r[1];
            return '';
        },$name);

        if($this->otherNumber)array_pop($this->otherNumber);

        
        $array = [

            '720p','360p','1080p','\d{4}x1080','\d{3,4}x\d{3}',

            '(繁|简)(体|體)?','\b(GB|BIG5)\b','( |^)CH(T|S)( |$)','(内|外)(嵌|挂)(版)?','(^| )[^ ]+版',

            'MP4','MKV','IOS','RMVB',

            '(^| )(10|1|4|7|一|四|七|十)月(新番)?( |$)',

            'OVA','OAD','MOVIE',

            'h264','x264','10bit','8bit','ACC','AC3','FLAC','HEVC',
            
            'BD(RIP)?','DVD(RIP)?','网盘'
        ];

        $tag = &$this->tag;

        foreach($array as $a)$name = mb_ereg_replace_callback($a,function($matches) use ( &$tag ){
            $tag[] = trim($matches[0]);
            return ' ';

        },$name,'i');

        $pattern = ['+','_','/','&','★','!','~'];
        $name = str_replace($pattern,' ',$name);
        $name = preg_replace('#\(.*?\)#',' ',$name);
        $name = preg_replace('# ?\[ *?\] ?#',' ',$name);
        $name = preg_replace('# +#',' ',$name);
        $name = trim( $name );

        $name = str_replace('[',']',$name);

        $array = explode(']',$name);

        foreach($array as $k=>$v){

            if(!$v){
                unset($array[$k]);
            }
        }

        
        

        $this->nameArray = $array;



    }




    

    
}