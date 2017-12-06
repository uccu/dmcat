<?php
namespace App\Car\Middleware;
use Middleware;
use Request;
use App\Car\Tool\Func;
use Uccu\DmcatTool\Tool\LocalConfig AS Config;
use Uccu\DmcatTool\Tool\AJAX;
use Response;

use App\Car\Model\ConfigModel;
use App\Car\Model\UserModel;


class L extends Middleware{

    private $request;

    public $config;
    public $user_token;
    public $userInfo;
    public $id;

    function __construct(){


        /*获取所有的参数*/
        $this->config = ConfigModel::copyMutiInstance()->get_field('value','name');


        /*获取干扰码*/
        $salt = $this->config->SITE_SALT;


        $this->request = Request::getSingleInstance();

        /*获取user_token*/
        $this->user_token = $this->request->request('user_token');
        if(!$this->user_token)$this->user_token = $this->request->cookie('user_token');
        if(!$this->user_token)return;


        /*处理user_token*/
        $user_token = substr($this->user_token,1);
        $user_token = Func::aes_decode($user_token,$this->config->AES_SECRECT_KEY);
        $user_token = substr($user_token,1);
        list($hash,$id,$time) = explode('|',base64_decode($user_token));
        if(!$hash||!$id||!$time){
            Response::getSingleInstance()->cookie('user_token','',-3600);
            return;
        }


        /*查询需要验证登陆的用户信息*/
        $user = UserModel::copyMutiInstance();
        $info = $user->find($id);
        if(!$info){
            Response::getSingleInstance()->cookie('user_token','',-3600);
            return;
        }

        if(!$info->active){
            Response::getSingleInstance()->cookie('user_token','',-3600);
            AJAX::error('账号已被禁用，请联系管理员！');
        }

        if($info->last_login != $time){
            Response::getSingleInstance()->cookie('user_token','',-3600);
            return;
        }

        /*验证登陆合法性*/
        if($hash === sha1($info->password.$salt.$time)){
            $this->userInfo = $info;
            $this->id = $info->id;
            return;
        }

        /*其他情况*/
        Response::getSingleInstance()->cookie('user_token','',-3600);
    }




    
}