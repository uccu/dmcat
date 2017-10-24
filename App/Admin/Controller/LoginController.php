<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\App\Middleware\L3;


class LoginController extends Controller{


    function __construct(){

        $this->L = L3::getSingleInstance();
        
        $array = [1];

        if($this->L->id){
            header('Location:/admin/index');
            die();
        }

        
        View::hamlReader('login','Admin');
    }



    


}