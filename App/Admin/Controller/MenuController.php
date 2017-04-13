<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\School\Middleware\L;

class MenuController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    /* 经理 */
    function show(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->menu;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    



}