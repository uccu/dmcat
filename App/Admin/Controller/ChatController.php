<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Lawyer\Middleware\L;

class ChatController extends Controller{

    function __construct(){

        $this->L = L::getSingleInstance();
        

    }

    /* 快速问题 */
    function fast(){

        View::addData(['getList'=>'/chat/admin_fast']);
        View::hamlReader('home/list','Admin');
    }

    

    


}