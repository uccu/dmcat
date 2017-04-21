<?php

namespace App\School\Controller;


use Controller;
use Response;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class HomeController extends Controller{

    private $cookie = false;

    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }

    function login(){

        View::hamlReader(__FUNCTION__,'App');
    }

    function attend($code){

        $info = StudentModel::getInstance()->where(['rand_code'=>$code])->find();

        View::addData(['info'=>$info]);

        View::hamlReader('doctor/'.__FUNCTION__,'App');
    }

    function code(){

        $sss = Func::getSignature();

        $data['appId'] = $this->L->config->wc_appid;
        $data['timestamp'] = $sss['timestamp'];
        $data['nonceStr'] = $sss['noncestr'];
        $data['signature'] = $sss['sign'];
        
        View::addData($data);

        View::hamlReader('doctor/'.__FUNCTION__,'App');
    }
    

    function tes(){

        $sss = Func::getSignature();

        $data['appId'] = $this->L->config->wc_appid;
        $data['timestamp'] = $sss['timestamp'];
        $data['nonceStr'] = $sss['noncestr'];
        $data['signature'] = $sss['sign'];

        AJAX::success($out);
    }

}