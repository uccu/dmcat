<?php

namespace App\Project\Controller;

use Controller;

use Uccu\DmcatTool\Tool\AJAX;

use Request;
use Route;
use App\Project\Model\UserModel;
use App\Project\Model\LessionModel as Lession;
use Model;
use Uccu\DmcatTool\Tool\E;
use Uccu\DmcatTool\Tool\LocalConfig;



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

        // AJAX::success(['user'=>$user]);
        
        echo basename(__CLASS__);

    }

    function b(){

        $a = 'Lib\Tool\Hook';
        // $a::ajaxCallback();
        $s = class_exists($a);
        $g = method_exists('','ajaxCallback');
        var_dump($g,$a);
    }



}