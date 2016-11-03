<?php

namespace App\Project\Controller;

use Controller;

use AJAX;


use App\Project\Model\UserModel as User;

class TestController extends Controller{


    function __construct(){

        
        
        $u = table('Lib/Model/Using');


        //$e = $u->query('insert into `user` set name ="123"');


        $var = new User;
        

        $var->select('id','user2.ww')->page(3,10)->get();
       
      
    }








}