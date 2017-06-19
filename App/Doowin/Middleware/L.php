<?php
namespace App\Doowin\Middleware;
use Middleware;
use Request;
use App\Doowin\Tool\Func;
use Config;
use Response;
use App\Doowin\Model\ConfigModel;
use App\Doowin\Model\UserModel;
use AJAX;
use Model;

class L extends Middleware{

    private $request;
    public $config;
    public $user_token;
    public $userInfo;
    public $id;
    public $lang;


    function __construct(){

        $this->request = Request::getInstance();

        $this->lang = $this->request->cookie('language','cn');

        /*获取所有的参数*/
        $this->config = ConfigModel::getInstance()->get_field('value','name');


        /*获取干扰码*/
        $salt = $this->config->site_salt;

        header('Access-Control-Allow-Origin:*');
        
        

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
            $this->delCookie();
            return;
        }
        

        /*查询需要验证登陆的用户信息*/
        $user = UserModel::getInstance();
        $info = $user->find($id);
        if(!$info){
            $this->delCookie();
            return;
        }

        /*验证登陆合法性*/
        if($hash === sha1($info->password.$salt.$time)){
            $this->userInfo = $info;
            $this->id = $info->id;
            return;
        }

        /*其他情况*/
        $this->delCookie();
    }

    function delCookie(){
        Response::getInstance()->cookie('user_token','',-3600);
    }

    function check_type($arr){

        if(!$this->id)AJAX::error('没有登录');
        if(!is_array($arr))$arr = [$arr];
        if(!in_array($this->userInfo->type,$arr))AJAX::error('没有权限');

    }
    
}