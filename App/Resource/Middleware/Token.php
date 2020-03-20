<?php

namespace App\Resource\Middleware;

use Middleware;
use Uccu\DmcatHttp\Request;
use App\Resource\Tool\Func;
use Config;
use Uccu\DmcatTool\Tool\AJAX;
use Model;
use Uccu\DmcatHttp\Response;

class Token extends Middleware{

    function __construct(){

        $salt = Config::get('SITE_SALT');

        $user_token = Request::getSingleInstance()->post('user_token');

        if(!$user_token)$user_token = Request::getSingleInstance()->cookie('user_token');

        if(!$user_token)return;

        $user_token = substr($user_token,1);
        $user_token = Func::aes_decode($user_token);
        $user_token = substr($user_token,1);
        list($hash,$id,$time) = explode('|',base64_decode($user_token));

        if(!$hash||!$id||!$time||$time+2600*24<TIME_NOW){

            Response::getSingleInstance()->cookie('user_token','',-3600);
            return;
        }

        $user = Model::getInstance('user');
        $info = $user->find($id);

        if(!$info){

            Response::getSingleInstance()->cookie('user_token','',-3600);
            return;
        }

        if($hash === sha1($info->password.$salt.$time,true)){

            $this->userInfo = $info;
            $this->id = $info->id;
            return;
        }
        Response::getSingleInstance()->cookie('user_token','',-3600);

    }


    


}