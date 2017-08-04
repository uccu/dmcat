<?php

namespace App\Admin\Controller;

use Controller;
use View;


class HomeController extends Controller{


    function __construct(){

        
    }

    function test(){

        View::hamlReader('home','Admin');
    }

    function upd(){

        View::hamlReader('home/upd','Admin');
    }


    function index(){

        View::hamlReader('home','Admin');
    }

    function banner(){

        View::addData(['getList'=>'/home/admin_banner']);
        View::hamlReader('home/list','Admin');
    }

    

    function setting(){

        View::hamlReader('home/setting','Admin');
    }

    

}