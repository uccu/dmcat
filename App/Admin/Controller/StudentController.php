<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\School\Middleware\L;

class StudentController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    /* 学生档案 */
    function record(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->student;
        $lang->classes;
        $lang->school;
        View::addData(['lang'=>$lang,'type'=>$this->L->userInfo->type]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }


    function attendance(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->student;
        $lang->classes;
        $lang->school;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }
    
    function physical(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->student;
        $lang->classes;
        $lang->school;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }

    function comment(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->student;
        $lang->classes;
        $lang->school;
        View::addData(['lang'=>$lang,'type'=>$this->L->userInfo->type]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }

    function rest(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }

    


}