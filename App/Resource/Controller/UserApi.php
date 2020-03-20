<?php

namespace App\Resource\Controller;

use App\Resource\Tool\Func;
use App\Resource\Middleware\Token;
use Controller;
use Uccu\DmcatTool\Tool\AJAX;
use Uccu\DmcatHttp\Request;
use Uccu\DmcatHttp\Response;
use Model;
use Config;
use stdClass;



class UserApi extends Controller{

    private $cookie = false;
    private $salt;

    function __construct(){

        $this->salt = Config::get('SITE_SALT');

    }

    function login($password,$email,$cookie, Model $user){

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

        $encryptedPassword = sha1($this->salt.md5($password).$info->salt);
        if($encryptedPassword!=$info->password)AJAX::error('密码错误');

        //输出登录返回信息
        $this->_out_info($info);


    }

    private function _out_info($info){

        $user_token = Func::randWord().Func::aes_encode(Func::randWord().base64_encode(sha1($info->password.$this->salt.TIME_NOW,true).'|'.$info->id.'|'.TIME_NOW));

        if($this->cookie)Response::getSingleInstance()->cookie('user_token',$user_token,0);
        
        $out = [
            'user_token'=>$user_token,
            'id'=>$info->id,
        ];
        
        AJAX::success($out);

    }

    private function _add_user($info){

        $info->ctime = TIME_NOW;
        $info->nickname = $info->nickname?$info->nickname:'user_'.$info->ctime;
        if(!Func::validate_email($info->email)){
            AJAX::error('邮箱地址错误');
        }

        $user = Model::getInstance('user');
        if($user->where(['nickname'=>$info->nickname])->find()){
            AJAX::error('昵称已存在');
        }
        $user = Model::getInstance('user');
        if($user->where(['email'=>$info->email])->find()){
            AJAX::error('邮箱已存在');
        }
        $user = Model::getInstance('user');
        $info->id = $user->set($info)->add();

        if(!$info->id){
            AJAX::error('新用户创建失败');
        }

        $this->_out_info($info);

    }

    function register($password,$email,$nickname,$cookie){
        


        //是否储存登录信息到cookie
        if($cookie)$this->cookie = true;

        $info           = new stdClass;
        $info->nickname = $nickname;
        $info->email    = $email;
        $info->salt     = Func::randWord(6);
        $info->password = sha1($this->salt.md5($password).$info->salt);

        $this->_add_user($info);

    }

    function look($id = 0,Model $user){

        $user2 = clone $user;

        $u = $user2->find($id);

        $u->type = 2;

        $u->save();

        $data['info'] = $user->where('id=%d',$id)->find();

        AJAX::success($data);


    }

    function me(Token $token){

        echo $token->id;

    }

    



}