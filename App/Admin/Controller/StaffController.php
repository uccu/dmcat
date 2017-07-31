<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Lawyer\Middleware\L;

class StaffController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
        

    }

    /* 管理员 */
    function admin(){

        View::hamlReader('staff/'.__FUNCTION__,'Admin');
    }

    /*  用户 */
    function user(){

        View::hamlReader('staff/'.__FUNCTION__,'Admin');
    }
    

    


}