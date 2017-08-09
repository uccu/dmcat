<?php
namespace App\Lawyer\Middleware;
use Middleware;
use Request;
use App\Lawyer\Tool\Func;
use Config;
use AJAX;
use Response;

use App\Lawyer\Model\ConfigModel;
use App\Lawyer\Model\LawyerModel;
use App\Lawyer\Model\AdminMenuModel;


class L2 extends Middleware{

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
        $this->user_token = $this->request->request('lawyer_token');
        if(!$this->user_token)$this->user_token = $this->request->cookie('lawyer_token');
        if(!$this->user_token)return;


        /*处理user_token*/
        $user_token = substr($this->user_token,1);
        $user_token = Func::aes_decode($user_token,$this->config->AES_SECRECT_KEY);
        $user_token = substr($user_token,1);
        list($hash,$id,$time) = explode('|',base64_decode($user_token));
        if(!$hash||!$id||!$time){
            Response::getSingleInstance()->cookie('lawyer_token','',-3600);
            return;
        }


        /*查询需要验证登陆的用户信息*/
        $user = LawyerModel::copyMutiInstance();
        $info = $user->find($id);
        if(!$info){
            Response::getSingleInstance()->cookie('lawyer_token','',-3600);
            return;
        }

        !$info->active && AJAX::error('账号已被禁用，请联系管理员！');

        /*验证登陆合法性*/
        if($hash === sha1($info->password.$salt.$time)){
            $this->userInfo = $info;
            $this->id = $info->id;
            return;
        }

        /*其他情况*/
        Response::getSingleInstance()->cookie('lawyer_token','',-3600);
    }



    function adminPermissionCheck($id){

        !$this->id && AJAX::error('未登录');
        $type = $this->userInfo->type;
        $auth = AdminMenuModel::copyMutiInstance()->find($id)->auth;
        $auth = $auth ? explode(',',$auth) : [];
        
        !in_array($type,$auth) && AJAX::error('没有权限，请与超级管理员联系！');

        return true;
    }
    
}