<?php

namespace App\Admin\Controller;

use Controller;
use View;


class HomeController extends Controller{


    function __construct(){

        
    }

    function test(){

        View::hamlReader('Home','Admin');
    }

    function upd(){

        View::hamlReader('Home/upd','Admin');
    }


    function index(){

        View::hamlReader('Home','Admin');
    }

    function banner(){

        View::hamlReader('Home/Banner','Admin');
    }

    

    function setting(){

        View::hamlReader('home/Setting','Admin');
    }

    

}