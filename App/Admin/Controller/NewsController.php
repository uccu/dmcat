<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Doowin\Middleware\L;

class NewsController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    # 集团要闻
        function group(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function group_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    
    # 热点专题
        function hot(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function hot_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

    # 媒体聚焦
        function media(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function media_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
    
    # 是屁中心
        function video(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }
        function video_detail($id){
            View::addData(['id'=>$id]);
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

        function video_type(){
            View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
        }

}