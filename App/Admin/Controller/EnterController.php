<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Doowin\Middleware\L;

class EnterController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    # 集团简介
        function introduction(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function introduction_product(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function introduction_product_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    
    # 董事长专区
        function chairman(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function chairman_picture(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }


    # 发展历程
        function develop(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

    # 企业文化
        function culture(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function newspaper(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function newspaper_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function magazine(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

    # 企业荣誉
        function honor(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

    # 社会责任
        function responsibility(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function charitable(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

}