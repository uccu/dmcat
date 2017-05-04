<?php

namespace App\Project\Controller;

use Controller;

use View;


class IndexController extends Controller{


    function __construct(){

        View::hamlReader('index','App');
        
    }




    

}
