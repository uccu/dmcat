<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;
use Route;
use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;
use Model;




use View;


class TestController extends Controller{


    function __construct(){

        // $get = Request->get;

        // var_dump($get);

    }



    function main($cc){

        $cc = Request::getInstance()->get('cc','s');
        var_dump($cc);
 
    }

    function ec(){

        
        
        echo 'ok';
        
       

        

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



}