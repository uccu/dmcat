<?php

namespace App\Project\Controller;

use Controller;

use View;
use App\Resource\Tool\Func;

class IndexController extends Controller{


    function __construct(){
        
        Func::visit_log();
        View::hamlReader('index','App');
        
    }




    

}
