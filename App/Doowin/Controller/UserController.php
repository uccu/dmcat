<?php

namespace App\Doowin\Controller;


use Controller;
use Response;
use App\Doowin\Model\UserModel;
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
    


}