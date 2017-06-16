<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Doowin\Middleware\L;

class NewsController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    function group(){

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    
    function group_detail($id){

        View::addData(['id'=>$id]);

        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    


}