<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\School\Middleware\L;

class IndexController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

        $array = [1];

        if(!$this->L->id || !$this->L->userInfo->type){
            header('Location:/user/logout');
        }

        View::hamlReader('index','Admin');
    }



    


}