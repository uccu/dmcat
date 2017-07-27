<?php

namespace App\Lawyer\Controller;

use Controller;
use AJAX;
use DB;
use stdClass;
use Response;
# Model
use App\Lawyer\Model\UserModel;
use App\Lawyer\Middleware\L;
use App\Lawyer\Tool\Func;

class UserController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }

    /** 给密码加密
     * 2333
     * @param mixed $password 
     * @param mixed $salt 
     * @return mixed 
     */
    private function encrypt_password($password,$salt){
        return sha1($this->salt.md5($password).$salt);
    }
    /** 生成登录TOKEN
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function encrypt_token($info){
        return Func::randWord().Func::aes_encode(Func::randWord().base64_encode(sha1($info->password.$this->salt.TIME_NOW).'|'.$info->id.'|'.TIME_NOW));
    }

    /** 添加新用户
     * _add_user
     * @param mixed $info 
     * @return mixed 
     */
    private function _add_user($info){
        $info->create_time = TIME_NOW;

        

        $model = UserModel::getInstance();
        if($model->where(['phone'=>$info->phone])->find()){
            AJAX::error('手机号已存在');
        }

        DB::start();


        $info->id = $model->set($info)->add()->getStatus();
        
        !$info->id && AJAX::error('新用户创建失败');
        
        $info = $model->find($info->id);


        DB::commit();
        
        $this->_out_info($info);
    }

    /** 登出
     * 
     * @return mixed 
     */
    function logout(){
        Response::getInstance()->cookie('user_token','',-3600);
        header('Location:/admin/login');
    }

    /** 登录
     * 
     * @param mixed $phone 
     * @param mixed $password 
     * @param mixed $cookie 
     * @return mixed 
     */
    function login($phone = null,$password =null,UserModel $model,$cookie = null){


        //检查参数是否存在
        !$phone && AJAX::error('账号不能为空！');
        !$password && AJAX::error('密码不能为空！');
        
        //找到对应手机号的用户
        $info = $model->where('phone=%n',$phone)->find();
        !$info && AJAX::error('用户不存在');

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        # 验证密码 加密算法采用  sha1(网站干扰码+md5(密码)+用户干扰码)
        $encryptedPassword = $this->encrypt_password($password,$info->salt);
        if($encryptedPassword!=$info->password)AJAX::error('密码错误');

        //输出登录返回信息
        $this->_out_info($info);


    }

    /** 第三方登录
     * other_login
     * @param mixed $type 
     * @param mixed $code 
     * @param mixed $cookie 
     * @return mixed 
     */
    function other_login($type,$code,$cookie = null){

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        !in_array($type,['wx','wb','qq']) && AJAX::error('不支持的登录方式！');

        !$code && AJAX::error('未知的第三方登录标示！');

        $model = UserModel::getInstance();
        $userInfo = $model->where([$type=>$code])->find();

        !$userInfo && AJAX::error('用户不存在！');

        //输出登录返回信息
        $this->_out_info($info);
    }

    /** 输出用户登录信息
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private function _out_info($info){
        
        $user_token = $this->encrypt_token($info);
        
        $this->cookie && Response::getInstance()->cookie('user_token',$user_token,0);
        
        $out = [
            'user_token'=>$user_token,
            'id'=>$info->id,
            
        ];
        
        AJAX::success($out);
    }


    /** 注册
     * register
     * @param mixed $password 
     * @param mixed $phone 
     * @param mixed $captcha 
     * @param mixed $phone_captcha 
     * @param mixed $cookie 
     * @param mixed $parent 
     * @return mixed 
     */
    function register($password,$phone,$phone_captcha,$cookie = false){
        
        
        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone($phone);

        Func::check_password($password);

        Func::check_phone_captcha($phone,$phone_captcha);

        $info           = new stdClass;
        $info->phone    = $phone;
        $info->name     = $phone;
        $info->salt     = Func::randWord(6);
        $info->password = $this->encrypt_password($password,$info->salt);
        $this->_add_user($info);
    }

    
    /** 第三方注册
     * other_register
     * @param mixed $type 
     * @param mixed $code 
     * @param mixed $phone 
     * @param mixed $phone_captcha 
     * @param mixed $cookie 
     * @return mixed 
     */
    function other_register($type,$code,$phone,$phone_captcha,$cookie = false){

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        !in_array($type,['wx','wb','qq']) && AJAX::error('不支持的登录方式！');

        
        Func::check_phone_captcha($phone,$phone_captcha);

        $model = UserModel::getInstance();
        if($userInfo = $model->where(['phone'=>$phone])->find()){
        
            $userInfo->$type && AJAX::error('已绑定该第三方登录，请解绑后重新绑定！');
            !$code && AJAX::error('未知的第三方登录标示！');

            $model->where([$type=>$code])->find() && AJAX::error('已绑定账号，请直接登录！');

            $userInfo->$type = $code;
            $userInfo->save();

            $this->_out_info($userInfo);

        }else{
    
            $password = '';

            $info           = new stdClass;
            $info->phone    = $phone;
            $info->name     = $phone;
            $info->salt     = Func::randWord(6);
            $info->password = $this->encrypt_password($password,$info->salt);

            $this->_add_user($info);
        }

        
    }


    /** 修改密码
     * forget_password
     * @param mixed $new_password 
     * @param mixed $phone 
     * @param mixed $phone_captcha 
     * @param mixed $cookie 
     * @return mixed 
     */
    function forget_password($new_password,$phone,$phone_captcha,$cookie = false){

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        Func::check_phone_captcha($phone,$phone_captcha);

        $model = UserModel::getInstance();
        if(!$userInfo = $model->where(['phone'=>$phone])->find()){

            AJAX::error('用户不存在！');
        }

        $userInfo->password = $this->encrypt_password($new_password,$userInfo->salt);
        $userInfo->save();

        $this->_out_info($userInfo);


    }

    
    /** 发送手机验证码
     * captcha
     * @param mixed $phone 手机号
     * @param mixed $out 是否输出AJAX
     * @return mixed 
     */
    function captcha($phone,$out = 1) {

        Func::check_phone($phone);

        if($out)AJAX::success();

    }

}