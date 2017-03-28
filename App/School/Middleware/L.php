<?php
namespace App\School\Middleware;
use Middleware;
use Request;
use App\School\Tool\Func;
use Config;
use Response;
use \App\School\Model\ConfigModel;
use \App\School\Model\UserModel;


class L extends Middleware{

    private $request;

    public $config;
    public $user_token;
    public $userInfo;
    public $id;

    function __construct(){


        /*获取所有的参数*/
        $this->config = ConfigModel::getInstance()->get_field('value','name');


        /*获取干扰码*/
        $salt = $this->config->site_salt;

        
        $this->request = Request::getInstance();

        /*获取user_token*/
        $this->user_token = $this->request->request('user_token');
        if(!$this->user_token)$this->user_token = $this->request->cookie('user_token');
        if(!$this->user_token)return;
        

        /*处理user_token*/
        $user_token = substr($this->user_token,1);
        $user_token = Func::aes_decode($user_token);
        $user_token = substr($user_token,1);
        list($hash,$id,$time) = explode('|',base64_decode($user_token));
        if(!$hash||!$id||!$time){
            Response::getInstance()->cookie('user_token','',-3600);
            return;
        }
        

        /*查询需要验证登陆的用户信息*/
        $user = UserModel::getInstance();
        $info = $user->find($id);
        if(!$info){
            Response::getInstance()->cookie('user_token','',-3600);
            return;
        }

        /*验证登陆合法性*/
        if($hash === sha1($info->password.$salt.$time)){
            $this->userInfo = $info;
            $this->id = $info->id;
            return;
        }

        /*其他情况*/
        Response::getInstance()->cookie('user_token','',-3600);
    }
    
}