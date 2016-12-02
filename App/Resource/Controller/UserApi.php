<?php

namespace App\Resource\Controller;

use Controller;

use AJAX;

use Request;

use View;

use Model;


class UserApi extends Controller{

    private $cookie = false;

    function __construct(){

        

    }

    function login(Request $request,Model $user){
        $password = $request->post['password'];
        $email = $request->post['email'];
        $where['usercode'] = $usercode;
        if($cookie = $request->post['cookie'])$this->cookie = true;
        
        $info = $user->where('email=%n',$email)->find();



        if(!$info)AJAX::error('邮箱不存在');
        if(md5(md5($password).$this->salt)!=$info['password'])$this->errorCode(402);
        $this->_out_info($info,$this->cookie);


    }

    function register(){
        


    }

    function look($id = 0,Model $user){


        $data['info'] = $user->where('id=%d',$id)->find();

        AJAX::success($data);


    }

    



}