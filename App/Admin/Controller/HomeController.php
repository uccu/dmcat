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


    function index(){

        View::hamlReader('home','Admin');
    }


}