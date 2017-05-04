<?php

namespace App\Project\Controller;

use Controller;

use View;


class MyController extends Controller{


    function __construct(){

        
        
    }



    function chat(){

        View::hamlReader('chat','App');

 
    }

    

}
