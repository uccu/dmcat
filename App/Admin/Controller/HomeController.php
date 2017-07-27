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


    function index(){

        View::hamlReader('Home','Admin');
    }

    function banner(){

        View::hamlReader('Home/Banner','Admin');
    }

    function setting(){

        View::hamlReader('home/Setting','Admin');
    }

    function m1(){

        View::hamlReader('home/m1','Admin');
    }

    function m2(){

        View::hamlReader('home/m2','Admin');
    }

    function m3(){

        View::hamlReader('home/m3','Admin');
    }

    function m4(){

        View::hamlReader('home/m4','Admin');
    }

    function m5(){

        View::hamlReader('home/m5','Admin');
    }

}