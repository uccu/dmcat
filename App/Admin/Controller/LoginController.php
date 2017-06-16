<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\Doowin\Middleware\L;


class LoginController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

        $array = [1];

        if($this->L->id && $this->L->userInfo->type){
            header('Location:/admin/index');
        }


        View::hamlReader('login','Admin');
    }



    


}