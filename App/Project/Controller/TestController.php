<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;

use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;
use Model;




use View;


class TestController extends Controller{


    function __construct(){

        // $get = Request->get;

        // var_dump($get);

    }



    function main(Request $request ,Lession $lession ,$baka = 1){

        // var_dump( $request );

        // var_dump( $lession );
        
 
    }

    function ec(Model $user){

        $z = $user->where([['%F=%d','id',1]])->get();
        
        echo $z;
        
       

        

    }


    function getLessionById($name = null,$id = null){

        //var_dump(func_get_args());
        //echo '123';

        echo Lession::getInstance()->where('id=%d',1)->get();

    }

    function haml(){

        View::addData(['g'=>['title'=>'zz','keywords'=>'baka']]);

        View::hamlReader('Test/my','App');


    }


    function t(){

        


    }


}