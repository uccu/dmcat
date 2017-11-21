<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Doowin\Middleware\L;

class ContactController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    # 德汇招聘
        function recruit(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

        function recruit_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    
    # 招标及公告
        function moves(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function moves_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }


    # 投诉及建议
        function complaints(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function complaints_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

    # 法律声明
        function notice(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        
}