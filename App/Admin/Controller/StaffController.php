<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Doowin\Middleware\L;

class StaffController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    /* 管理员 */
    function admin(){

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    

    


}