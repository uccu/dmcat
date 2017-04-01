<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;

class RecruitController extends Controller{

    function __construct(){



    }

    /* 新建招生考试信息 */
    function news(){


        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    
    /* 招生考试信息列表 */
    function lists(){


        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 招生考试信息详情 */
    function info(){

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 招生考试报名列表 */
    function slists(){

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 招生考试报名详情 */
    function sinfo(){

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }




}