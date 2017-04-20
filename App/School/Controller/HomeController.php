<?php

namespace App\School\Controller;


use Controller;
use Response;
use App\School\Model\UserModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class HomeController extends Controller{

    private $cookie = false;

    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }

    function login(){

        View::hamlReader(__FUNCTION__,'App');
    }

    function attend(){

        View::hamlReader('doctor/'.__FUNCTION__,'App');
    }
    


}