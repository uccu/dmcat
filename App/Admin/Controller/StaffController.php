<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\School\Middleware\L;

class StaffController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    /* 经理 */
    function admin(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->user;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    

    /* 校长 */
    function principal(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->user;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }

    /* 行政 */
    function executive(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->user;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }

    /* 老师 */
    function teacher(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->user;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }


    /* 医生 */
    function doctor(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->user;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }


    /* 市场 */
    function market(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->user;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }



}