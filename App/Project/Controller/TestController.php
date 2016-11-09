<?php

namespace App\Project\Controller;

use Controller;

use AJAX;


use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;

class TestController extends Controller{


    function __construct(){

        // $var = Lession::new();

        // $g = $var->where('%F = %d AND %F = %n','id','1','name','1')->order('id desc')->get('id');

        // $r = $g->find(1);

        //echo $g;


    }



    function main(){

        
        
        call_user_func_array(array($this,'getLessionById'),['id'=>2]);

    }


    function getLessionById($name = null,$id = null){

        var_dump(func_get_args());

        echo Lession::new()->find($id);

    }





}