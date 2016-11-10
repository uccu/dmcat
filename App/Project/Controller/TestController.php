<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;

use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;

class TestController extends Controller{


    function __construct(){

        // $get = Request::get();

        // var_dump($get);

    }



    function main(Request $request ,Lession $lession ,$baka = 1){

        // var_dump( $request );

        // var_dump( $lession );
        
 
    }


    function getLessionById($name = null,$id = null){

        //var_dump(func_get_args());
        echo '123';

        echo Lession::news()->find($id);

    }





}