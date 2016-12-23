<?php

namespace App\Resource\Tool;

use Config;

class Func{

   /**
    *
    *  Source : http://www.jquerycn.cn/a_10460
    *
    *  Change : pzcat
    *
    */

    static function validate_email($email){

        $exp="#^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$#";

        if(preg_match($exp,$email)){

            return checkdnsrr(array_pop(explode("@",$email)),"MX")?true:false;    
        }else{

            return false;
        }
    }

    static function randWord($count = 1,$s = false){

        $rand = 'ABCDEFGJIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
        $rand2 = '1234567890abcdefghijklmnopqrstuvwxyz';

        $o = '';
        for($i=0;$i<$count;$i++){
            $o .= $s?$rand2[rand(0,35)]:$rand[rand(0,61)];
        }
        return $o;

    }

    const CIPHER = MCRYPT_RIJNDAEL_128;
    const MODE = MCRYPT_MODE_ECB;  

    static public function aes_encode( $str , $key = null){
        if(!$key)$key = Config::get('AES_SECRECT_KEY');
        $key = md5($key);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER,self::MODE),MCRYPT_RAND);  
        return base64_encode(mcrypt_encrypt(self::CIPHER, $key, $str, self::MODE, $iv));  
    }  

    static public function aes_decode( $str , $key = null){
        if(!$key)$key = Config::get('AES_SECRECT_KEY');
        $key = md5($key);
        $str = base64_decode($str);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER,self::MODE),MCRYPT_RAND);  
        return mcrypt_decrypt(self::CIPHER, $key, $str, self::MODE, $iv);  
    }  



    static public function time_calculate( $time ){

        if($time>TIME_TODAY){

            $second = $time % 60 ;

            $time_r = ( $time - $second ) / 60 ;
            $minute = $time_r % 60 ;
            if(!$time_r)return $second.'秒前';

            $time_r = ( $time - $minute ) / 60 ;
            $hour = $time_r % 24 ;
            if(!$time_r)return $minute.'分'.($second?($second<10?'0':'').$second.'秒':'').'前';
            else return $hour.'时'.($minute?($minute<10?'0':'').$minute.'分':'').'前';
        
        }elseif($time>TIME_YESTERDAY){

            return '昨日'.date('H时i分',$time);

        }else{

            return date('Y-m-d');
            
        }



    }  

}