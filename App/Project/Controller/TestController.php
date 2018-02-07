<?php

namespace App\Project\Controller;

use Controller;

use Uccu\DmcatTool\Tool\AJAX;

use Uccu\DmcatHttp\Request;
use Uccu\DmcatHttp\Route;
use App\Project\Model\UserModel;
use App\Project\Model\LessionModel as Lession;
use Model;
use Uccu\DmcatTool\Tool\E;
use Uccu\DmcatTool\Tool\LocalConfig;
use App\Car\Tool\Func;
use App\Car\Model\ErrorApiModel;
use App\Car\Model\SuccessApiModel;


use View;


class TestController extends Controller{


    function __construct(){

        // $get = Request->get;

        // var_dump($get);

    }



    function main($cc){

        echo 'ok';
 
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

        system("cd ".BASE_ROOT." && git pull");
    }

    # 测试websocket
    function testSock(){

        View::hamlReader('Test/sock','App');
    }


    function c(){

        $out = Func::getDistance('37.22','118.02',31.15,121.10);
        AJAX::success($out);

    }


    function getSuccessReq($id,SuccessApiModel $model,Request $request){

        $log = $model->find($id);
        if(!$log)AJAX::error('没有数据');

        // $req = json_decode($log->request);
        // $request->request = $req;

        

    }


    function re_ws(){

        echo '<pre>'; 
        $last_line = system('/home/app/code/dmcat/restart_ws', $retval); 
        echo ' 
        </pre> 
        <hr />Last line of the output: ' . $last_line . ' 
        <hr />Return value: ' . $retval; 

    }
}