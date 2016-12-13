<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;

use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;
use Model;
use App\Resource\Model\ResourceNameSharp as RNS;



use View;


class TestController extends Controller{


    function __construct(){
       
       
        $z = new RNS('【西農YUI漢化組】★十月新番【Stella no Mahou 斯特拉的魔法】第11話 BIG5繁體 720P MP4');

        var_dump($z);
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

        View::hamlReader('Test/my','App');


    }



}