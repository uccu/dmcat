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

        $lang = $this->L->i18n;
        
        View::addData(['lang'=>$lang]);
        View::addData(['userInfo'=>$this->L->userInfo]);

        View::hamlReader('index','Admin');
    }



    


}