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

    function __construct(&$name){

        $this->init($name);

    }


    function singleByte(&$name){

        $pattern = array( 

            '０' , '１' , '２' , '３' , '４' , '５' , '６' , '７' , '８' , '９' , 'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' , 
            'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' , 'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' , 'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' , 
            'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' , 'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' , 'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' , 
            'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' , 'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' , 'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' , 
            'ｙ' , 'ｚ' , '－' , '　' , '：' , '．' , '，' , '／' , '％' , '＃' , '！' , '＠' , '＆' , '（' , '）' ,
            '＜' , '＞' , '＂' , '＇' , '？' , '［' , '］' , '｛' , '｝' , '＼' , '｜' , '＋' , '＝' , '＿' , '＾' ,
            '￥' , '￣' ,'～', '｀','&amp;','×','・',
            '偵探','團','女僕','復仇','為'
        );
 
        $replace = array(

            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 
            'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 
            'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 
            'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 
            'y', 'z', '-', ' ', ':','.', ',', '/', '%', ' #','!', '@', '&', '(', ')',
            '<', '>', '"', '\'','?','[', ']', '{', '}', '\\','|', '+', '=', '_', '^',
            '￥','~','~', '`','&','x','·',
            '侦探','团','女仆','复仇','为'
        );

        $name = str_replace( $pattern, $replace, $name );

    }

    function getRawNumber(&$name){

        $name = preg_replace_callback('/(第)?(\d{2,3}-\d{2,3})(完|end|集|话)?/i',function($r){
            $this->otherNumber[] = $this->number = $r[2];
            return '';
        },$name);
        
        $name = preg_replace_callback('/#(\d{2,3})/',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('/- ?(\d{2,3})/',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('/第(\d{2,4})(集|话)/',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('#\|(\d{2,3}) ?(end|final)?\|#',function($r){
            $this->otherNumber[] = $this->number = $r[1];
            return '|';
        },$name);
        if($this->otherNumber)array_pop($this->otherNumber);
        
    }

    function getTag(&$name){

        $array = [

            '(tv-)?720p','360p','1080p','480p','\d{4}x1080','\d{3,4}x\d{3}','\b(19|20)\d{2}\b',

            '(繁|简)(体|體|中|日)?','(GB|BIG5)(_.n)?\b','CH(T|S)\b','(内|外)(嵌|挂)(版)?','中日双语(版)?','字幕(文件)?\b','日文(版)?',

            'MP4\b','MKV\b','IOS\b','RMVB\b',

            '(10|1|4|7|一|四|七|十)月(新番|泡面)?\b','剧场版','新番','生肉',

            'OVA','OAD','(the )?MOVIE','HDTV',

            'h264\b','x26\d\b','10-?bit\b','8-?bit\b','HardSub','ACC\b','AAC\b','AC3\b','FLAC\b','HEVC\b','Main10p\b','VFR\b','Web(Rip)?\b',
            
            'BD-?(RIP|BOX)?\b','DVD(RIP)?\b','TV(RIP)?\b','网盘','第.{1,2}(季|部|卷|章)',

            '320K','v\d\b','s\d\b','PSV\b','pc\b'
        ];


        foreach($array as $a)$name = mb_ereg_replace_callback($a,function($matches){
            $this->tag[] = trim($matches[0]);
            return '|';

        },$name,'i');



        
        $name = preg_replace('# +#',' ',$name);
        $name = preg_replace('# *\| *#','|',$name);
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
  
    function init(&$name2){
        $name = $name2;

        //未加工的名字
        $name = trim( $name );
        $this->rawName = $name;


        //优化名字
        $this->singleByte($name);

        $name2 = $name;

        $name = preg_replace('# *({|【|「|\[|}|】|」|\]|\+|&| x |附|/|\\|~|:) *#','|',$name);
        $name = preg_replace(['#\(.*?\)|_|~#'],' ',$name);

        $name = str_replace('★','',$name);
        if(substr_count($name,'.')>2)$name = str_replace('.',' ',$name);
        $name = Hanzi::turn($name, true);

        $name = preg_replace('# +#',' ',$name);
        $name = preg_replace('# *\| *#','|',$name);
        $name = preg_replace('#\|+#','|',$name);
        $name = trim( $name );
        $this->name = $name;

        $this->getTag($name);
        
        $this->getRawNumber($name);

        

        $array = explode('|',$name);

        


        foreach($array as $k=>&$v){

            $this->multibyteUnicodeNameOfResource($v);

            if(!$v)unset($array[$k]);
            
        }

        $this->nameArray = array_merge( $this->nameArray,$array );

        $subtitle = Subtitle::getInstance();

        foreach($this->nameArray as $k=>$p){

            if(!$p)continue;
            //var_dump($p);
            

            if(!$this->number){
                $p = preg_replace_callback('# *(\d+)$#',function($p2){
                    $this->number = $p2[1];return '';
                },$p);
            }
            if(!$p)continue;

            if(preg_match('#(字幕组|sub)$#i',$p)){
                $this->tag[] = $this->nameArray[$k];
                unset($this->nameArray[$k]);
                continue;
            }
            
            
            $p2 = preg_replace('#(\d+|一|二|三|四|五|伍|六|七|八|九|十|I)(期)?$| ?\dnd Season$#','',$p);
            if(!$p2){
                unset($this->nameArray[$k]);
                continue;
            }
            
            $p2 = str_replace([' ','.',';','·','!'],'',$p2);
            if(mb_strlen($p2)<4)for($i=mb_strlen($p2);$i<4;$i++){
                $p2 = '_'.$p2;
            }
            if(preg_match('#^another$#i',$p2))$p2 = '_'.$p2;
                
            $this->nameArray[$k] = $p2;

                    
                
            
        }
        $theme = Theme::getInstance();
        $mat = implode(' ',$this->nameArray);
        if($this->nameArray && $t = $theme->where('MATCH( %F )AGAINST( %n )','matches',$mat)->order('level DESC')->find()){
            $this->theme[$t->id] = $t;
            
            
        }

        if(preg_match('#预告#',$mat)){
            if($this->number)$this->number -= 1;
        }

    }




    

    
}