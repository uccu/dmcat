<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Doowin\Middleware\L;

class IndustryController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    
    
    # 德汇宝贝广场
        function newWorld(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    # 德汇万达广场
        function wandaSquare(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    # 德汇特色小镇
        function newCity(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    # 德汇金融
        function finance(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    # 德汇教育
        function edu(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    # 德汇物流
        function logistics(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }


    
}