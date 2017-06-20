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

    
    
    # 德汇新天地
        function newWorld(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    # 万达广场
        function wandaSquare(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    # 亚欧新城
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


    
}