<?php

namespace App\School\Controller;

use App\School\Tool\Func;
use App\School\Middleware\L;
use Controller;

class WcController extends Controller{

    public $appid;
    public $app_secret;

    function __construct(){

        $this->L = L::getInstance();
        $this->appid = $this->L->config->wc_appid;
        $this->app_secret = $this->L->config->wc_app_secret;


    }


    
    function roll(){
        $redirect_uri = urlencode( 'http://weixin.ivy-china.com/wc/getCode' );
        $state = 'test';

        echo 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid
        .'&redirect_uri='.$redirect_uri
        .'&response_type=code&scope=snsapi_base&state='.$state
        .'#wechat_redirect';

    }

    function getCode($code,$state){

        if(!$code)die('微信连接失败！');

        $data['appid'] = $this->appid;
        $data['secret'] = $this->app_secret;
        $data['code'] = $code;
        $data['grant_type'] = 'authorization_code';


        $json = Func::curl('https://api.weixin.qq.com/sns/oauth2/access_token',$data);

        if(!$json)die('微信解析失败！');

        var_dump($json);
    }


    


}