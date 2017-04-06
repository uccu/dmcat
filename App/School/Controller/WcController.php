<?php

namespace App\School\Controller;


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

    function getCode(){

        var_dump($_REQUEST);
    }


    


}