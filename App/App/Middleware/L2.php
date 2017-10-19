<?php
namespace App\App\Middleware;
use Middleware;
use Request;
use App\App\Tool\Func;
use Uccu\DmcatTool\Tool\LocalConfig AS Config;
use Uccu\DmcatTool\Tool\AJAX;
use Response;

use App\App\Model\ConfigModel;
use App\App\Model\DoctorModel;


class L2 extends Middleware{

    private $request;

    public $config;
    public $doctor_token;
    public $userInfo;
    public $id;

    function __construct(){


        /*获取所有的参数*/
        $this->config = ConfigModel::copyMutiInstance()->get_field('value','name');


        /*获取干扰码*/
        $salt = $this->config->SITE_SALT;


        $this->request = Request::getSingleInstance();

        /*获取doctor_token*/
        $this->doctor_token = $this->request->request('doctor_token');
        if(!$this->doctor_token)$this->doctor_token = $this->request->cookie('doctor_token');
        if(!$this->doctor_token)return;


        /*处理doctor_token*/
        $doctor_token = substr($this->doctor_token,1);
        $doctor_token = Func::aes_decode($doctor_token,$this->config->AES_SECRECT_KEY);
        $doctor_token = substr($doctor_token,1);
        list($hash,$id,$time) = explode('|',base64_decode($doctor_token));
        if(!$hash||!$id||!$time){
            Response::getSingleInstance()->cookie('doctor_token','',-3600);
            return;
        }


        /*查询需要验证登陆的用户信息*/
        $user = DoctorModel::copyMutiInstance();
        $info = $user->find($id);
        if(!$info){
            Response::getSingleInstance()->cookie('doctor_token','',-3600);
            return;
        }

        if(!$info->active){
            Response::getSingleInstance()->cookie('doctor_token','',-3600);
            AJAX::error('账号已被禁用，请联系管理员！');
        }
        /*验证登陆合法性*/
        if($hash === sha1($info->password.$salt.$time)){
            $this->userInfo = $info;
            $this->id = $info->id;
            return;
        }

        /*其他情况*/
        Response::getSingleInstance()->cookie('doctor_token','',-3600);
    }




    
}