<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\School\Middleware\L;


class LoginController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

        $array = [1];
        echo $this->L->id;
        if($this->L->id && $this->L->userInfo->type){
            header('Location:index');
        }

        View::hamlReader('login','Admin');
    }



    


}