<?php
namespace App\Doowin\Tool;
use Config;
use App\Doowin\Tool\AJAX;
use App\Doowin\Middleware\L;
use App\Doowin\Model\ConfigModel;
use App\Doowin\Model\MessageModel;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Component\HttpFoundation\Response;

use App\Doowin\Model\StudentModel;

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

    

    /* 密码安全性验证 */
    static function check_password($password){

        !$password && AJAX::error('密码为空');
        preg_match('#^\d+$#',$password) && AJAX::error('密码需要英文字母+数字组成');
        preg_match('#^[a-z]+$#i',$password) && AJAX::error('密码需要英文字母+数字组成');
        return true;
    }

    public static function getInstance(){
        static $object;
		if(empty($object)) $object = new self();
		return $object;
    }

    static function upload($name){
        if(!$_FILES)return [];

        $func = self::getInstance();

        $paths = [];
        $upn = $upa = [];
        if(!is_dir(BASE_ROOT.'upload'))
                !mkdir(BASE_ROOT.'upload',0777,true) && AJAX::error('文件夹权限不足，无法创建文件！');
        foreach($_FILES as $k=>$file){
            $upn[] = $k;
            if(!$name || $k==$name){
                $upa[] = $k;
                $paths[$k] = date('Ymd_His',TIME_NOW).'_'.$k.'.cache';
                move_uploaded_file($file['tmp_name'],BASE_ROOT."upload/".$paths[$k]);
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

        $func = self::getInstance();

        $paths = [];
        $upn = $upa = [];
        foreach($_FILES as $k=>$file){
            $upn[] = $k;
            if(!$name || $k==$name){
                $upa[] = $k;
                $paths[$k] = $func->uploadPic($file['tmp_name'],0,$width,$height,$cut);
            }
            
        }

        $data['upn'] = implode(',',$upn);
        $data['upa'] = implode(',',$upa);
        $data['succ'] = ($name?$paths[$name]:$paths) ? 1 : 0;
        $data['ctime'] = TIME_NOW;
        
        // LogUploadModel::getInstance()->set($data)->add();

        return $name?$paths[$name]:$paths;

    }


    /* 处理上传图片 */
    function uploadPic($tmp_name,$type = 0,$width = 0,$height = 0,$cut = 0){

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
    public static function add_zero($number,$total){

        $string = strval($number);

        $len = strlen($string);

        for($i = $len;$i<$total;$i++){

            $string = '0'.$string;
        }

        return $string;

    }


   



    /* 判断是否通过ssl访问 */
    public static function is_SSL(){  
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)  
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0; 
    }  

    /* 返回全图片网址 */
    public static function fullPicAddr($path){

        return self::fullAddr('pic/'.$path);
    }
    /* 返回全网址 */
    public static function fullAddr($path){

        return (self::is_SSL()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.$path;
    }

    /* 模拟curl */
    public static function curl($url,$data = []){

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


    public static function student_qr($id = 0){

        $user = StudentModel::getInstance()->find($id);

        $zzz = 'z';

        $zzz = $user ? $user->rand_code : $zzz;

        $qrCode = new QrCode($zzz);
        $qrCode->setSize(300);
        
        $qrCode
            ->setMargin(10)
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
            ->setValidateResult(true);

        header('Content-Type: '.$qrCode->getContentType(PngWriter::class));
        echo $qrCode->writeString(PngWriter::class);

    }



    public static function calculateDayCount($month){

        $myz = $monthFull = $month;
        
        if(strlen($month) == 6){
            $month = substr($monthFull,-2);
            $year = substr($monthFull,0,4);
            $myz = $year.'-'.$month;
        }

        $date = new \DateTime($myz);
        $date->add(new \DateInterval('P1M'));
        $date->sub(new \DateInterval('P1D'));
        $dayOfThisMonth = $date->format('d');

        return $dayOfThisMonth;

    }
    public static function subdate($date){
        $myz = $monthFull = $date;
        
        if(strlen($month) == 8){
            $month = substr($monthFull,4,2);
            $year = substr($monthFull,0,4);
            $day = substr($monthFull,6);
            $myz = $year.'-'.$month.'-'.$day;
        }

        $date = new \DateTime($myz);
        $date->sub(new \DateInterval('P1D'));
        $dayOfThisMonth = $date->format('Y-m-d');
        return $dayOfThisMonth;
    }
    public static function adddate($date){
        $myz = $monthFull = $date;
        
        if(strlen($month) == 8){
            $month = substr($monthFull,4,2);
            $year = substr($monthFull,0,4);
            $day = substr($monthFull,6);
            $myz = $year.'-'.$month.'-'.$day;
        }

        $date = new \DateTime($myz);
        $date->add(new \DateInterval('P1D'));
        $dayOfThisMonth = $date->format('Y-m-d');
        return $dayOfThisMonth;
    }

    public static function getAccessToken(){

        $L = L::getInstance();
        
        $access_token = $L->config->wc_access_token;

        $configModel = ConfigModel::getInstance();
        $timeline = $configModel->where(['name'=>'wc_access_token'])->find()->timeline;

        if($timeline < TIME_NOW){

            $appid = $L->config->wc_appid;
            $app_secret = $L->config->wc_app_secret;

            $data = self::curl('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$app_secret);
            $access_token = $data->access_token;

            if(!$access_token)AJAX::error('get access_token failed');
            $configModel->set(['value'=>$access_token,'timeline'=>TIME_NOW+6000])->where(['name'=>'wc_access_token'])->save();
        }


        return $access_token;
        

    }

    public static function getJsapiTicket(){

        $L = L::getInstance();

        $jsapi_ticket = $L->config->wc_jsapi_ticket;

        $configModel = ConfigModel::getInstance();
        $timeline = $configModel->where(['name'=>'wc_jsapi_ticket'])->find()->timeline;

        if($timeline < TIME_NOW){

            $access_token = self::getAccessToken();
            $data = self::curl('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi');
            $jsapi_ticket = $data->ticket;

            if($data->errcode)AJAX::error($data->errcode.':'.$data->errmsg);
            ConfigModel::getInstance()->set(['value'=>$jsapi_ticket,'timeline'=>TIME_NOW+6000])->where(['name'=>'wc_jsapi_ticket'])->save();
        }

        return $jsapi_ticket;

    }


    public static function getSignature(){

        $data['noncestr'] = self::randWord(18);
        $data['jsapi_ticket'] = self::getJsapiTicket();
        $data['timestamp'] = TIME_NOW;
        $data['url'] = self::fullAddr(REQUEST_PATH);

        ksort($data,SORT_STRING);

        $data2 = [];
        foreach($data as $k=>$v){
            $data2[] =  $k.'='.$v;
        }
        $data['sign'] = sha1(implode('&',$data2));
        return $data;
    }



    public static function add_message($user_id,$message,$url = ''){

        $model = MessageModel::getInstance();

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

    public static function getPageLink($page, $count, $url = "",$length = 16)
    {


        $total_page = (int)ceil($count / $length);
        /*if ($total_page <= 1) {
            return 0;
        }*/
        /*合成分页链接*/
        $html_pages = '';
        $html_pages .= '<span>'.lang('每页显示') . $length . lang('条，共') . $count . lang('条记录').'</span><div id="links">';
        if ($page != 1) {
            $html_pages .= '<a href="' . $url . '?page=1" >'.lang('首页').'</a>';
        } else {
            $html_pages .= '<a href="javascript:void(0);" class="no_page">'.lang('首页').'</a>';
        }
        if ($page != 1) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page - 1) . '" >'.lang('上一页').'</a>';
        }
        if (($page - 4) >= 1) {
            $html_pages .= '<a href="javascript:void(0);" class="no_page">...</a>';
        }
        if (($page - 3) >= 1) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page - 3) . '" >' . ($page - 3) . '</a>';
        }
        if (($page - 2) >= 1) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page - 2) . '" >' . ($page - 2) . '</a>';
        }
        if (($page - 1) >= 1) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page - 1) . '" >' . ($page - 1) . '</a>';
        }
        $html_pages .= '<a href="javascript:void(0);" class="this_page">' . $page . '</a>';
        if (($page + 1) <= $total_page) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page + 1) . '" >' . ($page + 1) . '</a>';
        }
        if (($page + 2) <= $total_page) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page + 2) . '" >' . ($page + 2) . '</a>';
        }
        if (($page + 3) <= $total_page) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page + 3) . '" >' . ($page + 3) . '</a>';
        }
        if (($page + 4) <= $total_page) {
            $html_pages .= '<a href="javascript:void(0);" class="no_page">...</a>';
        }
        if ($page != $total_page && $total_page != 0) {
            $html_pages .= '<a href="' . $url . '?page=' . ($page + 1) . '" >'.lang('下一页').'</a>';
        }
        if ($page != $total_page && $total_page != 0) {
            $html_pages .= '<a href="' . $url . '?page=' . $total_page . '" >'.lang('末页').'</a>';
        } else {
            $html_pages .= '<a href="javascript:void(0);" class="no_page">'.lang('末页').'</a>';
        }
        return $html_pages .= "</div>";

    }
    
}