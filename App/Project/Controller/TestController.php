<?php

namespace App\Project\Controller;

use Controller;

use AJAX;


use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;

class TestController extends Controller{


    function __construct(){
        echo microtime().'<br>';
        


        echo microtime().'<br>';
        //$e = $u->query('insert into `user` set name ="123"');


        $var = Lession::new();
        echo microtime().'<br>';

        //$var->select('id','id')->find(1);


       
        $g = $var->select('user.*','uid2','count')->where('user.id = 2')->get();


        echo '<br>';
        echo microtime().'<br>';

        //var_dump($g);
    }










}