<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\Lawyer\Middleware\L;


class LoginController extends Controller{


    function __construct(){

        $this->L = L::getSingleInstance();
        
        $array = [1];

        if($this->L->id && ($this->L->userInfo->type || $this->L->userInfo->master_type>-1)){
            header('Location:/admin/index');
            die();
        }

        
        View::hamlReader('login','Admin');
    }



    


}