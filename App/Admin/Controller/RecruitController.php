<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\School\Middleware\L;

class RecruitController extends Controller{

    function __construct(){

        $this->L = L::getInstance();

    }

    /* 新建招生考试信息 */
    function news(){


        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    
    /* 招生考试信息列表 */
    function lists(){


        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }
    function lists_w(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->recruit;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 招生考试信息详情 */
    function info(){

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    
    function slists_w(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->recruit;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }


}