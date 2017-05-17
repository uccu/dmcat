<?php

namespace App\Project\Controller;

use Controller;

use View;


class MyController extends Controller{


    function __construct(){

        
        
    }



    function audio(){

        View::hamlReader('audio','App');

 
    }
    function audio2(){

        View::hamlReader('audio_test','App');

 
    }

    function video(){

        View::hamlReader('video','App');

 
    }

    

}
