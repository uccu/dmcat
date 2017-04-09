<?php
namespace App\School\Middleware;
use Middleware;
use Request;
use App\School\Tool\Func;
use Config;
use Response;
use App\School\Model\ConfigModel;
use App\School\Model\UserModel;

class L extends Middleware{

    private $request;
    public $config;
    public $user_token;
    public $userInfo;
    public $id;
    public $i18n;

    function __construct(){

        $this->request = Request::getInstance();

        /* 导入国际化 */
        $this->i18n = I18n::getInstance();
        /* 设置语言 */
        $this->i18n->setLanguage( $this->request->cookie('language','cn') );

        /*获取所有的参数*/
        $this->config = ConfigModel::getInstance()->get_field('value','name');


        /*获取干扰码*/
        $salt = $this->config->site_salt;

        
        

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
            if($this->i18n->language != 'cn')$this->userInfo->name = $this->userInfo->name_en;
            $this->id = $info->id;
            return;
        }

        /*其他情况*/
        $this->delCookie();
    }

    function delCookie(){
        Response::getInstance()->cookie('user_token','',-3600);
    }
    
}