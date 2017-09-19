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

    function test(UserModel $model){

        $user = $model->find(1);

        echo $user;

    }

    function b(){

        $a = LocalConfig::get('HOOK_CLASS');
        $s = class_exists($a);
        $g = method_exists($a,'ajaxCallback');
        $a::ajaxCallback();
        AJAX::error();
    }


    # 更新代码
    function pull(){

        system("cd ".BASE_ROOT." && \"C:\Program Files\Git\git-cmd.exe\" git pull");
    }

    # 测试websocket
    function testSock(){

        View::hamlReader('Test/sock','App');
    }

}