<?php
namespace App\Lawyer\Tool;
use Config;
use AJAX;
use App\Lawyer\Middleware\L;
use App\Lawyer\Model\ConfigModel;
use App\Lawyer\Model\MessageModel;
use App\Lawyer\Model\CaptchaModel;
use App\Lawyer\Model\UploadModel;


class Func {
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
    static function randWord($count = 1,$s = 0){
        $rand = 'ABCDEFGJIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
        $rand2 = '1234567890abcdefghijklmnopqrstuvwxyz';
        $rand3 = '1234567890';
        $o = '';
        for($i=0;$i<$count;$i++){
            if(!$s)$o .= $rand[rand(0,61)];
            elseif($s==2)$o .= $rand2[rand(0,35)];
            elseif($s==3)$o .= $rand3[rand(0,9)];
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
        if(!$key)$key = L::getSingleInstance()->config->AES_SECRECT_KEY;
        $key = md5($key);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB),MCRYPT_RAND);  
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB, $iv));  
    }  
    /*AES解码*/
    static public function aes_decode( $str , $key = null){
        if(!$key)$key = L::getSingleInstance()->config->AES_SECRECT_KEY;
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

    /**
     *
     * 人性化时间转换
     * @author pzcat
     * @param number $time 10位数字的时间戳
     * 
     * @return string 
     */
    static public function time_zcalculate( $time ){
        if($time == 0 )return '秒回';
        if($time < 60 )return $time.'秒';
        if($time < 3600 )return (floor(($time-1)/60) + 1).'分钟';
        if($time < 3600 * 24 )return (floor(($time-1)/3600) + 1).'小时';
        return (floor(($time-1)/3600/24) + 1).'天';
        
    }  
    
    /* 密码安全性验证 */
    static function check_password($password){
        !$password && AJAX::error('密码为空');
        preg_match('#^\d+$#',$password) && AJAX::error('密码需要英文字母+数字组成');
        preg_match('#^[a-z]+$#i',$password) && AJAX::error('密码需要英文字母+数字组成');
        return true;
    }
    
    static function upload($name = ''){
        if(!$_FILES)return [];
        
        $paths = [];
        $upn = $upa = [];
        if(!is_dir(BASE_ROOT.'upload'))
                !mkdir(BASE_ROOT.'upload',0777,true) && AJAX::error('文件夹权限不足，无法创建文件！');
        $model = UploadModel::copyMutiInstance();
        
        foreach($_FILES as $k=>$file){
            $upn[] = $k;
            if(!$name || $k==$name){
                $upa[] = $k;
                $paths[$k] = date('Ymd_His',TIME_NOW).'_'.$k.'.cache';
                $data = [];
                $data['path'] = $paths[$k];
                $data['name'] = $file['name'];
                $data['type'] = $file['type'];
                $id = $model->set($data)->add()->getStatus();
                move_uploaded_file($file['tmp_name'],BASE_ROOT."upload/".$paths[$k]);
                $paths[$k] = $id;
            }
        }
        $data['upn'] = implode(',',$upn);
        $data['upa'] = implode(',',$upa);
        $data['succ'] = ($name?$paths[$name]:$paths) ? 1 : 0;
        $data['ctime'] = TIME_NOW;
        
        return $name?$paths[$name]:$paths;
    }
    /* 上传图片 */
    static function uploadFiles($name = null,$width = 0,$height = 0,$cut = 0){
        if(!$_FILES)return [];
        
        $paths = [];
        $upn = $upa = [];
        foreach($_FILES as $k=>$file){
            $upn[] = $k;
            if(!$name || $k==$name){
                $upa[] = $k;
                $paths[$k] = self::uploadPic($file['tmp_name'],0,$width,$height,$cut);
            }
            
        }
        $data['upn'] = implode(',',$upn);
        $data['upa'] = implode(',',$upa);
        $data['succ'] = ($name?$paths[$name]:$paths) ? 1 : 0;
        $data['ctime'] = TIME_NOW;
        
        // LogUploadModel::copyMutiInstance()->set($data)->add();
        return $name?$paths[$name]:$paths;
    }
    /* 处理上传图片 */
    static function uploadPic($tmp_name,$type = 0,$width = 0,$height = 0,$cut = 0){
        if(!$tmp_name)AJAX::error('上传失败,无法获取缓存路径');
        $arr = getimagesize($tmp_name);
        /* 判断图片格式 */
        switch($arr[2]){
            case 3:
                $img = imagecreatefrompng($tmp_name);
                imagesavealpha($img,true);
                break;
            case 2:
                $img = imagecreatefromjpeg($tmp_name);
                break;
             case 1:
                $img = imagecreatefromgif($tmp_name);
                break;
            default:
                AJAX::error('解析图片失败');  //非jpg/png/gif 强制退出程序
                break;
        }
        $picRoot = PUBLIC_ROOT.'pic';
        if($type === 'md5'){
            $md5 = md5_file($tmp_name);
            $folder = substr($md5,0,2);
            $folderRoot = $picRoot.'/'.$folder;
            if(!is_dir($folderRoot))
                !mkdir($folderRoot,0777,true) && AJAX::error('文件夹权限不足，无法创建文件！');
            $folderRoot .= '/';
            $src = $folderRoot.$md5.'.jpg';
            $path = $folder.'/'.$md5.'.jpg';
        }else{
            $folder = DATE_TODAY;
            $folderRoot = $picRoot.'/'.$folder;
            if(!is_dir($folderRoot))
                !mkdir($folderRoot,0777,true) && AJAX::error('文件夹权限不足，无法创建文件！');
            $folderRoot .= '/';
            $time = date('H.i.s.').self::randWord(6,3);
            $src = $folderRoot.$time.'.jpg';
            $path = $folder.'/'.$time.'.jpg';
        }
        $width0 = $arr[0];
        $height0 = $arr[1];
        $width = $width?$width:$width0;
        // var_dump($height);die();
        $height = $height?$height:$height0;
        
        $image = imagecreatetruecolor($width, $height);
        $color = imagecolorallocatealpha($image, 255, 255, 255,127);
        imagefill($image, 0, 0, $color);
        if($cut == 0)
            imagecopyresampled($image, $img,0,0,0,0,$width,$height,$width0,$height0);
        
        imagejpeg($image,$src,75);
        imagedestroy($image);
        return $path;
    }
    /* 数字前增加0 */
    static function add_zero($number,$total){
        $string = strval($number);
        $len = strlen($string);
        for($i = $len;$i<$total;$i++){
            $string = '0'.$string;
        }
        return $string;
    }
   
    /* 判断是否通过ssl访问 */
    static function is_SSL(){  
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)  
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0; 
    }  
    /* 返回全图片网址 */
    static function fullPicAddr($path){
        return self::fullAddr('pic/'.$path);
    }
    /* 返回全网址 */
    static function fullAddr($path){
        return (self::is_SSL()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.$path;
    }
    /* 模拟curl */
    static function curl($url,$data = []){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if($data){
            curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        $z = curl_exec($ch);
        return $json = json_decode($z);
    }

    
    static function add_message($user_id,$message,$url = ''){
        $model = MessageModel::copyMutiInstance();
        if(!$user_id || !$message)return;
        if(!is_array($user_id))$user_id = [$user_id];
        $data['content'] = $message;
        $data['create_time'] = TIME_NOW;
        $data['url'] = $url;
        foreach($user_id as $id){
            
            $data['user_id'] = $id;
            $model->set($data)->add();
        }
    }
    

    /** 验证手机号是否合法
     * check_phone
     * @param mixed $phone 
     * @return mixed 
     */
    static public function check_phone($phone){

        if(!$phone)AJAX::error('手机号不能为空！');

        // if(!preg_match('#1\d{10}#',$phone)){
        //     AJAX::error('手机号格式错误');
        // }

        return true;

    }

    /** 发送验证码
     * msm
     * @param mixed $phone 
     * @return mixed 
     */
    static public function msm($phone){

        if(!$phone)AJAX::error('手机号不能为空！');

        // $rand = self::randWord(4,3);
        $rand = '1234';

        $data['captcha'] = $rand;
        $data['time'] = TIME_NOW + 300;
        $data['create_time'] = TIME_NOW;
        $data['phone'] = $phone;

        CaptchaModel::copyMutiInstance()->set($data)->add();
        CaptchaModel::copyMutiInstance()->where(
            [
                'time'=>['time < %n',TIME_NOW]
            ]
        )->remove();

        return true;

    }


    /** 验证验证码
     *
     * 手机验证码的验证
     * @author pzcat
     * @param string $word 验证码
     * @param int $user_id 如果用户登陆，则传入用户ID
     * 
     * @return boolean
     *
     */
    static public function check_phone_captcha( $phone,$word){

        $captcha = CaptchaModel::copyMutiInstance()->where(['phone'=>$phone])->order('create_time desc')->find();
        
        !$captcha && AJAX::error('请发发送验证码！');

        $captcha->time < TIME_NOW && AJAX::error('验证码已过期，请重新发送验证码！');
        $captcha->captcha != $word && AJAX::error('验证码错误，请输入正确的验证码！');

        return true;
        
    }



    /** 推送
     * push
     * @param mixed $user_id 
     * @param mixed $content 
     * @param mixed $extras 
     * @return mixed 
     */
    public static function push($user_id = 0,$content,$extras = ['type'=>'1']){

        if(!$user_id)return false;

        $L = L::getSingleInstance();

        $app_key = $L->config->push_app_key;
        $master_secret = $L->config->push_master_secret;

        $client = new \JPush\Client($app_key, $master_secret,LOG_ROOT.'push.log');
        $client2 = new \JPush\Client($app_key, $master_secret,LOG_ROOT.'push.log');



        $z1 = $client->push()
            ->options(['apns_production'=>1])
            ->setPlatform('ios')
            ->addAlias('A'.$user_id)
            ->androidNotification($content,['extras'=>$extras])
            ->iosNotification($content,['extras'=>$extras,'sound'=>'default'])
            ->send();
        $z2 = $client2->push()
        
            ->setPlatform('all')
            ->addAlias('A'.$user_id)
            ->androidNotification($content,['extras'=>$extras])
            ->iosNotification($content,['extras'=>$extras,'sound'=>'default'])
            ->send();

        return [$z1,$z2];

    }
    
}