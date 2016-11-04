<?php

namespace App\Project\Controller;

use Controller;

use AJAX;


use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;

class TestController extends Controller{


    function __construct(){

        
        
        $u = table('Lib/Model/Using');


        //$e = $u->query('insert into `user` set name ="123"');


        $var = new Lession;
        

        $var->select('id','id')->find(1);
       
        
    }








}