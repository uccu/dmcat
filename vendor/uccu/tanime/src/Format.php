<?php
namespace uccu\Tanime;

use fengqi\Hanzi\Hanzi;

class Format{

    


    static function changeToSimplified( $name ){

        return $name = Hanzi::turn($name, true);

    }






    static function changeToSimplifiedPlus( $name ){

        $pattern = array( 

            '０' , '１' , '２' , '３' , '４' , '５' , '６' , '７' , '８' , '９' , 'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' , 
            'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' , 'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' , 'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' , 
            'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' , 'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' , 'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' , 
            'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' , 'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' , 'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' , 
            'ｙ' , 'ｚ' , '－' , '　' , '：' , '．' , '，' , '／' , '％' , '＃' , '！' , '＠' , '＆' , '（' , '）' ,
            '＜' , '＞' , '＂' , '＇' , '？' , '［' , '］' , '｛' , '｝' , '＼' , '｜' , '＋' , '＝' , '＿' , '＾' ,
            '￥' , '￣' ,'～', '｀','&amp;','×','・','_','★','❤','《','》',
            '偵探','團','女僕','復仇','為','什麼','重啟','时鐘','慾'
        );
 
        $replace = array(

            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 
            'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 
            'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 
            'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 
            'y', 'z', '-', ' ', ':','.', ',', '/', '%', ' #','!', '@', '&', '(', ')',
            '<', '>', '"', '\'','?','[', ']', '{', '}', '\\','|', '+', '=', '_', '^',
            '￥','~','~', '`','&','x','·',' ',' ','','[',']',
            '侦探','团','女仆','复仇','为','什么','重启','时钟','欲'
        );

        return $name = str_replace( $pattern, $replace, $name );
    }


    
    static function changeToSplit( $name ){

        $name = preg_replace('# *({|【|「|\[|}|】|」|\]|\+|&| x |附|/|\\|~|\(|\)|:|~) *#','|',$name);
        
        self::trim( $name );

        return $name;
    }
    

    static function trim( &$name ){

        $name = trim( $name );

        $name = preg_replace_callback('#[\| \x{200b}\x{200e}]+#u',function($tea){
            return strpos($tea[0], '|') === false ? ' ' : '|';
        },$name);
        $name = preg_replace('#^\||\|$|#','',$name);

    }


    static function completeToTag( &$name ){

        $array = [

            '(tv-)?720p','360p','1080p','480p','\d{4}x1080','\d{3,4}x\d{3}','\b(19|20)\d{2}\b',

            '\b(繁|简|繁简|简繁)(体|體|中|日)?\b','(GB|BIG5)(_.n)?\b','CH(T|S)\b','(内|外)(嵌|挂)(版)?','中日双语(\w+)?','字幕(文件)?\b','日文(版)?','\bRAW\b',

            '\.?MP4\b','\.?MKV\b','\.?ISO\b','\.?RMVB\b','\bsc\b','\btc\b','\bAVC\b',

            '(10|1|4|7|一|四|七|十)月(新番|泡面)?\b','剧场版','新番','生肉','合集\b','外传','预告',

            'OVA','OAD','(the )?MOVIE','\w+TV\b','MBS\b','\bcn\b','\bjap\b',

            'h264\b','x26\d\b','10-?bit\b','8-?bit\b','HardSub','ACC\b','AAC\b','AC3\b','FLAC\b','HEVC\b','Main10p\b','VFR\b','Web(Rip)?\b',
            
            'BD-?(RIP|BOX)?\b','DVD(RIP)?\b','TV(RIP)?\b','第.{1,2}(季|部|卷|章)',

            '320K','v\d\b','\dnd\b','s\d\b','PSV\b','pc\b','\bsp\b',
        ];

        $tag = [];

        foreach($array as $a)$name = mb_ereg_replace_callback($a,function($matches) use ( &$tag ){
            $tag[] = trim($matches[0],". \t\n\r\0\x0B");
            return '|';

        },$name,'i');

        return $tag;

    }

    static function deleteTag( &$name ){

        $array = [

            '\b(\w+)?招募(\w+)?\b','(\w+)?网盘'
        ];

        $tag = [];

        foreach($array as $a)$name = mb_ereg_replace_callback($a,function($matches) use ( &$tag ){

            $tag[] = trim($matches[0]);
            return '|';

        },$name,'i');

        return $tag;

    }


    static function toExTags( $name ){

        $arr = explode('|', $name );

        $frm = [];

        foreach($arr as $piece){

            $pieces = explode(' ',$piece);
            foreach($pieces as $k=>$p){

                if( preg_match('#[^0-9a-z!-;]#i',$p) ){

                    if( preg_match('/[a-z0-9]+$/i',$p,$mmm) ){

                        $start = strpos($piece,$p) + strlen($p);

                        if(strpos($piece,$mmm[0],$start) === false){
                            $frm[] = str_replace($mmm[0],'',$p);
                            $pieces[$k] = $mmm[0];
                            
                        }else{
                            $frm[] = $p;
                            unset($pieces[$k]);
                        }

                    }else{
                        $frm[] = $p;
                        unset($pieces[$k]);
                    }
                }
            }
            if($pieces)$frm[] = implode(' ',$pieces);

        }
        return $frm;

    }

    static function getRawNumber(&$name){

        $num = null;

        $name = preg_replace_callback('/(第)?(\d{2,3}-\d{2,3})(完|end|集|话)?/i',function($r) use (&$num){
            $num = $r[2];
            return '';
        },$name);
        
        $name = preg_replace_callback('/# ?(\d{2,3})/',function($r) use (&$num){
            $num = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('/- ?(\d{2,3})/',function($r) use (&$num){
            $num = $r[1];
            return '';
        },$name);

        $name = preg_replace_callback('/(第)?(\d{2,4})(集|话)(全)?/',function($r) use (&$num){
            $num = $r[2];
            return '';
        },$name);

        $name = preg_replace_callback('#(\||\[|【)(\d{2,3}) ?(end|final)?#i',function($r) use (&$num){
            $num = $r[2];
            return '|';
        },$name);
        
        return $num;
        
    }

    static function getNumberPlus( &$arr ){

        $num = null;

        foreach($arr as $k=>&$v){
            if(strlen($v) == 1 && is_numeric($v))unset($arr[$k]);
            elseif(preg_match('/( |^)(\d{2,3})$/',$v,$h)){
                $num = $h[2];
                 $v = str_replace($h[0],'',$v);
            }
            if(!$v)unset($arr[$k]);
        }

        return $num;


    }




}