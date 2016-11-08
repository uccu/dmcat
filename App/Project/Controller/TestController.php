<?php

namespace App\Project\Controller;

use Controller;

use AJAX;


use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;

class TestController extends Controller{


    function __construct(){



        $var = Lession::new();

       
        $g = $var->get();

        $r = $g->find(0)->save();

        echo $g;


    }










}