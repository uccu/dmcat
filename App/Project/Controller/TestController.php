<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;
use Route;
use App\Project\Model\UserModel;
use App\Project\Model\LessionModel as Lession;
use Model;




use View;


class TestController extends Controller{


    function __construct(){

        // $get = Request->get;

        // var_dump($get);

    }



    function main($cc){

        $cc = Request::getSingleInstance()->get('cc','s');
        var_dump($cc);
 
    }

    function ec(UserModel $model){

        // $model = UserModel::copyMutiInstance();
        
        echo $model->select('friendsTable.friend_id')->find();
        
       

        

    }


    function getLessionById($name = null,$id = null){

        //var_dump(func_get_args());
        //echo '123';

        echo Lession::copyMutiInstance()->where('id=%d',1)->get();

    }

    function haml(){

        View::addData(['g'=>['title'=>'zz','keywords'=>'baka']]);

        View::hamlReader('Test/my','App');


    }

    function tes($e = null){

        // if(!isset($e))echo 1;

        $model = UserModel::copyMutiInstance();

        $user = $model->find(null);

        var_dump($model);
        
        echo basename(__CLASS__);

    }

    function b(){

        echo $ss;

    }



}