<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\School\Middleware\L;

class I18nController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    /* i */
    function i(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    

    

}