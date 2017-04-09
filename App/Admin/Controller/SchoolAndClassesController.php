<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\School\Middleware\L;

class SchoolAndClassesController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);

    }

    /* 学校 */
    function school(){

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    
    /* 班级 */
    function classes(){


        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    


}