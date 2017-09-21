<?php
namespace App\Car\Middleware;
use Middleware;
use Request;
use App\Car\Tool\Func;
use Uccu\DmcatTool\Tool\LocalConfig AS Config;
use Uccu\DmcatTool\Tool\AJAX;
use Response;

use App\Car\Model\ConfigModel;
use App\Car\Model\AdminModel;


class L3 extends Middleware{

    private $request;

    public $config;
    public $admin_token;
    public $userInfo;
    public $id;

    function __construct(){


        /*获取所有的参数*/
        $this->config = ConfigModel::copyMutiInstance()->get_field('value','name');


        /*获取干扰码*/
        $salt = $this->config->SITE_SALT;


        $this->request = Request::getSingleInstance();

        /*获取admin_token*/
        $this->admin_token = $this->request->request('admin_token');
        if(!$this->admin_token)$this->admin_token = $this->request->cookie('admin_token');
        if(!$this->admin_token)return;


        /*处理admin_token*/
        $admin_token = substr($this->admin_token,1);
        $admin_token = Func::aes_decode($admin_token,$this->config->AES_SECRECT_KEY);
        $admin_token = substr($admin_token,1);
        list($hash,$id,$time) = explode('|',base64_decode($admin_token));
        if(!$hash||!$id||!$time){
            Response::getSingleInstance()->cookie('admin_token','',-3600);
            return;
        }


        /*查询需要验证登陆的用户信息*/
        $user = AdminModel::copyMutiInstance();
        $info = $user->find($id);
        if(!$info){
            Response::getSingleInstance()->cookie('admin_token','',-3600);
            return;
        }

        if(!$info->active){
            Response::getSingleInstance()->cookie('admin_token','',-3600);
            AJAX::error('账号已被禁用，请联系管理员！');
        }
        /*验证登陆合法性*/
        if($hash === sha1($info->password.$salt.$time)){
            $this->userInfo = $info;
            $this->id = $info->id;
            return;
        }

        /*其他情况*/
        Response::getSingleInstance()->cookie('admin_token','',-3600);
    }




    
}