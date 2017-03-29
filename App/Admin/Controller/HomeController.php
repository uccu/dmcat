<?php

namespace App\Admin\Controller;

use Controller;
use View;


class HomeController extends Controller{


    function __construct(){

        
    }

    function test(){

        View::hamlReader('test','Admin');
    }


    function index(){

        View::hamlReader('test','Admin');
    }


}