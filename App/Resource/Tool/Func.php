<?php

namespace App\Resource\Tool;

use Config;
use App\Resource\Model\VisitModel;

class Func{

    /**
     *
     * 验证是否是合法的邮箱 
     * 做了下修改
     * @source  http://www.jquerycn.cn/a_10460
     * @return boolean 
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


    /**
     *
     * 随机字符串
     * @author pzcat
     * @param number $count 字符串的长度   
     * @param boolean $s 是否只输出小写字母
     * @return string 字符串
     *
     */

    static function randWord($count = 1,$s = false){

        $rand = 'ABCDEFGJIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
        $rand2 = '1234567890abcdefghijklmnopqrstuvwxyz';

        $o = '';
        for($i=0;$i<$count;$i++){
            $o .= $s?$rand2[rand(0,35)]:$rand[rand(0,61)];
        }
        return $o;

    }

    /**
     *
     * AES转码/解码
     * @author pzcat
     * @param string $str 需要转码/解码的字符串
     * @param string $key 需要的秘钥
     * @return string 转码/解码后的字符串
     *
     */

     /*AES转码*/
    static public function aes_encode( $str , $key = null){
        if(!$key)$key = Config::get('AES_SECRECT_KEY');
        $key = md5($key);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB),MCRYPT_RAND);  
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB, $iv));  
    }  
    /*AES解码*/
    static public function aes_decode( $str , $key = null){
        if(!$key)$key = Config::get('AES_SECRECT_KEY');
        $key = md5($key);
        $str = base64_decode($str);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB),MCRYPT_RAND);  
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB, $iv);  
    }  


    /**
     *
     * 人性化时间转换
     * @author pzcat
     * @param number $time 10位数字的时间戳
     * 
     * @return string 
     *      今日
     *      当小于1m时      xx秒前
     *      当小于1h时      xx分xx秒前
     *      当小于1d时      xx时xx分前
     *      昨日           昨日xx时xx分
     *      其他           xxxx-xx-xx
     *
     */

    static public function time_calculate( $time ){

        if($time>=TIME_TODAY){

            $time = TIME_NOW - $time;

            $second = $time % 60 ;
            $time = ( $time - $second ) / 60 ;
            if(!$time)return $second.'秒前';

            $minute = $time % 60 ;
            $hour = ( $time - $minute ) / 60 ;
            if(!$hour)return $minute.'分'.($second?($second<10?'0':'').$second.'秒':'').'前';

            return $hour.'时'.($minute?($minute<10?'0':'').$minute.'分':'').'前';
        
        }elseif($time>=TIME_YESTERDAY){

            return '昨日'.date('H时i分',$time);

        }else{

            return date('Y-m-d',$time);
            
        }



    }  

    static public function visit_log(){

        $ip = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        $ip = ($ip) ? $ip : $_SERVER["REMOTE_ADDR"];
        
        $data['ip'] = $ip;
        $data['date'] = date('Y-m-d');
        $data['time'] = date('H:i:s');
        $data['referer'] = $_SERVER['HTTP_REFERER'];
        $data['url'] = REQUEST_PATH;

        $add = VisitModel::getInstance()->set($data)->add()->getStatus();
        return $add;

    }
}