<?php

namespace App\School\Controller;


use Controller;
use Response;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class ParentController extends Controller{



    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }

    function index(){

        View::hamlReader('parent/'.__FUNCTION__,'App');
    }
    


}