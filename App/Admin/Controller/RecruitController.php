<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;

class RecruitController extends Controller{

    function __construct(){



    }

    function news(){


        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    
    function lists(){



    }




}