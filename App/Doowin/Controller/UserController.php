<?php

namespace App\Doowin\Controller;


use Controller;
use Response;
use App\Doowin\Model\UserModel;
use App\Doowin\Model\CaptchaModel;
use App\Doowin\Middleware\L;
use App\Doowin\Tool\Func;
use AJAX;
use Model;

class UserController extends Controller{

    private $cookie = false;
    private $salt;
    private $L;


    function __construct(){

        $this->L = L::getInstance();
        $this->salt = $this->L->config->site_salt;

    }

    function video_type($l = 0){
        $l = (string)floor($l);
        Response::getInstance()->cookie('video_type',$l,0);
        AJAX::success();

    }

    function recruit_type($l = 0){
        $l = (string)floor($l);
        Response::getInstance()->cookie('recruit_type',$l,0);
        AJAX::success();

    }

    function language($l = 'cn'){
        if(!is_string($l))$l = 'cn';
        Response::getInstance()->cookie('language',$l,0);
        AJAX::success();

    }

    function my_info(UserModel $model){

        if(!$this->L->id)AJAX::error('未登录/not login');

        $info = $model->find($this->L->id);
        $out['info'] = $info;
        AJAX::success($out);

    }


    /* 通过用户ID判断用户是否存在 */
    function exist($id = 0){

        $user = UserModel::getInstance()->find($id);
        $outData['exist'] = $user ? true : false;
        AJAX::success($outData);
    }


    /* 生成登录TOKEN */
    private function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }

    private function encrypt_token($info){
        return Func::randWord().Func::aes_encode(Func::randWord().base64_encode(sha1($info->password.$this->salt.TIME_NOW).'|'.$info->id.'|'.TIME_NOW));
    }

    function logout(){

        Response::getInstance()->cookie('user_token','',-3600);
        header('Location:/admin/login');
    }

    function login($user_name = null,$password =null,UserModel $model,$cookie = null){


        //检查参数是否存在
        !$user_name && AJAX::error('用户名不能为空！');
        
        !$password && AJAX::error('密码不能为空！');
        

        

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;


        //找到对应用户名的账号
        $info = $model->where(['user_name'=>$user_name])->find();
        !$info && AJAX::error('用户不存在！');


       /**
        *  验证密码
        *  加密算法采用  sha1(网站干扰码+md5(密码)+用户干扰码)
        */
        $encryptedPassword = $this->encrypt_password($password,$info->salt);
        if($encryptedPassword!=$info->password)AJAX::error('密码错误！');


        //输出登录返回信息
        $this->_out_info($info);


    }

    private function _out_info($info){
        
        $user_token = $this->encrypt_token($info);
        

        $this->cookie && Response::getInstance()->cookie('user_token',$user_token,0);
        
        $out = [
            'user_token'=>$user_token,
            'id'=>$info->id,
            'type'=>$info->type
        ];
        
        AJAX::success($out);
    }




    private function _add_user($info){

        $info->create_time = TIME_NOW;

        
        $model = UserModel::getInstance();
        $model->where(['user_name'=>$info->user_name])->find() && AJAX::error('用户名已存在');
            
        

        DB::start();
       
        $info->id = $model->set($info)->add()->getStatus();
        !$info->id && AJAX::error('新用户创建失败');
        
        
        $info = $model->find($info->id);


        DB::commit();
        
        $this->_out_info($info);
    }
    

    /**
    * 生成验证码 
    * 做了下修改
    * @source  https://www.oschina.net/code/snippet_258733_12375
    */
    function captcha() {
        $num = 4;$size = 20;$width = 0;$height = 0;
        !$width && $width = $num * $size  + 5;
        !$height && $height = $size + 10; 
        // 去掉了 0 1 O l 等
        $str = "1234567890";
        $code = '';
        for ($i = 0; $i < $num; $i++) {
            $code .= $str[mt_rand(0, strlen($str)-1)];
        } 
        // 画图像
        $im = imagecreatetruecolor($width, $height); 
        // 定义要用到的颜色
        $back_color = imagecolorallocate($im, 235, 236, 237);
        $boer_color = imagecolorallocate($im, 118, 151, 199);
        $text_color = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120)); 
        // 画背景
        imagefilledrectangle($im, 0, 0, $width, $height, $back_color); 
        // 画边框
        imagerectangle($im, 0, 0, $width-1, $height-1, $boer_color); 
        // 画干扰线
        imagesetthickness ( $im , 2 );
        for($i = 0;$i < 10;$i++) {
            
            imagearc($im, mt_rand(- $width, $width), mt_rand(- $height, $height), mt_rand(30, $width * 2), mt_rand(20, $height * 2), mt_rand(0, 360), mt_rand(0, 360), $text_color);
        } 
        
        // 画验证码
        @imagefttext($im, $size + 15 , 0, 10, $size + 10, $text_color, BASE_ROOT.'App/Doowin/1980sWriter.ttf', $code);
        if($this->L->id){
            $data['user_id'] = $this->L->id;
        }
        $data['ctime'] = TIME_NOW;
        $data['ip'] = $_SERVER["REMOTE_ADDR"];
        $data['value'] = $code;
        $model = CaptchaModel::getInstance();
        $model->where('ip=%n AND type=0',$data['ip'])->remove();
        $model->set($data)->add();
        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-type: image/png;charset=utf=8");
        imagepng($im);
        imagedestroy($im);
    } 
}