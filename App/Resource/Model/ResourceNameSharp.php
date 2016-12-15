<?php

namespace App\Resource\Model;

use fengqi\Hanzi\Hanzi;
use App\Resource\Model\SubtitleModel as Subtitle;
use App\Resource\Model\ThemeModel as Theme;

class ResourceNameSharp{

    public $rawName;

    public $name;

    public $number = 0;

    public $tag = [];

    public $subtitle;

    public $otherNumber = [];

    public $nameArray = [];

    public $theme = [];

    function __construct($name){

        $this->init($name);

    }


    function singleByte(&$name){

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

    }

    function getRawNumber(&$name){
        
        $name = preg_replace_callback('/#(\d{2,3})/',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('/- ?(\d{2,3})/',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('/第(\d{2,3})(集|话)/',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('#\[(\d{2,3})\]#',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '';
        },$name);
        if($this->otherNumber)array_pop($this->otherNumber);
        
    }

    function getTag(&$name){

        $array = [

            '720p','360p','1080p','\d{4}x1080','\d{3,4}x\d{3}','\b(19|20)\d{2}\b',

            '(繁|简)(体|體)?','(GB|BIG5)\b','CH(T|S)\b','(内|外)(嵌|挂)(版)?',

            'MP4\b','MKV\b','IOS\b','RMVB\b',

            '(10|1|4|7|一|四|七|十)月(新番)?',

            'OVA','OAD','MOVIE','HDTV',

            'h264\b','x26\d\b','10-?bit\b','8-?bit\b','ACC\b','AC3\b','FLAC\b','HEVC\b','Main10p\b','VFR\b',
            
            'BD(RIP)?\b','DVD(RIP)?\b','网盘','第\d+(季|部|卷|章)',

            '320K',
        ];


        foreach($array as $a)$name = mb_ereg_replace_callback($a,function($matches){
            $this->tag[] = trim($matches[0]);
            return '|';

        },$name,'i');

        $pattern = ['_'];
        $name = str_replace($pattern,' ',$name);
        $pattern = ['+','&',' x ',' × '];
        $name = str_replace($pattern,'|',$name);
        $pattern = ['★'];
        $name = str_replace($pattern,'',$name);
        $name = preg_replace('# *\[ *?\] *|\(.*?\)#',' ',$name);
        $name = preg_replace('# +#',' ',$name);
        $name = preg_replace('#\|+#','|',$name);
        $name = trim( $name );

    }

    function multibyteUnicodeNameOfResource(&$name){

        $name = trim($name);

        if(!$name)return;

        $pieces = explode(' ',$name);


        foreach($pieces as $k=>$p){
            
            if( preg_match('#[^0-9a-z!-]#i',$p)){

                if(strlen($p)>1)$this->nameArray[] = trim($p);

                unset($pieces[$k]);

            }elseif(strlen($p)<2)unset($pieces[$k]);

        }

        $name = implode(' ',$pieces);

        $name = preg_replace('# +#',' ',$name);

        $name = trim( $name );

        
    }
  
    function init($name){


        //未加工的名字
        $name = trim( $name );
        $this->rawName = $name;


        //优化名字
        $this->singleByte($name);
        $pattern = ['# *({|【|「|\[) *#','# *(}|】|」|\]) *#'];
        $name = preg_replace($pattern,'|',$name);
        $name = str_replace('_',' ',$name);
        if(substr_count($name,'.')>2)$name = str_replace('.',' ',$name);
        $name = Hanzi::turn($name, true);
        $this->name = $name;


        $this->getRawNumber($name);

        $this->getTag($name);

        $name = str_replace(['/','\\','~',':'],'|',$name);

        $array = explode('|',$name);

        


        foreach($array as $k=>&$v){

            $this->multibyteUnicodeNameOfResource($v);

            if(!$v)unset($array[$k]);
            
        }

        $this->nameArray = array_merge( $this->nameArray,$array );

        $subtitle = Subtitle::getInstance();

        foreach($this->nameArray as $k=>$p){

            if(!$this->number && is_numeric($p) && strlen($p)<5){
                $this->number = $p;
                unset($this->nameArray[$k]);
            }else{
                // if(!$this->subtitle){
                //     $subtitle = Subtitle::getInstance();
                //     if($subtitle->where('MATCH( %F )AGAINST( %n IN BOOLEAN MODE)','matches',$p)->find()){
                //         $this->subtitle = $t;
                //         unset($this->nameArray[$k]);
                //     }
                    
                // }
                if(1){
                    $theme = Theme::getInstance();
                    $p2 = str_replace(' ','',$p);
                    if(mb_strlen($p2)<4)for($i=mb_strlen($p2);$i<4;$i++){
                        $p2 = '_'.$p2;
                    }
                    if(0===strnatcasecmp($p2,'another'))$p2 = '_'.$p2;
                    if($t = $theme->where('MATCH( %F )AGAINST( %n IN BOOLEAN MODE)','matches',$p2)->order('level DESC')->find()){
                        $this->theme[$t->id][] = $t;
                        unset($this->nameArray[$k]);
                    }
                    //echo $theme->sql;
                    
                }
            }
        }

        

    }




    

    
}