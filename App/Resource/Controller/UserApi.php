<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;

use View;

use Model;

use Config;


class UserApi extends Controller{

    private $cookie = false;

    function __construct(){

        

    }

    function login(Request $request,Model $user){


        //获取表单
        $password   =   $request->request['password'];
        $email      =   $request->request['email'];
        $cookie     =   $request->request['cookie'];

        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;
        

        //在数据库里查询对应邮箱的账号
        $info = $user->where('email=%n',$email)->find();


        //如果查询失败，返回错误
        if(!$info)AJAX::error('邮箱不存在');

       /**
        *  验证密码
        *
        *  加密算法采用  sha1(网站干扰码+md5(密码)+用户干扰码)
        *
        */
        $salt = Config::get('SITE_SALT');
        $encryptedPassword = sha1($salt.md5($password).$info->salt);
        if($encryptedPassword!=$info->password)AJAX::error('密码错误');

        //输出登录返回信息
        $this->_out_info($info,$this->cookie);


    }

    private function _out_info($info,$cookie){




    }

    function register(){
        


    }

    function look($id = 0,Model $user){


        $data['info'] = $user->where('id=%d',$id)->find();

        AJAX::success($data);


    }

    



}